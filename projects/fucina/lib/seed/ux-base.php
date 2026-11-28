<?php
/** UX basics: what user experience is, users and needs, the design process, usability heuristics, microcopy, affordances and mental models, accessibility. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uxbase_diamond')) {
    /** The Double Diamond: four triangles that open and close; a dot travels slowly through the phases. */
    function fig_uxbase_diamond(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Scoprire', 'Definire', 'Sviluppare', 'Consegnare', 'Il problema giusto', 'La soluzione giusta', 'apre', 'chiude']
            : ['Discover', 'Define', 'Develop', 'Deliver', 'The right problem', 'The right solution', 'opens', 'closes'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.dot{animation:go 10s ease-in-out infinite}@keyframes go{0%{transform:translateX(0)}45%,55%{transform:translateX(290px)}100%{transform:translateX(580px)}}@media (prefers-reduced-motion:reduce){.dot{animation:none}}</style>
<text x="175" y="26" text-anchor="middle" font-weight="700">{$t[4]}</text>
<text x="465" y="26" text-anchor="middle" font-weight="700">{$t[5]}</text>
<path d="M30 140 L175 50 L175 230 Z" fill="#FFF3EA"/>
<path d="M175 50 L320 140 L175 230 Z" fill="#FFE8D6"/>
<path d="M320 140 L465 50 L465 230 Z" fill="#DCEBFF"/>
<path d="M465 50 L610 140 L465 230 Z" fill="#D5F6EA"/>
<path d="M30 140 L175 50 L320 140 L175 230 Z M320 140 L465 50 L610 140 L465 230 Z" fill="none" stroke="#1E1C52" stroke-width="2" stroke-linejoin="round"/>
<line x1="175" y1="50" x2="175" y2="230" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 5"/>
<line x1="465" y1="50" x2="465" y2="230" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 5"/>
<g font-weight="700" text-anchor="middle">
<text x="122" y="145" fill="#B84508">{$t[0]}</text>
<text x="228" y="145" fill="#B84508">{$t[1]}</text>
<text x="412" y="145" fill="#1B66BC">{$t[2]}</text>
<text x="518" y="145" fill="#0B7F5C">{$t[3]}</text>
</g>
<g font-size="13" fill="#5A5880" text-anchor="middle">
<text x="102" y="262">&lt; {$t[6]}</text><text x="248" y="262">{$t[7]} &gt;</text>
<text x="392" y="262">&lt; {$t[6]}</text><text x="538" y="262">{$t[7]} &gt;</text>
</g>
<circle class="dot" cx="30" cy="140" r="8" fill="#E8590C" stroke="#FFFFFF" stroke-width="2"/>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxbase_five')) {
    /** Share of usability problems found as more users are tested (Nielsen and Landauer’s model, about 31% per user). */
    function fig_uxbase_five(string $lg): string
    {
        $t = $lg === 'it'
            ? ['problemi trovati', 'utenti nel test', '5 utenti ≈ 85%', 'poi pochi problemi nuovi a ogni utente']
            : ['problems found', 'users in the test', '5 users ≈ 85%', 'then few new problems with each user'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g stroke="#E4E2F0" stroke-width="1"><line x1="80" y1="40" x2="600" y2="40"/><line x1="80" y1="90" x2="600" y2="90"/><line x1="80" y1="140" x2="600" y2="140"/><line x1="80" y1="190" x2="600" y2="190"/></g>
<line x1="80" y1="240" x2="606" y2="240" stroke="#1E1C52" stroke-width="2"/>
<line x1="80" y1="34" x2="80" y2="240" stroke="#1E1C52" stroke-width="2"/>
<g text-anchor="end" fill="#5A5880"><text x="72" y="244">0%</text><text x="72" y="144">50%</text><text x="72" y="44">100%</text></g>
<g text-anchor="middle" fill="#5A5880"><text x="80" y="258">0</text><text x="253.3" y="258">5</text><text x="426.7" y="258">10</text><text x="600" y="258">15</text></g>
<text x="340" y="286" text-anchor="middle" font-size="15" font-weight="700">{$t[1]}</text>
<text x="20" y="140" text-anchor="middle" font-size="15" font-weight="700" transform="rotate(-90 20 140)">{$t[0]}</text>
<path d="M253.3 240 V71.3 H80" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="6 5"/>
<path d="M80,240 L114.7,178 L149.3,135.2 L184,105.7 L218.7,85.3 L253.3,71.3 L288,61.6 L322.7,54.9 L357.3,50.3 L392,47.1 L426.7,44.9 L461.3,43.4 L496,42.3 L530.7,41.6 L565.3,41.1 L600,40.8" fill="none" stroke="#2F8CF0" stroke-width="3" stroke-linejoin="round"/>
<circle cx="253.3" cy="71.3" r="7" fill="#E8590C" stroke="#FFFFFF" stroke-width="2"/>
<rect x="268" y="84" width="150" height="30" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="343" y="104" text-anchor="middle" font-size="15" font-weight="700" fill="#B84508">{$t[2]}</text>
<text x="600" y="72" text-anchor="end" font-size="14" fill="#1B66BC">{$t[3]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxbase_signifiers')) {
    /** Two doors (a handle says pull, a plate says push) and the same idea on screen. */
    function fig_uxbase_signifiers(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Maniglia: «tira»', 'Piastra: «spingi»', 'Sullo schermo', 'Salva', 'Un pulsante', 'Un link', 'Testo o pulsante?', 'Apri il profilo']
            : ['Handle: “pull”', 'Plate: “push”', 'On screen', 'Save', 'A button', 'A link', 'Text or button?', 'Open your profile'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="30" y="30" width="120" height="200" rx="10" fill="#F1F0F8" stroke="#1E1C52" stroke-width="2"/>
<rect x="124" y="100" width="10" height="60" rx="5" fill="#E8590C"/>
<rect x="180" y="30" width="120" height="200" rx="10" fill="#F1F0F8" stroke="#1E1C52" stroke-width="2"/>
<rect x="268" y="106" width="20" height="48" rx="4" fill="#2F8CF0"/>
<text x="90" y="256" text-anchor="middle" font-weight="700">{$t[0]}</text>
<text x="240" y="256" text-anchor="middle" font-weight="700">{$t[1]}</text>
<line x1="325" y1="20" x2="325" y2="270" stroke="#E4E2F0" stroke-width="2"/>
<text x="480" y="38" text-anchor="middle" font-size="15" font-weight="700">{$t[2]}</text>
<rect x="360" y="62" width="110" height="44" rx="10" fill="#E8590C"/>
<text x="415" y="90" text-anchor="middle" font-size="15" font-weight="700" fill="#FFFFFF">{$t[3]}</text>
<path d="M452 94 l0 22 l6 -5 l4 9 l4 -2 l-4 -9 l8 0 z" fill="#FFFFFF" stroke="#1E1C52" stroke-width="1.5" stroke-linejoin="round"/>
<text x="490" y="90" fill="#0B7F5C" font-weight="700">✓ {$t[4]}</text>
<text x="360" y="156" fill="#1B66BC" font-size="15" text-decoration="underline">{$t[7]}</text>
<text x="510" y="156" fill="#0B7F5C" font-weight="700">✓ {$t[5]}</text>
<text x="360" y="216" font-size="15">{$t[3]}</text>
<text x="420" y="216" fill="#C2255C" font-weight="700">? {$t[6]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxbase_target')) {
    /** WCAG 2.2 target size: small targets need a 24 px circle that touches no other target; 24 px targets pass anyway. */
    function fig_uxbase_target(string $lg): string
    {
        $t = $lg === 'it'
            ? ['16 px, troppo vicini', '16 px, ben distanziati', '24 px', '✗ i cerchi si toccano', '✓ cerchi separati', '✓ abbastanza grandi', 'cerchio di 24 px attorno a ogni bersaglio (disegno ingrandito ×3)']
            : ['16 px, too close', '16 px, well spaced', '24 px', '✗ circles overlap', '✓ circles apart', '✓ big enough', '24 px circle around each target (drawing enlarged ×3)'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g text-anchor="middle" font-weight="700"><text x="110" y="30">{$t[0]}</text><text x="320" y="30">{$t[1]}</text><text x="530" y="30">{$t[2]}</text></g>
<g fill="#FFE3EE" fill-opacity="0.6" stroke="#F03E82" stroke-width="2" stroke-dasharray="5 4"><circle cx="80" cy="110" r="36"/><circle cx="140" cy="110" r="36"/></g>
<g fill="#F03E82"><rect x="56" y="86" width="48" height="48" rx="8"/><rect x="116" y="86" width="48" height="48" rx="8"/></g>
<g fill="none" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 4"><circle cx="278" cy="110" r="36"/><circle cx="362" cy="110" r="36"/></g>
<g fill="#0B7F5C"><rect x="254" y="86" width="48" height="48" rx="8"/><rect x="338" y="86" width="48" height="48" rx="8"/></g>
<g fill="#0B7F5C"><rect x="452" y="74" width="72" height="72" rx="10"/><rect x="536" y="74" width="72" height="72" rx="10"/></g>
<g text-anchor="middle" font-weight="700"><text x="110" y="182" fill="#C2255C">{$t[3]}</text><text x="320" y="182" fill="#0B7F5C">{$t[4]}</text><text x="530" y="182" fill="#0B7F5C">{$t[5]}</text></g>
<text x="320" y="228" text-anchor="middle" font-size="13" fill="#5A5880">{$t[6]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'ux-base', 'family' => 'ux', 'after' => null, 'categories' => ['design'],
    'title' => L('UX: le basi', 'UX Basics'), 'short' => L('UX base', 'UX Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Che cos’è la user experience: conoscere gli utenti e i loro bisogni, il processo di design, le euristiche di usabilità e i primi test, il microcopy, affordance e modelli mentali, l’accessibilità fin dall’inizio.', 'What user experience is: knowing users and their needs, the design process, usability heuristics and first tests, microcopy, affordances and mental models, accessibility from the start.'),
    'lessons' => [
        lesson('what', L('Che cos’è la UX', 'What UX is'), [
            P('La **user experience** (UX) è tutto ciò che una persona vive usando un prodotto: se riesce a fare quello che voleva, quanto fatica, che cosa prova. Non è solo l’aspetto grafico (quella è la **UI**, l’interfaccia): una app bellissima in cui non si trova il tasto “paga” ha una pessima UX.', '**User experience** (UX) is everything a person goes through when using a product: whether they manage to do what they wanted, how much effort it takes, how they feel. It is not just the visuals (that is the **UI**, the interface): a beautiful app where you cannot find the “pay” button has terrible UX.'),
            P('Una buona esperienza è **utile** (risolve un problema vero), **usabile** (facile da imparare ed efficiente), **accessibile** (per tutti, anche con disabilità) e **piacevole**. Il principio guida è il **design centrato sull’utente**: si parte dalle persone, non dalle funzioni che vogliamo costruire.', 'A good experience is **useful** (solves a real problem), **usable** (easy to learn and efficient), **accessible** (for everyone, including people with disabilities) and **pleasant**. The guiding principle is **user-centred design**: you start from people, not from the features we want to build.'),
            Tip('Tu non sei il tuo utente: chi progetta conosce già il prodotto e lo usa in modo diverso da chi lo vede per la prima volta.', 'You are not your user: whoever designs already knows the product and uses it differently from someone seeing it for the first time.'),
        ], [
            Q('w1', L('Che differenza c’è tra UX e UI?', 'What is the difference between UX and UI?'), [L('La UX è l’esperienza intera, la UI l’interfaccia', 'UX is the whole experience, UI the interface'), L('Sono la stessa cosa con due nomi', 'They are the same thing with two names'), L('La UI è l’esperienza, la UX la grafica', 'UI is the experience, UX the graphics'), L('La UX riguarda solo i colori', 'UX is only about colours')], 0, L('La UI è una parte della UX.', 'UI is one part of UX.')),
            Q('w2', L('Che cosa significa design centrato sull’utente?', 'What does user-centred design mean?'), [L('Partire dai bisogni delle persone', 'Starting from people’s needs'), L('Partire dalle funzioni da costruire', 'Starting from features to build'), L('Copiare i concorrenti', 'Copying competitors'), L('Seguire i gusti del capo', 'Following the boss’s taste')], 0, L('Prima le persone.', 'People first.')),
            Q('w3', L('Quale di queste è una qualità della buona UX?', 'Which of these is a quality of good UX?'), [L('È accessibile a tutti', 'It is accessible to everyone'), L('Ha più animazioni possibili', 'It has as many animations as possible'), L('Ha tutte le funzioni in una schermata', 'It has every feature on one screen'), L('Usa solo il colore preferito del team', 'It uses only the team’s favourite colour')], 0, L('Utile, usabile, accessibile, piacevole.', 'Useful, usable, accessible, pleasant.')),
        ], task('text', L('Pensa a un’app che usi spesso. Scrivi una cosa che rende buona la sua **UX** e una che la peggiora, su due righe che iniziano con `+` e `-`.', 'Think of an app you use often. Write one thing that makes its **UX** good and one that makes it worse, on two lines starting with `+` and `-`.'), "+ \n- ", [re('^\+\s*\S', L('Un punto positivo', 'A positive point')), re('^-\s*\S', L('Un punto negativo', 'A negative point'))], '', "+ pago con un tocco grazie alla carta salvata\n- le impostazioni delle notifiche sono nascoste in tre menu"), [
            dMatch(pMatch(), [[L('Utile', 'Useful'), L('Risolve un problema vero', 'Solves a real problem')], [L('Usabile', 'Usable'), L('Facile ed efficiente', 'Easy and efficient')], [L('Accessibile', 'Accessible'), L('Per tutte le persone', 'For all people')], ['UI', L('L’interfaccia visibile', 'The visible interface')]], L('Le parole della UX.', 'The words of UX.')),
            dFlash(pFlash(), [[L('Una app bella può avere una pessima UX', 'A beautiful app can have terrible UX'), true], [L('Chi progetta usa il prodotto come un nuovo utente', 'Designers use the product like a new user'), false], [L('L’accessibilità fa parte della UX', 'Accessibility is part of UX'), true], [L('UX significa solo scegliere i colori', 'UX only means picking colours'), false]], L('Oltre la grafica.', 'Beyond the graphics.')),
        ]),
        lesson('users', L('Utenti, bisogni e personas', 'Users, needs and personas'), [
            P('Per progettare bene bisogna **conoscere gli utenti**: chi sono, che cosa vogliono ottenere (**obiettivi**), che cosa li blocca (**punti di dolore**), in che contesto usano il prodotto (in metro col telefono, alla scrivania, di fretta). Le informazioni arrivano dalla **ricerca**: interviste, osservazione, questionari, dati d’uso.', 'To design well you need to **know your users**: who they are, what they want to achieve (**goals**), what blocks them (**pain points**), in what context they use the product (on the underground with a phone, at a desk, in a hurry). The information comes from **research**: interviews, observation, surveys, usage data.'),
            P('Una **persona** è un ritratto immaginario ma basato sui dati di un gruppo di utenti tipici, con nome, obiettivi, frustrazioni e abitudini. Serve al team per decidere pensando a qualcuno di concreto (“Giulia capirebbe questo menu?”). Il **job to be done** riassume il bisogno: “quando… voglio… così posso…”.', 'A **persona** is an imaginary but data-based portrait of a group of typical users, with a name, goals, frustrations and habits. It helps the team decide while thinking of someone concrete (“would Giulia understand this menu?”). The **job to be done** sums up the need: “when… I want to… so I can…”.'),
            C('Persona: Giulia, 34 anni, infermiera turnista
Obiettivo: prenotare la spesa in 5 minuti tra un turno e l’altro
Dolore: le app chiedono troppi passaggi e dimenticano l’ordine abituale
Job to be done: quando finisco il turno, voglio riordinare la spesa
della settimana scorsa, così posso riposare invece di girare il supermercato', 'text'),
        ], [
            Q('u1', L('Che cos’è una persona nella UX?', 'What is a persona in UX?'), [L('Un ritratto di utente tipico basato sui dati', 'A data-based portrait of a typical user'), L('Un cliente reale con nome e cognome', 'A real customer with full name'), L('Il designer del progetto', 'The project’s designer'), L('Un personaggio dei fumetti', 'A comic book character')], 0, L('Immaginaria, ma fondata.', 'Imaginary, but grounded.')),
            Q('u2', L('Che cos’è un punto di dolore?', 'What is a pain point?'), [L('Un ostacolo che frustra l’utente', 'An obstacle that frustrates the user'), L('Un errore nel codice', 'A bug in the code'), L('Il prezzo del prodotto', 'The product’s price'), L('Una pagina di errore 404', 'A 404 error page')], 0, L('Là c’è un’opportunità.', 'There lies an opportunity.')),
            Q('u3', L('Quale formato descrive un job to be done?', 'Which format describes a job to be done?'), [L('Quando… voglio… così posso…', 'When… I want to… so I can…'), L('Se… allora… altrimenti…', 'If… then… else…'), L('Chi… dove… quando…', 'Who… where… when…'), L('Prima… poi… infine…', 'First… then… finally…')], 0, L('Situazione, motivazione, risultato.', 'Situation, motivation, outcome.')),
        ], task('text', L('Scrivi un job to be done per un’app di parcheggio, nel formato `Quando ..., voglio ..., così posso ...` (o in inglese `When ..., I want ..., so I can ...`).', 'Write a job to be done for a parking app, in the format `When ..., I want ..., so I can ...`.'), '', [re('^(quando|when)\b', L('Inizia dalla situazione', 'Starts from the situation')), re('(voglio|i want)', L('C’è la motivazione', 'There is the motivation')), re('(così posso|cosi posso|so i can)', L('C’è il risultato', 'There is the outcome'))], '', 'Quando arrivo in centro in ritardo, voglio pagare la sosta dal telefono, così posso correre all’appuntamento senza cercare il parchimetro.'), [
            dMatch(pMatch(), [[L('Obiettivo', 'Goal'), L('Che cosa vuole ottenere', 'What they want to achieve')], [L('Punto di dolore', 'Pain point'), L('Che cosa lo blocca', 'What blocks them')], [L('Contesto', 'Context'), L('Dove e come usa il prodotto', 'Where and how they use it')], ['Persona', L('Ritratto di utente tipico', 'Typical user portrait')]], L('Capire chi usa il prodotto.', 'Understanding who uses the product.')),
            dFlash(pFlash(), [[L('Le personas vanno basate sulla ricerca', 'Personas should be based on research'), true], [L('Una persona è sempre un cliente reale', 'A persona is always a real customer'), false], [L('Il contesto d’uso cambia il design', 'The context of use changes the design'), true], [L('La ricerca sugli utenti si fa solo a progetto finito', 'User research is done only when the project is finished'), false]], L('Prima di disegnare.', 'Before drawing.')),
        ]),
        lesson('process', L('Il processo di design', 'The design process'), [
            P('Un modello molto usato è il **Double Diamond**: due rombi, ognuno con una fase che **apre** (esplorare, raccogliere idee) e una che **chiude** (scegliere). Primo rombo: **scoprire** il problema e **definirlo**. Secondo rombo: **sviluppare** soluzioni e **consegnarne** una. In breve: fare la cosa giusta, poi farla nel modo giusto.', 'A widely used model is the **Double Diamond**: two diamonds, each with a phase that **opens** (exploring, gathering ideas) and one that **closes** (choosing). First diamond: **discover** the problem and **define** it. Second diamond: **develop** solutions and **deliver** one. In short: do the right thing, then do it the right way.'),
            Fig(L(fig_uxbase_diamond('it'), fig_uxbase_diamond('en')),
                L('Due rombi affiancati che si toccano al centro. Il primo, sul problema, è diviso in «scoprire», che si allarga, e «definire», che si restringe; il secondo, sulla soluzione, in «sviluppare» e «consegnare». Un punto arancione li percorre da sinistra a destra.', 'Two diamonds side by side that touch in the middle. The first, about the problem, is split into “discover”, which widens, and “define”, which narrows; the second, about the solution, into “develop” and “deliver”. An orange dot travels through them from left to right.'),
                L('Ogni rombo prima si allarga (esplorare), poi si stringe (scegliere).', 'Each diamond first widens (exploring), then narrows (choosing).')),
            P('Il lavoro è **iterativo**: si passa dagli schizzi ai **wireframe** (schemi in bianco e nero della struttura), ai **prototipi** cliccabili, si testa con utenti veri, si corregge e si ripete. Sbagliare su carta costa pochi minuti, sbagliare nel codice rilasciato costa settimane.', 'The work is **iterative**: you go from sketches to **wireframes** (black-and-white structure layouts), to clickable **prototypes**, test with real users, fix and repeat. A mistake on paper costs a few minutes, a mistake in released code costs weeks.'),
        ], [
            Q('d1', L('Quali sono le quattro fasi del Double Diamond?', 'What are the four phases of the Double Diamond?'), [L('Scoprire, definire, sviluppare, consegnare', 'Discover, define, develop, deliver'), L('Disegnare, colorare, stampare, vendere', 'Draw, colour, print, sell'), L('Codificare, testare, rilasciare, chiudere', 'Code, test, release, close'), L('Ideare, votare, copiare, pubblicare', 'Ideate, vote, copy, publish')], 0, L('Due rombi, quattro fasi.', 'Two diamonds, four phases.')),
            Q('d2', L('Che cos’è un wireframe?', 'What is a wireframe?'), [L('Uno schema semplice della struttura', 'A simple layout of the structure'), L('La versione finale colorata', 'The final coloured version'), L('Il codice della pagina', 'The page’s code'), L('Un cavo di rete', 'A network cable')], 0, L('Struttura prima della grafica.', 'Structure before visuals.')),
            Q('d3', L('Perché il design è iterativo?', 'Why is design iterative?'), [L('Testare e correggere presto costa meno', 'Testing and fixing early costs less'), L('Per allungare i tempi', 'To stretch the timeline'), L('Perché i designer cambiano idea a caso', 'Because designers change their minds randomly'), L('Non lo è', 'It is not')], 0, L('Errori su carta, non nel codice.', 'Mistakes on paper, not in code.')),
        ], task('text', L('Scrivi le quattro fasi del Double Diamond in ordine, una per riga numerate da `1.` a `4.`.', 'Write the four Double Diamond phases in order, one per line numbered `1.` to `4.`.'), "1. \n2. \n3. \n4. ", [re('^1\.\s*(scopr|discover)', L('Scoprire', 'Discover')), re('^2\.\s*(defin)', L('Definire', 'Define')), re('^3\.\s*(svilupp|develop)', L('Sviluppare', 'Develop')), re('^4\.\s*(consegn|deliver)', L('Consegnare', 'Deliver'))], '', "1. scoprire\n2. definire\n3. sviluppare\n4. consegnare"), [
            dOrder(pSort(), [L('Schizzo su carta', 'Paper sketch'), 'Wireframe', L('Prototipo cliccabile', 'Clickable prototype'), L('Test con utenti', 'User test'), L('Correzioni e nuova versione', 'Fixes and new version')], L('Dalla bozza al test.', 'From draft to test.')),
            dMatch(pMatch(), [[L('Scoprire', 'Discover'), L('Esplorare il problema', 'Explore the problem')], [L('Definire', 'Define'), L('Scegliere il problema giusto', 'Choose the right problem')], [L('Sviluppare', 'Develop'), L('Esplorare soluzioni', 'Explore solutions')], [L('Consegnare', 'Deliver'), L('Rifinire e rilasciare', 'Refine and release')]], L('Il Double Diamond.', 'The Double Diamond.')),
        ]),
        lesson('heuristics', L('Euristiche di usabilità', 'Usability heuristics'), [
            P('Le **10 euristiche di Jakob Nielsen** sono regole pratiche per valutare un’interfaccia. Le più importanti: mostra lo **stato del sistema** (caricamento, salvataggio riuscito), parla la **lingua dell’utente**, dai **controllo e libertà** (annulla, indietro), sii **coerente**, **previeni gli errori**, fai **riconoscere invece di ricordare**, aiuta a **capire e correggere gli errori**.', 'Jakob Nielsen’s **10 heuristics** are practical rules for evaluating an interface. The most important: show **system status** (loading, saved successfully), speak the **user’s language**, give **control and freedom** (undo, back), be **consistent**, **prevent errors**, favour **recognition over recall**, help users **understand and recover from errors**.'),
            C('Messaggio cattivo:  "Errore 0x80070057"
Messaggio buono:    "La data di nascita non è valida: usa il formato gg/mm/aaaa"', 'text'),
            P('Un primo **test di usabilità** è semplice: dai a 5 persone un compito realistico (“prenota un tavolo per sabato”), osservale senza aiutarle e chiedi di pensare ad alta voce. Con 5 utenti emergono già gran parte dei problemi più gravi.', 'A first **usability test** is simple: give 5 people a realistic task (“book a table for Saturday”), watch them without helping and ask them to think aloud. With 5 users most of the worst problems already emerge.'),
            Fig(L(fig_uxbase_five('it'), fig_uxbase_five('en')),
                L('Un grafico: in orizzontale il numero di utenti nel test, da 0 a 15; in verticale la percentuale di problemi trovati. La curva sale ripida all’inizio, arriva a circa l’85% con 5 utenti e poi si appiattisce vicino al 100%.', 'A chart: horizontally the number of users in the test, from 0 to 15; vertically the share of problems found. The curve rises steeply at first, reaches about 85% with 5 users and then flattens out near 100%.'),
                L('Modello di Nielsen e Landauer: ogni utente scopre circa il 31% dei problemi. Meglio tre test da 5 utenti che uno da 15.', 'Nielsen and Landauer’s model: each user uncovers about 31% of the problems. Three tests with 5 users beat one with 15.')),
        ], [
            Q('h1', L('Una barra di avanzamento durante il caricamento applica quale euristica?', 'A progress bar while loading applies which heuristic?'), [L('Visibilità dello stato del sistema', 'Visibility of system status'), L('Estetica minimalista', 'Minimalist aesthetics'), L('Flessibilità d’uso', 'Flexibility of use'), L('Documentazione', 'Documentation')], 0, L('L’utente sa che cosa succede.', 'The user knows what is happening.')),
            Q('h2', L('Che cosa significa “riconoscere invece di ricordare”?', 'What does “recognition over recall” mean?'), [L('Mostrare le opzioni invece di farle ricordare', 'Showing options instead of making users remember them'), L('Ricordare la password dell’utente', 'Remembering the user’s password'), L('Riconoscere il volto dell’utente', 'Recognising the user’s face'), L('Usare un tutorial lungo', 'Using a long tutorial')], 0, L('Meno carico sulla memoria.', 'Less load on memory.')),
            Q('h3', L('Quanti utenti bastano per un primo test che trovi molti problemi?', 'How many users are enough for a first test that finds many problems?'), [L('Circa 5', 'About 5'), L('Almeno 500', 'At least 500'), L('Nessuno, basta il team', 'None, the team is enough'), L('Esattamente 1', 'Exactly 1')], 0, L('Pochi, ma ripetuti.', 'Few, but repeated.')),
        ], task('text', L('Riscrivi questo messaggio d’errore in modo utile: “Errore: input non valido”. Il campo è il **CAP** e deve avere **5 cifre**.', 'Rewrite this error message in a helpful way: “Error: invalid input”. The field is the **postcode** and must have **5 digits**.'), '', [re('cap|postcode|postal|codice postale', L('Dice quale campo', 'Says which field')), re('5|cinque|five', L('Dice come correggere', 'Says how to fix it'))], '', 'Il CAP deve avere 5 cifre, per esempio 33100.'), [
            dMatch(pMatch(), [[L('Stato del sistema', 'System status'), L('Barra di caricamento', 'Loading bar')], [L('Controllo e libertà', 'Control and freedom'), L('Tasto Annulla', 'Undo button')], [L('Prevenire gli errori', 'Error prevention'), L('Conferma prima di eliminare', 'Confirm before deleting')], [L('Coerenza', 'Consistency'), L('Stesse icone, stessi significati', 'Same icons, same meanings')]], L('Euristiche in pratica.', 'Heuristics in practice.')),
            dFlash(pFlash(), [[L('Un messaggio d’errore deve dire come correggere', 'An error message should say how to fix it'), true], [L('Durante un test si aiuta subito l’utente in difficoltà', 'During a test you help a struggling user at once'), false], [L('Il tasto Annulla dà controllo all’utente', 'The Undo button gives the user control'), true], [L('Un codice come 0x80070057 è chiaro per tutti', 'A code like 0x80070057 is clear to everyone'), false]], L('Interfacce comprensibili.', 'Understandable interfaces.')),
        ]),
        lesson('writing', L('UX writing e microcopy', 'UX writing and microcopy'), [
            P('Il **microcopy** sono le parole piccole dell’interfaccia: etichette dei pulsanti, messaggi d’errore, testi vuoti, conferme. Un pulsante dice **cosa succede** (“Salva la bozza”, non “OK”); un errore dice **cosa è successo e come rimediare**, senza dare la colpa all’utente.', '**Microcopy** is the small text of an interface: button labels, error messages, empty states, confirmations. A button says **what happens** (“Save draft”, not “OK”); an error says **what happened and how to fix it**, without blaming the user.'),
            C('No:  Errore 422. Input non valido.
Sì:  La data di nascita deve essere nel passato. Controlla l’anno.

No:  Nessun dato.
Sì:  Non hai ancora corsi salvati. Tocca il segnalibro su un corso per ritrovarlo qui.', 'text'),
            P('Scrivi corto, con parole comuni, sempre uguali per la stessa cosa (non “Elimina” in un punto e “Cancella” in un altro). Un **tono** coerente fa sembrare il prodotto una persona sola.', 'Write short, with common words, always the same for the same thing (not “Delete” in one place and “Remove” in another). A consistent **tone** makes the product sound like one person.'),
        ], [
            Q('wr1', L('Quale etichetta di pulsante è migliore?', 'Which button label is better?'), [L('Salva la bozza', 'Save draft'), 'OK', L('Invia dati', 'Submit data'), L('Clicca qui', 'Click here')], 0, L('Dice cosa succede.', 'It says what happens.')),
            Q('wr2', L('Che cosa deve dire un messaggio d’errore?', 'What should an error message say?'), [L('Cosa è successo e come rimediare', 'What happened and how to fix it'), L('Il codice tecnico soltanto', 'Only the technical code'), L('Che l’utente ha sbagliato', 'That the user was wrong'), L('Niente', 'Nothing')], 0, L('Senza colpe.', 'Without blame.')),
            Q('wr3', L('Perché usare sempre le stesse parole?', 'Why always use the same words?'), [L('Per non confondere le persone', 'So people are not confused'), L('Per risparmiare spazio', 'To save space'), L('Per la SEO', 'For SEO'), L('Non serve', 'It is not needed')], 0, L('Coerenza = fiducia.', 'Consistency = trust.')),
        ], task('text', L('Riscrivi l’errore `Input non valido` in una frase che dica cosa fare, iniziando con `Il`, `La` o `Scrivi`.', 'Rewrite the error `Invalid input` as a sentence saying what to do, starting with `The`, `Enter` or `Write`.'), '', [re('^(Il|La|Lo|Scrivi|Inserisci|Usa|The|Enter|Write|Use|Your)\s+\S.{15,}', L('Una frase utile', 'A helpful sentence'))], '', 'La password deve avere almeno 8 caratteri: aggiungine qualcuno.'), [
            dMatch(pMatch(), [[L('Pulsante', 'Button'), L('Dice l’azione', 'States the action')], [L('Errore', 'Error'), L('Causa e rimedio', 'Cause and fix')], [L('Stato vuoto', 'Empty state'), L('Spiega come iniziare', 'Explains how to start')], [L('Tono', 'Tone'), L('Una voce coerente', 'One consistent voice')]], L('Le parole dell’interfaccia.', 'Interface words.')),
            dFlash(pFlash(), [[L('Un buon errore spiega come rimediare', 'A good error explains how to fix it'), true], [L('“OK” è sempre l’etichetta migliore', '“OK” is always the best label'), false], [L('Lo stato vuoto può guidare il primo passo', 'An empty state can guide the first step'), true], [L('Usare sinonimi diversi aiuta a capire', 'Using different synonyms helps understanding'), false]], L('Scrivere per l’interfaccia.', 'Writing for the interface.')),
        ]),
        lesson('norman', L('Affordance, segnali e modelli mentali', 'Affordances, signifiers and mental models'), [
            P('Don Norman, nel libro *La caffettiera del masochista* (*The Design of Everyday Things*), spiega perché gli oggetti ci confondono. Un’**affordance** è ciò che un oggetto permette di fare (una maniglia permette di tirare); un **segnale** (signifier) è l’indizio che lo comunica (una maniglia verticale dice «tira», una piastra piatta dice «spingi»). Sullo schermo i segnali sono il cursore a manina, il testo sottolineato di un link, la forma di un pulsante.', 'Don Norman, in *The Design of Everyday Things*, explains why objects confuse us. An **affordance** is what an object allows you to do (a handle allows pulling); a **signifier** is the clue that communicates it (a vertical handle says “pull”, a flat plate says “push”). On screen, signifiers are the hand cursor, the underlined text of a link, the shape of a button.'),
            Fig(L(fig_uxbase_signifiers('it'), fig_uxbase_signifiers('en')),
                L('A sinistra due porte: una con una maniglia verticale, che invita a tirare, e una con una piastra piatta, che invita a spingere. A destra gli stessi indizi sullo schermo: un pulsante pieno con il cursore a mano, un link sottolineato e una parola «Salva» senza forma, che non si capisce se si possa premere.', 'On the left two doors: one with a vertical handle, inviting a pull, and one with a flat plate, inviting a push. On the right the same clues on screen: a filled button with the hand cursor, an underlined link and a plain word “Save” with no shape, which may or may not be pressable.'),
                L('Il significante dice che cosa si può fare: senza, l’utente tira a indovinare.', 'The signifier says what you can do: without one, the user has to guess.')),
            P('Ognuno ha un **modello mentale**, un’idea di come funziona il sistema: se l’interfaccia lo rispetta (il cestino che si svuota, il carrello che si riempie) si usa senza istruzioni. Norman parla anche di **mappatura** (comandi disposti come ciò che controllano, come le manopole dei fornelli) e di **feedback** (ogni azione ha una risposta visibile). Quando qualcuno sbaglia, il problema è quasi sempre il design, non la persona.', 'Everyone has a **mental model**, an idea of how the system works: if the interface respects it (the bin you empty, the cart that fills up) it can be used without instructions. Norman also talks about **mapping** (controls laid out like what they control, like hob knobs) and **feedback** (every action has a visible response). When someone makes a mistake, the problem is almost always the design, not the person.'),
            Tip('Se un pulsante ha bisogno di un cartello «premi qui», il suo segnale non basta.', 'If a button needs a “press here” sign, its signifier is not enough.'),
        ], [
            Q('n1', L('Che cos’è un segnale (signifier)?', 'What is a signifier?'), [L('Un oggetto in 3D', 'A 3D object'), L('L’indizio che comunica come usare qualcosa', 'The clue that communicates how to use something'), L('Un messaggio d’errore del sistema', 'A system error message'), L('La firma del designer sul progetto', 'The designer’s signature on the project')], 1, L('Dice «tira», «tocca», «trascina».', 'It says “pull”, “tap”, “drag”.')),
            Q('n2', L('Una porta ha una maniglia da tirare, ma va spinta. Che problema ha?', 'A door has a pull handle, but must be pushed. What is wrong with it?'), [L('Il feedback arriva troppo tardi', 'The feedback arrives too late'), L('Ha troppi colori diversi', 'It has too many different colours'), L('Il segnale suggerisce l’azione sbagliata', 'The signifier suggests the wrong action'), L('Manca un modello mentale del designer', 'The designer lacks a mental model')], 2, L('Si chiama «porta di Norman».', 'It is called a “Norman door”.')),
            Q('n3', L('Che cosa vuol dire mappatura?', 'What does mapping mean?'), [L('Disegnare la mappa del sito', 'Drawing the site map'), L('Tradurre l’interfaccia in più lingue', 'Translating the interface into several languages'), L('Salvare la posizione GPS dell’utente', 'Saving the user’s GPS position'), L('Disporre i comandi come ciò che controllano', 'Laying out controls like what they control')], 3, L('Come le manopole dei fornelli.', 'Like hob knobs.')),
        ], task('text', L('Pensa a un oggetto o a un’app che ti ha confuso. Scrivi due righe: `Problema:` (che cosa non era chiaro) e `Segnale:` (quale indizio visivo avrebbe aiutato).', 'Think of an object or an app that confused you. Write two lines: `Problem:` (what was unclear) and `Signifier:` (which visual clue would have helped).'), "Problema: \nSegnale: ", [re('^(problema|problem)\s*:[ \t]*\S.{5,}', L('Descrivi il problema', 'You describe the problem')), re('^(segnale|signifier|signal)\s*:[ \t]*\S.{5,}', L('Proponi un segnale', 'You suggest a signifier'))], '', "Problema: non capivo se la porta andava spinta o tirata\nSegnale: una piastra piatta sul lato da spingere"), [
            dMatch(pMatch(), [['Affordance', L('Ciò che un oggetto permette di fare', 'What an object allows you to do')], [L('Segnale', 'Signifier'), L('L’indizio che lo comunica', 'The clue that communicates it')], [L('Modello mentale', 'Mental model'), L('Come l’utente crede che funzioni', 'How the user thinks it works')], [L('Mappatura', 'Mapping'), L('Comandi disposti come ciò che controllano', 'Controls laid out like what they control')], ['Feedback', L('Risposta visibile a un’azione', 'Visible response to an action')]], L('Le idee di Don Norman.', 'Don Norman’s ideas.')),
            dFlash(pFlash(), [[L('Un link sottolineato è un segnale', 'An underlined link is a signifier'), true], [L('Se l’utente sbaglia, la colpa è sempre sua', 'If the user errs, it is always their fault'), false], [L('Rispettare il modello mentale riduce le istruzioni', 'Respecting the mental model reduces instructions'), true], [L('Il feedback serve solo quando c’è un errore', 'Feedback is only needed when there is an error'), false]], L('Oggetti che si spiegano da soli.', 'Objects that explain themselves.')),
        ]),
        lesson('accessibility', L('Accessibilità fin dall’inizio', 'Accessibility from the start'), [
            P('L’**accessibilità** permette a tutte le persone di usare un prodotto: chi non vede o vede poco, chi non sente, chi non usa il mouse, chi ha difficoltà cognitive. Il riferimento sono le **WCAG 2.2** del W3C, organizzate in quattro principi, **POUR**: **percepibile**, **utilizzabile** (operable), **comprensibile**, **robusto**. In Europa lo **European Accessibility Act**, applicato dal giugno 2025, rende obbligatoria l’accessibilità per molti servizi digitali, come negozi online e banche.', '**Accessibility** lets everyone use a product: people who are blind or have low vision, who are deaf, who do not use a mouse, who have cognitive difficulties. The reference is the W3C’s **WCAG 2.2**, organised in four principles, **POUR**: **perceivable**, **operable**, **understandable**, **robust**. In Europe the **European Accessibility Act**, applied since June 2025, makes accessibility mandatory for many digital services, such as online shops and banks.'),
            P('Le basi che ogni designer controlla: **contrasto** sufficiente (4,5:1 per il testo normale), testo alternativo per le immagini informative, etichette visibili nei campi, tutto usabile da **tastiera** con il focus visibile, target abbastanza grandi (le WCAG 2.2 chiedono almeno **24×24 px** CSS, oppure abbastanza spazio intorno) e mai informazioni affidate solo al colore. Aggiungere l’accessibilità alla fine costa molto più che progettarla da subito.', 'The basics every designer checks: enough **contrast** (4.5:1 for normal text), alt text for informative images, visible labels on fields, everything usable by **keyboard** with visible focus, large enough targets (WCAG 2.2 asks for at least **24×24** CSS px, or enough space around them) and never information conveyed by colour alone. Adding accessibility at the end costs far more than designing it from the start.'),
            Fig(L(fig_uxbase_target('it'), fig_uxbase_target('en')),
                L('Tre esempi ingranditi. Due bersagli da 16 pixel troppo vicini: i cerchi tratteggiati da 24 pixel attorno a loro si sovrappongono, e non va bene. Gli stessi bersagli più distanziati: i cerchi non si toccano, e va bene. Due bersagli da 24 pixel: vanno bene comunque.', 'Three enlarged examples. Two 16-pixel targets too close together: the dashed 24-pixel circles around them overlap, which fails. The same targets further apart: the circles do not touch, which passes. Two 24-pixel targets: they pass anyway.'),
                L('Un bersaglio piccolo è accettabile solo se ha spazio libero intorno.', 'A small target is acceptable only if it has free space around it.')),
            Tip('Prova la tua pagina solo con la tastiera: se con Tab ti perdi tu, si perde anche chi non usa il mouse.', 'Try your page with the keyboard only: if you get lost with Tab, so does anyone who does not use a mouse.'),
        ], [
            Q('a1', L('Che cosa significa POUR nelle WCAG?', 'What does POUR mean in WCAG?'), [L('Pubblico, ordinato, utile, rapido', 'Public, orderly, useful, rapid'), L('Pagina, oggetto, utente, ruolo', 'Page, object, user, role'), L('Percepibile, utilizzabile, comprensibile, robusto', 'Perceivable, operable, understandable, robust'), L('Prova, osserva, usa, rifai', 'Probe, observe, use, redo')], 2, L('I quattro principi dell’accessibilità.', 'The four principles of accessibility.')),
            Q('a2', L('Che dimensione minima chiedono le WCAG 2.2 (livello AA) per un target?', 'What minimum target size does WCAG 2.2 (level AA) ask for?'), [L('12×12 px CSS', '12×12 CSS px'), L('24×24 px CSS, o spazio intorno', '24×24 CSS px, or space around it'), L('64×64 px CSS', '64×64 CSS px'), L('Nessuna regola sui target', 'No rule on targets')], 1, L('Criterio 2.5.8. Apple e Material chiedono anche di più.', 'Criterion 2.5.8. Apple and Material ask for even more.')),
            Q('a3', L('Quando conviene pensare all’accessibilità?', 'When is it best to think about accessibility?'), [L('Solo dopo il lancio', 'Only after launch'), L('Quando qualcuno si lamenta', 'When someone complains'), L('Solo per i siti della pubblica amministrazione', 'Only for public administration sites'), L('Dall’inizio del progetto', 'From the start of the project')], 3, L('Dopo costa molto di più.', 'Later it costs much more.')),
        ], task('text', L('Scrivi tre controlli di accessibilità da fare su una pagina, uno per riga con `-`, tra cui il **contrasto** e la **tastiera**.', 'Write three accessibility checks to run on a page, one per line with `-`, including **contrast** and the **keyboard**.'), "- \n- \n- ", [re('^-[ \t]*\S.*\n-[ \t]*\S.*\n-[ \t]*\S', L('Tre controlli', 'Three checks')), re('contrast', L('Il contrasto', 'Contrast')), re('tastier|keyboard', L('La tastiera', 'The keyboard'))], '', "- il contrasto del testo è almeno 4,5:1\n- si arriva a ogni pulsante con la tastiera e il focus si vede\n- le immagini informative hanno un testo alternativo"), [
            dMatch(pMatch(), [[L('Percepibile', 'Perceivable'), L('Testo alternativo per le immagini', 'Alt text for images')], [L('Utilizzabile', 'Operable'), L('Tutto funziona da tastiera', 'Everything works by keyboard')], [L('Comprensibile', 'Understandable'), L('Errori spiegati chiaramente', 'Errors explained clearly')], [L('Robusto', 'Robust'), L('Codice letto bene dai lettori di schermo', 'Code read well by screen readers')]], L('POUR in pratica.', 'POUR in practice.')),
            dFlash(pFlash(), [[L('Lo European Accessibility Act riguarda anche i negozi online', 'The European Accessibility Act also covers online shops'), true], [L('L’accessibilità serve solo a chi è cieco', 'Accessibility is only for blind people'), false], [L('Il focus da tastiera deve essere visibile', 'Keyboard focus must be visible'), true], [L('Un campo rosso basta a segnalare un errore', 'A red field is enough to signal an error'), false]], L('Progettare per tutti.', 'Designing for everyone.')),
        ]),
    ],
];
