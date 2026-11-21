<?php
/** Intermediate UX: research methods, information architecture, user flows and journey maps, prototyping and testing, synthesis, surveys and analytics, ideation workshops. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uxint_methods')) {
    /** Research methods on two axes: what people say or do, and why or how many. */
    function fig_uxint_methods(string $lg): string
    {
        $t = $lg === 'it'
            ? ['QUALITATIVA: perché', 'QUANTITATIVA: quanto', 'Che cosa dicono', 'Che cosa fanno', 'Interviste', 'Questionari', 'Osservazione sul campo', 'Test moderati', 'Analytics', 'Test A/B', 'Heatmap']
            : ['QUALITATIVE: why', 'QUANTITATIVE: how many', 'What people say', 'What people do', 'Interviews', 'Surveys', 'Field observation', 'Moderated tests', 'Analytics', 'A/B tests', 'Heatmaps'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="150" y="20" width="230" height="130" rx="12" fill="#ECE6FF"/>
<rect x="390" y="20" width="230" height="130" rx="12" fill="#DCEBFF"/>
<rect x="150" y="160" width="230" height="130" rx="12" fill="#ECE6FF"/>
<rect x="390" y="160" width="230" height="130" rx="12" fill="#DCEBFF"/>
<g font-weight="700" font-size="15" text-anchor="end"><text x="138" y="90">{$t[2]}</text><text x="138" y="230">{$t[3]}</text></g>
<g font-weight="700" font-size="13" text-anchor="middle"><text x="265" y="312" fill="#5F3DC4">{$t[0]}</text><text x="505" y="312" fill="#1B66BC">{$t[1]}</text></g>
<g font-size="14">
<rect x="180" y="45" width="130" height="34" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="245" y="67" text-anchor="middle">{$t[4]}</text>
<rect x="440" y="62" width="130" height="34" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="505" y="84" text-anchor="middle">{$t[5]}</text>
<rect x="250" y="130" width="160" height="34" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="330" y="152" text-anchor="middle">{$t[7]}</text>
<rect x="165" y="215" width="190" height="34" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="260" y="237" text-anchor="middle">{$t[6]}</text>
<rect x="400" y="182" width="110" height="34" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="455" y="204" text-anchor="middle">{$t[8]}</text>
<rect x="460" y="238" width="120" height="34" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="520" y="260" text-anchor="middle">{$t[9]}</text>
<rect x="518" y="182" width="96" height="34" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2" stroke-dasharray="5 4"/><text x="566" y="204" text-anchor="middle">{$t[10]}</text>
</g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxint_journey')) {
    /** A customer journey map: five phases, their touchpoints and the emotional curve with its lowest point. */
    function fig_uxint_journey(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Scoprire', 'Valutare', 'Comprare', 'Usare', 'Chiedere aiuto', 'social', 'sito, recensioni', 'app, email', 'prodotto', 'assistenza', 'opportunità', 'emozioni', 'punti di contatto']
            : ['Discover', 'Evaluate', 'Buy', 'Use', 'Get help', 'social', 'site, reviews', 'app, email', 'product', 'support', 'opportunity', 'emotions', 'touchpoints'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g fill="#F1F0F8"><rect x="124" y="16" width="94" height="36" rx="10"/><rect x="224" y="16" width="94" height="36" rx="10"/><rect x="324" y="16" width="94" height="36" rx="10"/><rect x="424" y="16" width="94" height="36" rx="10"/><rect x="524" y="16" width="108" height="36" rx="10"/></g>
<g font-size="14" font-weight="700" text-anchor="middle"><text x="171" y="39">{$t[0]}</text><text x="271" y="39">{$t[1]}</text><text x="371" y="39">{$t[2]}</text><text x="471" y="39">{$t[3]}</text><text x="578" y="39">{$t[4]}</text></g>
<text x="116" y="82" text-anchor="end" fill="#5A5880">{$t[12]}</text>
<g text-anchor="middle" fill="#5A5880"><text x="171" y="82">{$t[5]}</text><text x="271" y="82">{$t[6]}</text><text x="371" y="82">{$t[7]}</text><text x="471" y="82">{$t[8]}</text><text x="578" y="82">{$t[9]}</text></g>
<text x="116" y="196" text-anchor="end" fill="#5A5880">{$t[11]}</text>
<line x1="124" y1="190" x2="630" y2="190" stroke="#E4E2F0" stroke-width="2" stroke-dasharray="6 5"/>
<g font-size="22" font-weight="700" text-anchor="middle"><text x="22" y="138" fill="#0B7F5C">+</text><text x="22" y="262" fill="#C2255C">−</text></g>
<path d="M171 150 C 210 130, 240 115, 271 120 S 335 170, 371 160 S 440 110, 471 125 S 540 260, 578 262" fill="none" stroke="#2F8CF0" stroke-width="3"/>
<g fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"><circle cx="171" cy="150" r="7"/><circle cx="271" cy="120" r="7"/><circle cx="371" cy="160" r="7"/><circle cx="471" cy="125" r="7"/></g>
<circle cx="578" cy="262" r="10" fill="#F03E82" stroke="#FFFFFF" stroke-width="2"/>
<rect x="418" y="268" width="130" height="30" rx="8" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="483" y="288" text-anchor="middle" font-size="14" font-weight="700" fill="#C2255C">{$t[10]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxint_fidelity')) {
    /** The same screen at three fidelities: a paper sketch, a grey wireframe, a coloured high-fidelity mock-up. */
    function fig_uxint_fidelity(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Schizzo', 'Wireframe', 'Alta fedeltà', 'struttura e flussi', 'dettagli, testi, interazioni', 'bassa fedeltà', 'alta fedeltà', 'Regali', 'Cerca']
            : ['Sketch', 'Wireframe', 'High fidelity', 'structure and flows', 'details, copy, interactions', 'low fidelity', 'high fidelity', 'Gifts', 'Search'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 330" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0 L10 5 L0 10 z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="15" text-anchor="middle"><text x="110" y="24">{$t[0]}</text><text x="320" y="24">{$t[1]}</text><text x="530" y="24">{$t[2]}</text></g>
<rect x="40" y="38" width="140" height="210" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<g fill="none" stroke="#8A87B0" stroke-width="2" stroke-linecap="round">
<path d="M56 62 q30 -4 60 1 t50 -2"/><path d="M58 92 h40 M58 104 h70 M58 116 h55"/>
<path d="M57 140 q2 30 1 40 q30 2 50 0 q1 -20 -1 -40 q-25 -2 -50 0 z"/><path d="M120 140 q2 20 0 40 q20 1 44 0 q2 -20 0 -40 z"/>
<path d="M60 214 q40 -6 100 0 q2 12 -2 18 q-48 4 -96 0 q-4 -8 -2 -18 z"/></g>
<rect x="250" y="38" width="140" height="210" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<g fill="#E4E2F0"><rect x="264" y="54" width="112" height="16" rx="4"/><rect x="264" y="86" width="80" height="8" rx="4"/><rect x="264" y="100" width="100" height="8" rx="4"/><rect x="264" y="130" width="52" height="56" rx="6"/><rect x="324" y="130" width="52" height="56" rx="6"/></g>
<rect x="264" y="208" width="112" height="26" rx="8" fill="#8A87B0"/>
<rect x="460" y="38" width="140" height="210" rx="12" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<text x="474" y="68" font-size="17" font-weight="700">{$t[7]}</text>
<rect x="474" y="82" width="112" height="24" rx="12" fill="#F1F0F8"/><text x="488" y="99" font-size="13" fill="#5A5880">{$t[8]}…</text>
<rect x="474" y="122" width="52" height="64" rx="8" fill="#FFE8D6"/><rect x="534" y="122" width="52" height="64" rx="8" fill="#DCEBFF"/>
<circle cx="500" cy="146" r="12" fill="#E8590C"/><rect x="548" y="136" width="24" height="22" rx="4" fill="#2F8CF0"/>
<text x="476" y="200" font-size="13">€24</text><text x="536" y="200" font-size="13">€29</text>
<rect x="474" y="208" width="112" height="26" rx="13" fill="#E8590C"/>
<path d="M512 221 h36" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
<line x1="60" y1="272" x2="580" y2="272" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<g font-size="13" fill="#5A5880"><text x="40" y="292">{$t[5]}</text><text x="600" y="292" text-anchor="end">{$t[6]}</text></g>
<g font-size="14" font-weight="700" text-anchor="middle"><text x="215" y="318" fill="#5A5880">{$t[3]}</text><text x="530" y="318" fill="#B84508">{$t[4]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxint_matrix')) {
    /** The impact/effort matrix with its four quadrants. */
    function fig_uxint_matrix(string $lg): string
    {
        $t = $lg === 'it'
            ? ['IMPATTO', 'SFORZO', 'alto', 'basso', 'Vittorie rapide', 'falle subito', 'Grandi progetti', 'pianificali', 'Riempitivi', 'se avanza tempo', 'Da evitare', 'costano e rendono poco']
            : ['IMPACT', 'EFFORT', 'high', 'low', 'Quick wins', 'do them first', 'Big bets', 'plan them', 'Fill-ins', 'if there is time', 'Avoid', 'costly, little gain'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="110" y="20" width="250" height="130" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="370" y="20" width="250" height="130" rx="12" fill="#DCEBFF"/>
<rect x="110" y="160" width="250" height="130" rx="12" fill="#F1F0F8"/>
<rect x="370" y="160" width="250" height="130" rx="12" fill="#FFE3EE"/>
<g text-anchor="middle"><text x="235" y="76" font-size="17" font-weight="700" fill="#0B7F5C">1 · {$t[4]}</text><text x="235" y="100" fill="#0B7F5C">{$t[5]}</text>
<text x="495" y="76" font-size="17" font-weight="700" fill="#1B66BC">2 · {$t[6]}</text><text x="495" y="100" fill="#1B66BC">{$t[7]}</text>
<text x="235" y="216" font-size="17" font-weight="700" fill="#5A5880">3 · {$t[8]}</text><text x="235" y="240" fill="#5A5880">{$t[9]}</text>
<text x="495" y="216" font-size="17" font-weight="700" fill="#C2255C">✗ {$t[10]}</text><text x="495" y="240" fill="#C2255C">{$t[11]}</text></g>
<text x="40" y="155" text-anchor="middle" font-weight="700" transform="rotate(-90 40 155)">{$t[0]}</text>
<g font-size="13" fill="#5A5880" text-anchor="end"><text x="100" y="40">{$t[2]}</text><text x="100" y="286">{$t[3]}</text></g>
<text x="365" y="314" text-anchor="middle" font-weight="700">{$t[1]}</text>
<g font-size="13" fill="#5A5880"><text x="110" y="314">{$t[3]}</text><text x="620" y="314" text-anchor="end">{$t[2]}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'ux-intermediate', 'family' => 'ux', 'after' => 'ux-base', 'categories' => ['design'],
    'title' => L('UX: livello intermedio', 'UX Intermediate'), 'short' => L('UX intermedio', 'UX Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Metodi di ricerca qualitativa e quantitativa, architettura dell’informazione e card sorting, user flow e customer journey, prototipi e test di usabilità, sintesi e priorità, questionari e analytics, ideazione e design sprint.', 'Qualitative and quantitative research methods, information architecture and card sorting, user flows and customer journeys, prototypes and usability tests, synthesis and priorities, surveys and analytics, ideation and design sprints.'),
    'lessons' => [
        lesson('research', L('Metodi di ricerca', 'Research methods'), [
            P('La ricerca si divide in **qualitativa** (perché le persone fanno qualcosa: interviste, osservazione sul campo, test moderati) e **quantitativa** (quanto e quanti: questionari, analytics, A/B test). Le due si completano: i numeri dicono **dove** c’è un problema, le interviste dicono **perché**.', 'Research splits into **qualitative** (why people do something: interviews, field observation, moderated tests) and **quantitative** (how much and how many: surveys, analytics, A/B tests). The two complement each other: numbers tell you **where** there is a problem, interviews tell you **why**.'),
            Fig(L(fig_uxint_methods('it'), fig_uxint_methods('en')),
                L('Una griglia due per due. In orizzontale la ricerca qualitativa, che spiega il perché, e quella quantitativa, che misura quanto; in verticale che cosa le persone dicono e che cosa fanno. Le interviste stanno in alto a sinistra, i questionari in alto a destra, l’osservazione in basso a sinistra, analytics e test A/B in basso a destra, i test moderati e le heatmap a cavallo.', 'A two-by-two grid. Horizontally qualitative research, which explains why, and quantitative research, which measures how many; vertically what people say and what they do. Interviews sit top left, surveys top right, field observation bottom left, analytics and A/B tests bottom right, with moderated tests and heatmaps on the borders.'),
                L('Per un quadro completo combina metodi di quadranti diversi.', 'For a full picture, combine methods from different quadrants.')),
            P('Nelle **interviste** si chiedono fatti passati, non opinioni sul futuro: “Raccontami l’ultima volta che hai prenotato un viaggio” vale più di “Useresti una app per i viaggi?”. Evita le **domande guidate** (“Non trovi comodo questo tasto?”) e lascia parlare: i silenzi fanno emergere dettagli.', 'In **interviews** you ask about past facts, not opinions about the future: “Tell me about the last time you booked a trip” is worth more than “Would you use a travel app?”. Avoid **leading questions** (“Don’t you find this button handy?”) and let people talk: silences bring out details.'),
            Tip('Ciò che le persone dicono e ciò che fanno spesso non coincide: quando puoi, osserva il comportamento reale.', 'What people say and what they do often differ: whenever you can, observe real behaviour.'),
        ], [
            Q('r1', L('Che cosa dice soprattutto la ricerca quantitativa?', 'What does quantitative research mainly tell you?'), [L('Dove e quanto succede qualcosa', 'Where and how often something happens'), L('Perché le persone si comportano così', 'Why people behave that way'), L('Che colore preferiscono', 'What colour they prefer'), L('Niente di utile', 'Nothing useful')], 0, L('I numeri indicano il dove.', 'Numbers point to the where.')),
            Q('r2', L('Quale domanda d’intervista è migliore?', 'Which interview question is better?'), [L('Raccontami l’ultima volta che l’hai fatto', 'Tell me about the last time you did it'), L('Non trovi comodo questo tasto?', 'Don’t you find this button handy?'), L('Lo compreresti sicuramente, vero?', 'You would surely buy it, right?'), L('Ti piace il nostro logo nuovo?', 'Do you like our new logo?')], 0, L('Fatti passati, non guidati.', 'Past facts, not led.')),
            Q('r3', L('Perché osservare oltre a chiedere?', 'Why observe as well as ask?'), [L('Detto e fatto spesso non coincidono', 'What is said and done often differ'), L('Osservare è più veloce sempre', 'Observing is always faster'), L('Le interviste sono vietate', 'Interviews are forbidden'), L('Per non parlare con gli utenti', 'To avoid talking to users')], 0, L('Il comportamento non mente.', 'Behaviour does not lie.')),
        ], task('text', L('Riscrivi questa domanda guidata in modo neutro, su fatti passati: “Non pensi che sarebbe comodo ordinare la cena dall’app?”', 'Rewrite this leading question neutrally, about past facts: “Don’t you think ordering dinner from the app would be handy?”'), '', [re('ultim|last|raccont|tell me|come hai|how did', L('Chiede un fatto passato', 'Asks about a past fact')), re('^(?!.*(non pensi|don.t you|comodo|handy)).+$', L('Non è guidata', 'It is not leading'))], '', 'Raccontami l’ultima volta che hai ordinato la cena: come hai fatto?'), [
            dMatch(pMatch(), [[L('Intervista', 'Interview'), L('Qualitativa', 'Qualitative')], ['Analytics', L('Quantitativa', 'Quantitative')], ['A/B test', L('Confronto tra due versioni', 'Comparing two versions')], [L('Osservazione sul campo', 'Field observation'), L('Comportamento reale', 'Real behaviour')]], L('Scegliere il metodo.', 'Choosing the method.')),
            dFlash(pFlash(), [[L('I numeri dicono dove, le interviste perché', 'Numbers tell where, interviews why'), true], [L('Le domande guidate danno risposte più affidabili', 'Leading questions give more reliable answers'), false], [L('Chiedere fatti passati è meglio che ipotesi future', 'Asking about past facts beats future hypotheses'), true], [L('Basta chiedere agli utenti che cosa vogliono', 'Just ask users what they want'), false]], L('Ricerca ben fatta.', 'Research done right.')),
        ]),
        lesson('ia', L('Architettura dell’informazione', 'Information architecture'), [
            P('L’**architettura dell’informazione** (IA) decide come organizzare, etichettare e collegare i contenuti perché le persone trovino ciò che cercano. Si compone di **organizzazione** (gerarchie e categorie), **etichette** (i nomi delle voci), **navigazione** (menu, breadcrumb, link) e **ricerca**.', '**Information architecture** (IA) decides how to organise, label and connect content so people find what they look for. It is made of **organisation** (hierarchies and categories), **labels** (the names of items), **navigation** (menus, breadcrumbs, links) and **search**.'),
            P('Con il **card sorting** gli utenti raggruppano schede con i contenuti: **aperto** (creano e nominano i gruppi), **chiuso** (usano categorie date). Con il **tree testing** si verifica una gerarchia senza grafica: “Dove cercheresti il reso di un ordine?”. Le etichette devono usare le parole degli utenti, non il gergo interno.', 'With **card sorting** users group cards with content: **open** (they create and name the groups), **closed** (they use given categories). With **tree testing** you check a hierarchy without visuals: “Where would you look to return an order?”. Labels must use users’ words, not internal jargon.'),
            C('Negozio
├── Donna
│   ├── Abbigliamento
│   └── Scarpe
├── Uomo
├── Il mio account
│   ├── Ordini e resi
│   └── Indirizzi
└── Aiuto', 'text'),
        ], [
            Q('i1', L('Che cosa decide l’architettura dell’informazione?', 'What does information architecture decide?'), [L('Come organizzare ed etichettare i contenuti', 'How to organise and label content'), L('Il colore dei pulsanti', 'The buttons’ colour'), L('Il server su cui pubblicare', 'The server to publish on'), L('Il prezzo dei prodotti', 'Product prices')], 0, L('Trovare ciò che si cerca.', 'Finding what you look for.')),
            Q('i2', L('Che differenza c’è tra card sorting aperto e chiuso?', 'What is the difference between open and closed card sorting?'), [L('Nell’aperto gli utenti creano i gruppi', 'In open sorting users create the groups'), L('Nel chiuso le schede sono girate', 'In closed sorting cards are face down'), L('L’aperto si fa all’aperto', 'Open sorting is done outdoors'), L('Nessuna', 'None')], 0, L('Aperto: nuove categorie.', 'Open: new categories.')),
            Q('i3', L('A che cosa serve il tree testing?', 'What is tree testing for?'), [L('Verificare una gerarchia senza grafica', 'Checking a hierarchy without visuals'), L('Testare la velocità del sito', 'Testing site speed'), L('Disegnare alberi', 'Drawing trees'), L('Contare i link', 'Counting links')], 0, L('Solo struttura ed etichette.', 'Structure and labels only.')),
        ], task('text', L('Organizza questi contenuti di un sito di una palestra in due gruppi con un’etichetta ciascuno, nel formato `Etichetta: voce, voce`: orari, abbonamenti, corsi di yoga, prezzi, spinning, prova gratuita.', 'Organise this gym site content into two labelled groups, in the format `Label: item, item`: timetable, memberships, yoga classes, prices, spinning, free trial.'), '', [re('^[^:\n]+:\s*\S.*\n[^:\n]+:\s*\S', L('Due gruppi con etichetta', 'Two labelled groups')), re('yoga[^\n]*spinning|spinning[^\n]*yoga', L('I corsi stanno insieme', 'Classes are together'))], '', "Corsi: corsi di yoga, spinning, orari\nAbbonamenti: prezzi, abbonamenti, prova gratuita"), [
            dMatch(pMatch(), [[L('Organizzazione', 'Organisation'), L('Gerarchie e categorie', 'Hierarchies and categories')], [L('Etichette', 'Labels'), L('I nomi delle voci', 'Item names')], [L('Navigazione', 'Navigation'), L('Menu e breadcrumb', 'Menus and breadcrumbs')], ['Tree testing', L('Verifica della gerarchia', 'Hierarchy check')]], L('I pilastri dell’IA.', 'The pillars of IA.')),
            dFlash(pFlash(), [[L('Le etichette devono usare le parole degli utenti', 'Labels should use users’ words'), true], [L('Il gergo interno aiuta gli utenti', 'Internal jargon helps users'), false], [L('Il card sorting coinvolge gli utenti', 'Card sorting involves users'), true], [L('Il tree testing richiede la grafica finale', 'Tree testing needs the final visuals'), false]], L('Contenuti al posto giusto.', 'Content in the right place.')),
        ]),
        lesson('flows', L('User flow e customer journey', 'User flows and customer journeys'), [
            P('Un **user flow** è il percorso passo per passo per completare un compito: schermate, scelte e possibili errori. Disegnarlo mostra passaggi inutili e vicoli ciechi: ogni passo in più fa perdere una parte degli utenti, quindi si cerca il **percorso più corto** che funzioni.', 'A **user flow** is the step-by-step path to complete a task: screens, choices and possible errors. Drawing it reveals useless steps and dead ends: every extra step loses part of the users, so you look for the **shortest path** that works.'),
            C('[Carrello] → [Accedi o ospite?] → [Indirizzo] → [Pagamento] → [Conferma]
                    ↓
             [Password dimenticata] → [Email] → (ritorno al carrello?)', 'text'),
            P('La **customer journey map** è più ampia: segue la persona attraverso tutte le fasi (scoprire, valutare, acquistare, usare, chiedere aiuto) e tutti i **touchpoint** (sito, negozio, email, assistenza), con azioni, pensieri ed **emozioni**. Il punto più basso della curva emotiva è spesso la migliore opportunità di miglioramento.', 'The **customer journey map** is broader: it follows the person through all phases (discover, evaluate, buy, use, ask for help) and all **touchpoints** (site, shop, email, support), with actions, thoughts and **emotions**. The lowest point of the emotional curve is often the best improvement opportunity.'),
            Fig(L(fig_uxint_journey('it'), fig_uxint_journey('en')),
                L('Una mappa del percorso con cinque fasi in colonna: scoprire, valutare, comprare, usare, chiedere aiuto, ciascuna con i suoi punti di contatto. Sotto, una curva delle emozioni sale e scende e tocca il punto più basso nella fase «chiedere aiuto», segnato come opportunità.', 'A journey map with five phases as columns: discover, evaluate, buy, use, get help, each with its touchpoints. Below, an emotional curve rises and falls and hits its lowest point in the “get help” phase, marked as an opportunity.'),
                L('Il punto più basso della curva è dove intervenire per primo.', 'The lowest point of the curve is where to act first.')),
        ], [
            Q('f1', L('Che cosa descrive uno user flow?', 'What does a user flow describe?'), [L('I passi per completare un compito', 'The steps to complete a task'), L('Il flusso di dati nel server', 'Data flow on the server'), L('L’andamento delle vendite', 'The sales trend'), L('Le ore di lavoro del team', 'The team’s working hours')], 0, L('Schermate e scelte.', 'Screens and choices.')),
            Q('f2', L('Che cos’è un touchpoint?', 'What is a touchpoint?'), [L('Un punto di contatto tra persona e servizio', 'A point of contact between person and service'), L('Uno schermo touch', 'A touch screen'), L('Un tipo di icona', 'A kind of icon'), L('Un pulsante grande', 'A big button')], 0, L('Sito, negozio, email…', 'Site, shop, email…')),
            Q('f3', L('Che cosa aggiunge la journey map rispetto a un flow?', 'What does a journey map add compared to a flow?'), [L('Tutte le fasi e le emozioni della persona', 'All phases and the person’s emotions'), L('Il codice delle schermate', 'The screens’ code'), L('I colori del brand', 'Brand colours'), L('Il numero di pixel', 'The pixel count')], 0, L('Una visione d’insieme.', 'A big-picture view.')),
        ], task('text', L('Scrivi uno user flow per “reimpostare la password” in quattro passi, uno per riga con `->` all’inizio, partendo dal link “Password dimenticata”.', 'Write a user flow for “reset password” in four steps, one per line starting with `->`, beginning with the “Forgot password” link.'), "-> \n-> \n-> \n-> ", [re('^->\s*\S.*\n->\s*\S.*\n->\s*\S.*\n->\s*\S', L('Quattro passi', 'Four steps')), re('^->\s*.*(dimentic|forgot)', L('Parte dal link', 'Starts from the link')), re('email|mail|codice|code|link', L('Passa da una verifica', 'Goes through a check'))], '', "-> tocco Password dimenticata\n-> scrivo la mia email\n-> apro il link ricevuto per email\n-> scelgo la nuova password ed entro"), [
            dOrder(pSort(), [L('Scoprire il servizio', 'Discover the service'), L('Valutare le alternative', 'Evaluate alternatives'), L('Acquistare', 'Buy'), L('Usare il prodotto', 'Use the product'), L('Chiedere assistenza', 'Ask for support')], L('Le fasi del journey.', 'The journey phases.')),
            dFlash(pFlash(), [[L('Ogni passo in più può far perdere utenti', 'Every extra step can lose users'), true], [L('Una journey map mostra solo il sito web', 'A journey map shows only the website'), false], [L('Il punto più basso delle emozioni è un’opportunità', 'The emotional low point is an opportunity'), true], [L('Un user flow non considera gli errori', 'A user flow ignores errors'), false]], L('Percorsi e viaggi.', 'Paths and journeys.')),
        ]),
        lesson('testing', L('Prototipi e test di usabilità', 'Prototypes and usability testing'), [
            P('La **fedeltà** di un prototipo va scelta in base alla domanda: **bassa** (carta, wireframe) per struttura e flussi, veloce da buttare; **alta** (Figma con grafica e interazioni) per dettagli, testi e micro-interazioni. Non serve il codice per scoprire che un menu non si capisce.', 'A prototype’s **fidelity** should match the question: **low** (paper, wireframes) for structure and flows, quick to throw away; **high** (Figma with visuals and interactions) for details, copy and micro-interactions. You do not need code to discover a menu is confusing.'),
            Fig(L(fig_uxint_fidelity('it'), fig_uxint_fidelity('en')),
                L('La stessa schermata di un negozio di regali tre volte: uno schizzo a mano con linee tremolanti, un wireframe con rettangoli grigi e una versione ad alta fedeltà con colori, prezzi e un pulsante arancione. Una freccia va dalla bassa all’alta fedeltà.', 'The same gift shop screen three times: a hand sketch with wobbly lines, a wireframe with grey rectangles and a high-fidelity version with colours, prices and an orange button. An arrow runs from low to high fidelity.'),
                L('La bassa fedeltà risponde a domande di struttura, l’alta a domande di dettaglio.', 'Low fidelity answers questions about structure, high fidelity questions about detail.')),
            P('Un buon test ha **compiti realistici** con uno scopo, non istruzioni (“Hai un compleanno sabato: trova un regalo sotto 30 €”, non “clicca su Regali”). Si misurano **successo**, **tempo**, **errori** e soddisfazione, ad esempio con il questionario **SUS**. Test **moderati** (con un facilitatore) danno profondità, **non moderati** (da remoto, da soli) danno numeri.', 'A good test has **realistic tasks** with a goal, not instructions (“You have a birthday on Saturday: find a gift under €30”, not “click Gifts”). You measure **success**, **time**, **errors** and satisfaction, for example with the **SUS** questionnaire. **Moderated** tests (with a facilitator) give depth, **unmoderated** ones (remote, alone) give numbers.'),
        ], [
            Q('p1', L('Quando usare un prototipo a bassa fedeltà?', 'When to use a low-fidelity prototype?'), [L('Per testare struttura e flussi presto', 'To test structure and flows early'), L('Per scegliere la tonalità di un colore', 'To choose a colour shade'), L('Per misurare la velocità del server', 'To measure server speed'), L('Mai', 'Never')], 0, L('Veloce da fare e da buttare.', 'Quick to make and discard.')),
            Q('p2', L('Quale compito di test è scritto meglio?', 'Which test task is written better?'), [L('Trova un regalo sotto 30 € per sabato', 'Find a gift under €30 for Saturday'), L('Clicca su Regali e poi su Filtri', 'Click Gifts and then Filters'), L('Guarda la home e dimmi se ti piace', 'Look at the home page and say if you like it'), L('Prova tutti i pulsanti', 'Try every button')], 0, L('Uno scopo, non istruzioni.', 'A goal, not instructions.')),
            Q('p3', L('Che cos’è il SUS?', 'What is the SUS?'), [L('Un questionario standard di usabilità', 'A standard usability questionnaire'), L('Un software di prototipazione', 'A prototyping tool'), L('Un errore di sistema', 'A system error'), L('Un tipo di font', 'A kind of font')], 0, L('System Usability Scale.', 'System Usability Scale.')),
        ], task('text', L('Trasforma questa istruzione in un compito di test realistico con uno scopo: “Clicca su Prenota e scegli una data”. Il servizio è un **parrucchiere**.', 'Turn this instruction into a realistic test task with a goal: “Click Book and choose a date”. The service is a **hairdresser**.'), '', [re('parrucch|taglio|capelli|hair|haircut', L('Parla del servizio', 'Mentions the service')), re('^(?!.*(clicca|click)).+$', L('Non dice dove cliccare', 'Does not say where to click'))], '', 'Vuoi tagliarti i capelli venerdì dopo il lavoro: prenota un appuntamento che ti vada bene.'), [
            dMatch(pMatch(), [[L('Bassa fedeltà', 'Low fidelity'), L('Struttura e flussi', 'Structure and flows')], [L('Alta fedeltà', 'High fidelity'), L('Dettagli e interazioni', 'Details and interactions')], [L('Test moderato', 'Moderated test'), L('Con un facilitatore', 'With a facilitator')], [L('Test non moderato', 'Unmoderated test'), L('Da soli, da remoto', 'Alone, remotely')], ['SUS', L('Questionario di usabilità', 'Usability questionnaire')]], L('Prototipare e testare.', 'Prototyping and testing.')),
            dFlash(pFlash(), [[L('Un prototipo di carta può trovare problemi reali', 'A paper prototype can find real problems'), true], [L('Nei compiti si dice all’utente dove cliccare', 'Tasks tell the user where to click'), false], [L('Si misurano successo, tempo ed errori', 'Success, time and errors are measured'), true], [L('Serve il codice finito per fare un test', 'Finished code is needed for a test'), false]], L('Test che insegnano.', 'Tests that teach.')),
        ]),
        lesson('synthesis', L('Sintesi della ricerca e priorità', 'Research synthesis and priorities'), [
            P('Dopo interviste e test hai decine di note. La **mappa di affinità** le raggruppa per somiglianza: ogni gruppo diventa un **tema**, e da un tema nasce un **insight**: una frase che spiega un comportamento e il suo perché (“Chi studia la sera salta le lezioni lunghe perché ha poco tempo”).', 'After interviews and tests you have dozens of notes. An **affinity map** groups them by similarity: each group becomes a **theme**, and from a theme comes an **insight**: a sentence explaining a behaviour and its why (“People studying in the evening skip long lessons because they have little time”).'),
            P('Le idee che ne escono sono troppe: si mettono in **priorità**. La matrice **impatto/sforzo** mette prima le idee ad alto impatto e basso sforzo; il metodo **MoSCoW** divide in Must, Should, Could e Won’t (per ora).', 'The ideas that come out are too many: they get **prioritised**. The **impact/effort** matrix puts high-impact, low-effort ideas first; the **MoSCoW** method splits them into Must, Should, Could and Won’t (for now).'),
            Fig(L(fig_uxint_matrix('it'), fig_uxint_matrix('en')),
                L('Una griglia due per due: in verticale l’impatto, in orizzontale lo sforzo. In alto a sinistra le vittorie rapide, da fare subito; in alto a destra i grandi progetti, da pianificare; in basso a sinistra i riempitivi; in basso a destra le idee da evitare, che costano molto e rendono poco.', 'A two-by-two grid: impact vertically, effort horizontally. Top left the quick wins, to do first; top right the big bets, to plan; bottom left the fill-ins; bottom right the ideas to avoid, costly with little gain.'),
                L('Si parte dall’angolo in alto a sinistra: molto impatto, poco sforzo.', 'Start from the top left corner: high impact, low effort.')),
        ], [
            Q('sy1', L('A che cosa serve una mappa di affinità?', 'What is an affinity map for?'), [L('Raggruppare le note in temi', 'Grouping notes into themes'), L('Disegnare il sito', 'Drawing the site'), L('Calcolare il budget', 'Calculating the budget'), L('Scegliere i colori', 'Choosing colours')], 0, L('Dal caos ai temi.', 'From chaos to themes.')),
            Q('sy2', L('Che cos’è un insight?', 'What is an insight?'), [L('Un comportamento con il suo perché', 'A behaviour with its why'), L('Una citazione qualsiasi', 'Any quote'), L('Un numero di visite', 'A visit count'), L('Una richiesta di funzione', 'A feature request')], 0, L('Spiega, non solo descrive.', 'It explains, not just describes.')),
            Q('sy3', L('Che cosa va fatto prima nella matrice impatto/sforzo?', 'What goes first in the impact/effort matrix?'), [L('Alto impatto, basso sforzo', 'High impact, low effort'), L('Basso impatto, alto sforzo', 'Low impact, high effort'), L('Tutto insieme', 'Everything together'), L('Ciò che piace al capo', 'What the boss likes')], 0, L('Le vittorie rapide.', 'Quick wins.')),
        ], task('text', L('Scrivi un insight che contenga un comportamento e la parola `perché`.', 'Write an insight containing a behaviour and the word `because`.'), '', [re('\S.{10,}\s(perché|perche|because)\s.{8,}', L('C’è il perché', 'The why is there'))], '', 'Chi usa l’app sul treno abbandona i video perché la connessione cade spesso.'), [
            dOrder(pSort(), [L('Raccogli le note', 'Collect the notes'), L('Raggruppale per affinità', 'Group them by affinity'), L('Dai un nome ai temi', 'Name the themes'), L('Scrivi gli insight', 'Write the insights'), L('Metti in priorità le idee', 'Prioritise the ideas')], L('Dalla ricerca alle decisioni.', 'From research to decisions.')),
            dMatch(pMatch(), [['Must', L('Indispensabile', 'Essential')], ['Should', L('Importante', 'Important')], ['Could', L('Se avanza tempo', 'If time allows')], ['Won’t', L('Non ora', 'Not now')]], L('Il metodo MoSCoW.', 'The MoSCoW method.')),
        ]),
        lesson('surveys', L('Questionari e dati quantitativi', 'Surveys and quantitative data'), [
            P('Un **questionario** raggiunge tante persone, ma solo se è scritto bene: una domanda alla volta (niente «È veloce e facile?», che ne contiene due), risposte che coprono tutti i casi, scale coerenti come la **scala Likert** (da «per niente d’accordo» a «del tutto d’accordo») e poche domande aperte. Prima di inviarlo, provalo con 2-3 persone: le domande confuse emergono subito.', 'A **survey** reaches many people, but only if it is well written: one question at a time (no “Is it fast and easy?”, which holds two), answers that cover every case, consistent scales such as the **Likert scale** (from “strongly disagree” to “strongly agree”) and few open questions. Before sending it, try it with 2-3 people: confusing questions show up at once.'),
            P('Gli **analytics** dicono che cosa fanno davvero le persone: pagine visitate, clic, abbandoni, ricerche senza risultati. Le **heatmap** mostrano dove si clicca e fin dove si scorre; le **registrazioni di sessione** (anonimizzate e con il consenso) mostrano i punti di esitazione. Attenzione al **campione**: chi risponde a un questionario non rappresenta sempre tutti gli utenti, e i numeri dicono che cosa succede, non perché.', '**Analytics** tell you what people really do: pages visited, clicks, drop-offs, searches with no results. **Heatmaps** show where people click and how far they scroll; **session recordings** (anonymised and with consent) show the moments of hesitation. Watch the **sample**: those who answer a survey do not always represent all users, and numbers tell you what happens, not why.'),
            C('No:  L’app è veloce e facile da usare?   (due domande in una)
Sì:  Quanto è stato facile trovare il prodotto che cercavi?
     1 Molto difficile  2  3  4  5 Molto facile', 'text'),
        ], [
            Q('s1', L('Che problema ha la domanda «L’app è veloce e facile da usare?»', 'What is wrong with the question “Is the app fast and easy to use?”'), [L('È troppo corta per essere utile', 'It is too short to be useful'), L('Usa parole troppo tecniche', 'It uses words that are too technical'), L('Chiede due cose in una', 'It asks two things in one'), L('Non ha una scala numerica', 'It has no numeric scale')], 2, L('Una domanda, un concetto.', 'One question, one concept.')),
            Q('s2', L('Che cosa mostra una heatmap?', 'What does a heatmap show?'), [L('La temperatura del telefono', 'The phone’s temperature'), L('Dove si clicca e fin dove si scorre', 'Where people click and how far they scroll'), L('Il tempo di caricamento delle pagine', 'Page loading times'), L('Le vendite divise per regione', 'Sales split by region')], 1, L('Più caldo = più attenzione.', 'Hotter = more attention.')),
            Q('s3', L('Perché provare un questionario con 2-3 persone prima di inviarlo?', 'Why try a survey with 2-3 people before sending it?'), [L('Per scoprire le domande confuse in tempo', 'To find confusing questions in time'), L('Per avere più risposte in totale', 'To get more answers overall'), L('Perché è obbligatorio per legge', 'Because the law requires it'), L('Per scegliere il colore del modulo', 'To choose the form’s colour')], 0, L('Dopo l’invio non si torna indietro.', 'Once sent, there is no going back.')),
        ], task('text', L('Dividi questa domanda doppia in due domande separate, una per riga, ognuna con il `?`: «Il checkout è veloce e sicuro?»', 'Split this double question into two separate questions, one per line, each ending with `?`: “Is the checkout fast and secure?”'), 'Il checkout è veloce e sicuro?', [re('^(?![^\n]*(sicur|secur|safe))[^\n]*(veloc|fast|quick|rapid)[^\n]*\?', L('Una domanda sulla velocità', 'A question on speed')), re('^(?![^\n]*(veloc|fast|quick|rapid))[^\n]*(sicur|secur|safe)[^\n]*\?', L('Una domanda sulla sicurezza', 'A question on security'))], '', "Il checkout ti è sembrato veloce?\nTi sei sentito sicuro a inserire i dati di pagamento?"), [
            dMatch(pMatch(), [[L('Scala Likert', 'Likert scale'), L('Da «per niente» a «del tutto d’accordo»', 'From “strongly disagree” to “strongly agree”')], ['Heatmap', L('Dove si clicca', 'Where people click')], [L('Registrazione di sessione', 'Session recording'), L('Punti di esitazione', 'Moments of hesitation')], [L('Domanda doppia', 'Double question'), L('Due domande in una', 'Two questions in one')]], L('Strumenti quantitativi.', 'Quantitative tools.')),
            dFlash(pFlash(), [[L('Chi risponde a un questionario può non rappresentare tutti', 'Survey respondents may not represent everyone'), true], [L('Gli analytics spiegano perché le persone abbandonano', 'Analytics explain why people drop off'), false], [L('Le registrazioni di sessione richiedono attenzione alla privacy', 'Session recordings require care for privacy'), true], [L('Più domande aperte rendono un questionario più facile', 'More open questions make a survey easier'), false]], L('Numeri ben raccolti.', 'Numbers well collected.')),
        ]),
        lesson('ideation', L('Ideazione e workshop', 'Ideation and workshops'), [
            P('Dagli insight si passa alle idee. Una domanda **«Come potremmo…?»** (How Might We) trasforma un problema in un invito: «Come potremmo aiutare chi studia la sera a finire una lezione in 5 minuti?». Nel **brainstorming** prima si generano tante idee senza giudicarle, poi si scelgono: mescolare le due fasi spegne le idee migliori.', 'From insights you move to ideas. A **“How might we…?”** question turns a problem into an invitation: “How might we help people studying in the evening finish a lesson in 5 minutes?”. In **brainstorming** you first generate many ideas without judging them, then choose: mixing the two phases kills the best ideas.'),
            P('Con il **Crazy 8** si piega un foglio in otto riquadri e si disegnano otto idee in otto minuti, per superare la prima idea ovvia. Il **design sprint** (nato in Google Ventures) porta un team da un problema a un prototipo testato con utenti veri in cinque giorni: capire, abbozzare, decidere, prototipare, testare. Si vota con puntini adesivi (**dot voting**) e alla fine decide chi ha la responsabilità del prodotto.', 'With **Crazy 8s** you fold a sheet into eight panels and sketch eight ideas in eight minutes, to get past the first obvious idea. The **design sprint** (born at Google Ventures) takes a team from a problem to a prototype tested with real users in five days: understand, sketch, decide, prototype, test. People vote with sticky dots (**dot voting**) and in the end the person responsible for the product decides.'),
        ], [
            Q('i1', L('Quale domanda è nel formato «Come potremmo…?»', 'Which question is in the “How might we…?” format?'), [L('Perché gli utenti sbagliano sempre?', 'Why do users always get it wrong?'), L('Come potremmo aiutare chi viaggia a trovare un treno in 30 secondi?', 'How might we help travellers find a train in 30 seconds?'), L('Quanto costerà questo progetto?', 'How much will this project cost?'), L('Chi ha disegnato questa schermata?', 'Who designed this screen?')], 1, L('Un problema diventa un invito.', 'A problem becomes an invitation.')),
            Q('i2', L('Che cos’è il Crazy 8?', 'What are Crazy 8s?'), [L('Otto riunioni in un giorno', 'Eight meetings in one day'), L('Un test con otto utenti', 'A test with eight users'), L('Otto idee abbozzate in otto minuti', 'Eight ideas sketched in eight minutes'), L('Una griglia di layout a otto colonne', 'An eight-column layout grid')], 2, L('Oltre la prima idea ovvia.', 'Past the first obvious idea.')),
            Q('i3', L('Quanto dura un design sprint classico?', 'How long does a classic design sprint last?'), [L('Un’ora', 'One hour'), L('Sei mesi', 'Six months'), L('Un anno', 'One year'), L('Cinque giorni', 'Five days')], 3, L('Esistono anche varianti da quattro giorni.', 'Four-day variants also exist.')),
        ], task('text', L('Trasforma questo problema in una domanda «Come potremmo…?»: «I clienti abbandonano il carrello quando vedono le spese di spedizione». Inizia con `Come potremmo` (o `How might we`) e finisci con `?`.', 'Turn this problem into a “How might we…?” question: “Customers abandon the cart when they see the shipping costs”. Start with `How might we` and end with `?`.'), '', [re('^(come potremmo|how might we)\b', L('Il formato giusto', 'The right format')), re('\?\s*$', L('È una domanda', 'It is a question')), re('spedizion|shipping|deliver|consegn|cost|carrello|cart|checkout', L('Parla del problema', 'It addresses the problem'))], '', 'Come potremmo mostrare le spese di spedizione presto, così che nessuno abbia sorprese nel carrello?'), [
            dOrder(pSort(), [L('Capire il problema', 'Understand the problem'), L('Abbozzare soluzioni', 'Sketch solutions'), L('Decidere quale provare', 'Decide which to try'), L('Costruire il prototipo', 'Build the prototype'), L('Testare con utenti veri', 'Test with real users')], L('I cinque giorni di un design sprint.', 'The five days of a design sprint.')),
            dFlash(pFlash(), [[L('Nel brainstorming prima si genera, poi si giudica', 'In brainstorming you generate first, then judge'), true], [L('La prima idea è quasi sempre la migliore', 'The first idea is almost always the best'), false], [L('Il dot voting aiuta un gruppo a scegliere', 'Dot voting helps a group choose'), true], [L('Un design sprint finisce con un documento, senza test', 'A design sprint ends with a document, without testing'), false]], L('Dalle idee al prototipo.', 'From ideas to prototype.')),
        ]),
    ],
];
