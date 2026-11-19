<?php
/** UX on mobile: context of use, thumb zone and touch targets, navigation patterns, gestures, forms and notifications, perceived speed, accessibility, watches, cars and voice. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uxmob_thumb')) {
    /** The thumb zone of a phone held in the right hand, and a layout that follows it. */
    function fig_uxmob_thumb(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Difficile', 'Con sforzo', 'Facile', 'Elimina account', 'Azioni rare e pericolose in alto', 'Azione principale e navigazione in basso', 'Paga ora']
            : ['Hard', 'Stretch', 'Easy', 'Delete account', 'Rare and risky actions up high', 'Main action and navigation at the bottom', 'Pay now'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 400" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><clipPath id="scr"><rect x="52" y="34" width="176" height="336" rx="18"/></clipPath><clipPath id="scr2"><rect x="372" y="34" width="176" height="336" rx="18"/></clipPath></defs>
<rect x="40" y="20" width="200" height="364" rx="30" fill="#1E1C52"/>
<g clip-path="url(#scr)"><rect x="52" y="34" width="176" height="336" fill="#FFE3EE"/>
<ellipse cx="200" cy="360" rx="190" ry="230" fill="#FFE8D6"/><ellipse cx="190" cy="350" rx="125" ry="150" fill="#D5F6EA"/></g>
<g font-weight="700" text-anchor="middle"><text x="140" y="70" fill="#C2255C">{$t[0]}</text><text x="104" y="190" fill="#B84508">{$t[1]}</text><text x="160" y="300" fill="#0B7F5C">{$t[2]}</text></g>
<rect x="360" y="20" width="200" height="364" rx="30" fill="#1E1C52"/>
<rect x="372" y="34" width="176" height="336" rx="18" fill="#FFFFFF"/>
<g clip-path="url(#scr2)">
<text x="386" y="64" font-size="13" fill="#C2255C">{$t[3]}</text>
<rect x="386" y="84" width="148" height="10" rx="5" fill="#E4E2F0"/><rect x="386" y="102" width="110" height="10" rx="5" fill="#E4E2F0"/><rect x="386" y="126" width="148" height="90" rx="10" fill="#F1F0F8"/>
<rect x="386" y="268" width="148" height="40" rx="20" fill="#E8590C"/><text x="460" y="293" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[6]}</text>
<rect x="372" y="322" width="176" height="48" fill="#F1F0F8"/>
<g fill="#8A87B0"><circle cx="402" cy="346" r="8"/><circle cx="446" cy="346" r="8"/><circle cx="490" cy="346" r="8"/><circle cx="530" cy="346" r="8"/></g></g>
<text x="460" y="16" text-anchor="middle" font-size="13" fill="#C2255C" font-weight="700">↑ {$t[4]}</text>
<text x="460" y="398" text-anchor="middle" font-size="13" fill="#0B7F5C" font-weight="700">↓ {$t[5]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxmob_tabs')) {
    /** A hamburger menu hiding the sections versus a bottom tab bar showing them. */
    function fig_uxmob_tabs(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Menu hamburger', 'Tab bar in basso', 'voci nascoste: si usano meno', 'sempre visibili, sotto il pollice', 'Home', 'Cerca', 'Ordini', 'Profilo']
            : ['Hamburger menu', 'Bottom tab bar', 'hidden items: used less', 'always visible, under the thumb', 'Home', 'Search', 'Orders', 'Profile'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 380" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g font-size="15" font-weight="700" text-anchor="middle"><text x="160" y="24">{$t[0]}</text><text x="480" y="24">{$t[1]}</text></g>
<rect x="70" y="38" width="180" height="300" rx="24" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<g stroke="#1E1C52" stroke-width="3" stroke-linecap="round"><path d="M90 66 h22 M90 74 h22 M90 82 h22"/></g>
<rect x="90" y="104" width="140" height="12" rx="6" fill="#E4E2F0"/><rect x="90" y="124" width="100" height="12" rx="6" fill="#E4E2F0"/>
<rect x="90" y="150" width="140" height="80" rx="10" fill="#F1F0F8"/><rect x="90" y="244" width="140" height="12" rx="6" fill="#E4E2F0"/><rect x="90" y="264" width="120" height="12" rx="6" fill="#E4E2F0"/>
<circle cx="101" cy="74" r="20" fill="none" stroke="#F03E82" stroke-width="2" stroke-dasharray="5 4"/>
<text x="160" y="362" text-anchor="middle" font-size="14" fill="#C2255C" font-weight="700">✗ {$t[2]}</text>
<rect x="390" y="38" width="180" height="300" rx="24" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<rect x="410" y="64" width="90" height="14" rx="7" fill="#1E1C52"/>
<rect x="410" y="104" width="140" height="12" rx="6" fill="#E4E2F0"/><rect x="410" y="124" width="100" height="12" rx="6" fill="#E4E2F0"/>
<rect x="410" y="150" width="140" height="80" rx="10" fill="#F1F0F8"/><rect x="410" y="244" width="120" height="12" rx="6" fill="#E4E2F0"/>
<path d="M391 278 h178 v36 a23 23 0 0 1 -23 23 h-132 a23 23 0 0 1 -23 -23 z" fill="#F1F0F8"/>
<line x1="391" y1="278" x2="569" y2="278" stroke="#8A87B0" stroke-width="1.5"/>
<rect x="400" y="284" width="38" height="20" rx="10" fill="#FFE8D6"/>
<g fill="#E8590C"><circle cx="419" cy="294" r="6"/></g><g fill="#8A87B0"><circle cx="461" cy="294" r="6"/><circle cx="503" cy="294" r="6"/><circle cx="545" cy="294" r="6"/></g>
<g font-size="11" text-anchor="middle"><text x="419" y="322" fill="#B84508" font-weight="700">{$t[4]}</text><text x="461" y="322">{$t[5]}</text><text x="503" y="322">{$t[6]}</text><text x="545" y="322">{$t[7]}</text></g>
<text x="480" y="362" text-anchor="middle" font-size="14" fill="#0B7F5C" font-weight="700">✓ {$t[3]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxmob_skeleton')) {
    /** The same loading screen with a spinner and with a skeleton: the skeleton shows the shape of the page at once. */
    function fig_uxmob_skeleton(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Spinner', 'Skeleton', 'non si sa che cosa arriva', 'la forma della pagina, subito']
            : ['Spinner', 'Skeleton', 'no idea what is coming', 'the shape of the page, at once'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 360" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.spin{transform-origin:160px 180px;animation:spin 3s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}.sh{animation:sh 3s ease-in-out infinite}@keyframes sh{0%,100%{opacity:1}50%{opacity:.45}}@media (prefers-reduced-motion:reduce){.spin,.sh{animation:none}}</style>
<g font-size="15" font-weight="700" text-anchor="middle"><text x="160" y="24">{$t[0]}</text><text x="480" y="24">{$t[1]}</text></g>
<rect x="70" y="38" width="180" height="280" rx="24" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<circle cx="160" cy="180" r="22" fill="none" stroke="#E4E2F0" stroke-width="6"/>
<path class="spin" d="M160 158 a22 22 0 0 1 22 22" fill="none" stroke="#2F8CF0" stroke-width="6" stroke-linecap="round"/>
<rect x="390" y="38" width="180" height="280" rx="24" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<g class="sh" fill="#E4E2F0"><rect x="410" y="62" width="100" height="16" rx="8"/><rect x="410" y="94" width="140" height="80" rx="10"/><rect x="410" y="188" width="140" height="12" rx="6"/><rect x="410" y="208" width="110" height="12" rx="6"/>
<circle cx="424" cy="252" r="14"/><rect x="446" y="244" width="104" height="10" rx="5"/><rect x="446" y="260" width="70" height="10" rx="5"/></g>
<text x="160" y="346" text-anchor="middle" fill="#C2255C" font-weight="700">✗ {$t[2]}</text>
<text x="480" y="346" text-anchor="middle" fill="#0B7F5C" font-weight="700">✓ {$t[3]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxmob_reader')) {
    /** A product screen with the screen reader’s reading order numbered, and what it says on the icon-only button. */
    function fig_uxmob_reader(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Indietro', 'Scarpe da corsa', '89 €', 'Aggiungi al carrello', '«Aggiungi ai preferiti, pulsante»', 'Ordine di lettura di VoiceOver e TalkBack']
            : ['Back', 'Running shoes', '£89', 'Add to basket', '“Add to favourites, button”', 'VoiceOver and TalkBack reading order'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 360" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="60" y="16" width="220" height="330" rx="26" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<text x="84" y="56" fill="#1B66BC">‹ {$t[0]}</text>
<rect x="80" y="74" width="180" height="110" rx="12" fill="#F1F0F8"/>
<path d="M120 160 q30 -40 70 -20 l30 10 q10 4 6 10 h-106 z" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="84" y="216" font-size="16" font-weight="700">{$t[1]}</text>
<text x="84" y="242">{$t[2]}</text>
<path d="M236 252 l-11 -11 a6.5 6.5 0 0 1 11 -7 a6.5 6.5 0 0 1 11 7 z" fill="none" stroke="#F03E82" stroke-width="2" stroke-linejoin="round"/>
<rect x="80" y="276" width="180" height="44" rx="22" fill="#E8590C"/><text x="170" y="303" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[3]}</text>
<g font-size="13" font-weight="700" fill="#FFFFFF" text-anchor="middle">
<circle cx="52" cy="50" r="12" fill="#7048E8"/><text x="52" y="55">1</text>
<circle cx="52" cy="130" r="12" fill="#7048E8"/><text x="52" y="135">2</text>
<circle cx="52" cy="210" r="12" fill="#7048E8"/><text x="52" y="215">3</text>
<circle cx="52" cy="240" r="12" fill="#7048E8"/><text x="52" y="245">4</text>
<circle cx="268" cy="214" r="12" fill="#7048E8"/><text x="268" y="219">5</text>
<circle cx="52" cy="298" r="12" fill="#7048E8"/><text x="52" y="303">6</text></g>
<path d="M282 214 L330 214" stroke="#7048E8" stroke-width="2"/>
<rect x="330" y="186" width="290" height="56" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="475" y="220" text-anchor="middle" font-weight="700" fill="#5F3DC4">{$t[4]}</text>
<text x="475" y="120" text-anchor="middle" font-size="15" font-weight="700">{$t[5]}</text>
<text x="475" y="146" text-anchor="middle" font-size="22" fill="#7048E8">1 → 2 → 3 → 4 → 5 → 6</text>
</svg>
SVG;
    }
}

return [
    'id' => 'ux-mobile', 'family' => 'ux', 'after' => 'ux-base', 'categories' => ['design', 'mobile'],
    'title' => L('UX su mobile', 'UX on Mobile'), 'short' => L('UX mobile', 'Mobile UX'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Progettare per il telefono: contesto d’uso, zona del pollice e target touch, navigazione e gesti, moduli, permessi e notifiche, velocità percepita, accessibilità mobile, smartwatch, auto e voce.', 'Designing for the phone: context of use, thumb zone and touch targets, navigation and gestures, forms, permissions and notifications, perceived speed, mobile accessibility, watches, cars and voice.'),
    'lessons' => [
        lesson('context', L('Contesto e mobile first', 'Context and mobile first'), [
            P('Sul telefono si usa il prodotto **in movimento**, a sessioni brevi, spesso con una mano sola, con luce del sole, rete lenta e mille distrazioni. Per questo la UX mobile punta su **compiti rapidi**, contenuti essenziali e stati di caricamento chiari.', 'On the phone the product is used **on the move**, in short sessions, often one-handed, in sunlight, on slow networks and with a thousand distractions. That is why mobile UX focuses on **quick tasks**, essential content and clear loading states.'),
            P('Con l’approccio **mobile first** si progetta prima per lo schermo piccolo: obbliga a scegliere che cosa conta davvero, poi si aggiunge spazio e dettaglio sugli schermi grandi. È l’opposto del “togliere pezzi” al sito desktop, che di solito produce versioni mobile monche.', 'With the **mobile first** approach you design for the small screen first: it forces you to choose what really matters, then you add space and detail on larger screens. It is the opposite of “cutting pieces” off the desktop site, which usually produces crippled mobile versions.'),
            Tip('Progetta anche per gli stati imperfetti: offline, rete lenta, batteria scarsa, testo ingrandito dalle impostazioni di accessibilità.', 'Design for imperfect states too: offline, slow network, low battery, text enlarged in accessibility settings.'),
        ], [
            Q('c1', L('Com’è tipicamente una sessione su mobile?', 'What is a typical mobile session like?'), [L('Breve, in movimento, con distrazioni', 'Short, on the move, with distractions'), L('Lunga e alla scrivania', 'Long and at a desk'), L('Sempre con il Wi-Fi veloce', 'Always on fast Wi-Fi'), L('Sempre con due mani libere', 'Always with both hands free')], 0, L('Il contesto conta.', 'Context matters.')),
            Q('c2', L('Che cosa significa mobile first?', 'What does mobile first mean?'), [L('Progettare prima per lo schermo piccolo', 'Designing for the small screen first'), L('Fare solo la app, mai il sito', 'Making only the app, never the site'), L('Togliere pezzi al sito desktop', 'Cutting pieces off the desktop site'), L('Usare solo telefoni nuovi', 'Using only new phones')], 0, L('Obbliga a scegliere l’essenziale.', 'It forces choosing the essential.')),
            Q('c3', L('Quale stato va progettato anche su mobile?', 'Which state should also be designed on mobile?'), [L('Offline e rete lenta', 'Offline and slow network'), L('Solo il caso perfetto', 'Only the perfect case'), L('Solo la modalità aereo dei piloti', 'Only pilots’ aeroplane mode'), L('Nessuno', 'None')], 0, L('Il mondo reale è imperfetto.', 'The real world is imperfect.')),
        ], task('text', L('Scrivi tre situazioni reali in cui si usa un’app per i biglietti del treno, una per riga con `-`, tra cui una con **rete** scarsa.', 'Write three real situations in which a train ticket app is used, one per line with `-`, including one with a poor **network**.'), "- \n- \n- ", [re('^-\s*\S.*\n-\s*\S.*\n-\s*\S', L('Tre situazioni', 'Three situations')), re('rete|network|offline|segnale|signal|galleria|tunnel|connession|connection', L('Una con rete scarsa', 'One with a poor network'))], '', "- di corsa in stazione con la valigia in mano\n- in galleria senza segnale di rete al controllo del biglietto\n- al sole con lo schermo poco leggibile"), [
            dMatch(pMatch(), [[L('Una mano sola', 'One hand'), L('Controlli a portata di pollice', 'Controls within thumb reach')], [L('Rete lenta', 'Slow network'), L('Stati di caricamento chiari', 'Clear loading states')], [L('Luce del sole', 'Sunlight'), L('Contrasto alto', 'High contrast')], ['Mobile first', L('Prima lo schermo piccolo', 'Small screen first')]], L('Progettare per il contesto.', 'Designing for context.')),
            dFlash(pFlash(), [[L('Su mobile le sessioni sono spesso brevi', 'Mobile sessions are often short'), true], [L('Mobile first significa ignorare il desktop', 'Mobile first means ignoring desktop'), false], [L('Il testo ingrandito va previsto nel layout', 'Enlarged text should be planned in the layout'), true], [L('Su mobile la rete è sempre veloce', 'On mobile the network is always fast'), false]], L('Il telefono nel mondo reale.', 'The phone in the real world.')),
        ]),
        lesson('touch', L('Zona del pollice e target touch', 'Thumb zone and touch targets'), [
            P('La maggior parte delle persone tiene il telefono con una mano e usa il **pollice**: la parte bassa e centrale dello schermo è la più comoda, gli angoli in alto sono i più difficili. Per questo le azioni principali scendono in basso (barra di navigazione, pulsante principale) e quelle rare o pericolose stanno più in alto.', 'Most people hold the phone with one hand and use their **thumb**: the lower centre of the screen is the most comfortable, the top corners the hardest. That is why main actions move to the bottom (navigation bar, main button) and rare or dangerous ones sit higher up.'),
            Fig(L(fig_uxmob_thumb('it'), fig_uxmob_thumb('en')),
                L('A sinistra lo schermo di un telefono tenuto con la mano destra, diviso in zone: facile in basso al centro, con sforzo intorno, difficile negli angoli in alto. A destra una schermata che la rispetta: «Elimina account» in alto, il pulsante «Paga ora» e la barra di navigazione in basso.', 'On the left the screen of a phone held in the right hand, split into zones: easy at the lower centre, a stretch around it, hard in the top corners. On the right a screen that follows it: “Delete account” at the top, the “Pay now” button and the navigation bar at the bottom.'),
                L('Quello che si usa spesso va dove il pollice arriva senza sforzo.', 'What is used often goes where the thumb reaches without effort.')),
            P('Un dito è impreciso: i **target touch** devono essere grandi almeno **44×44 punti** (Apple) o **48×48 dp** (Material), con spazio tra un target e l’altro. Il target può essere più grande dell’icona visibile. Il tocco non ha **hover**: niente informazioni che compaiono solo passando sopra con il mouse.', 'A finger is imprecise: **touch targets** must be at least **44×44 points** (Apple) or **48×48 dp** (Material), with space between targets. The target can be larger than the visible icon. Touch has no **hover**: no information that appears only when hovering with a mouse.'),
            C('.icona-chiudi {
  width: 24px;      /* icona visibile */
  height: 24px;
  padding: 12px;    /* area toccabile: 48 × 48 */
}', 'css', '<span class="icona-chiudi" style="display:inline-block;background:#FFE8D6;border-radius:50%;outline:2px dashed #E8590C"><svg viewBox="0 0 24 24" width="24" height="24"><path d="M6 6l12 12M18 6L6 18" stroke="#1E1C52" stroke-width="2.5" stroke-linecap="round"/></svg></span> <code>24 + 12 + 12 = 48</code>'),
        ], [
            Q('t1', L('Quale zona dello schermo è più comoda per il pollice?', 'Which screen area is most comfortable for the thumb?'), [L('In basso al centro', 'Lower centre'), L('In alto a sinistra', 'Top left'), L('In alto a destra', 'Top right'), L('Tutte allo stesso modo', 'All equally')], 0, L('Lì vanno le azioni principali.', 'Main actions go there.')),
            Q('t2', L('Quanto deve essere grande almeno un target touch secondo Material?', 'How big must a touch target be at least per Material?'), ['48×48 dp', '16×16 dp', '24×24 dp', '100×100 dp'], 0, L('Apple indica 44×44 punti.', 'Apple says 44×44 points.')),
            Q('t3', L('Perché non affidarsi all’hover su mobile?', 'Why not rely on hover on mobile?'), [L('Sul touch l’hover non esiste', 'Hover does not exist on touch'), L('Rallenta il telefono', 'It slows the phone'), L('È vietato dagli store', 'Stores forbid it'), L('Consuma batteria', 'It drains battery')], 0, L('Il dito tocca, non sorvola.', 'A finger taps, it does not hover.')),
        ], task('css', L('Rendi il pulsante `.chiudi` un target touch di almeno 48×48: dagli `min-width: 48px` e `min-height: 48px`.', 'Make the `.chiudi` button a touch target of at least 48×48: give it `min-width: 48px` and `min-height: 48px`.'), ".chiudi {\n  font-size: 16px;\n}\n", [css('.chiudi', 'min-width', '48px', L('Largo almeno 48px', 'At least 48px wide')), css('.chiudi', 'min-height', '48px', L('Alto almeno 48px', 'At least 48px tall'))], '<button class="chiudi" aria-label="Chiudi">✕</button>', ".chiudi {\n  min-width: 48px;\n  min-height: 48px;\n}"), [
            dMatch(pMatch(), [[L('In basso al centro', 'Lower centre'), L('Azioni principali', 'Main actions')], [L('Angoli in alto', 'Top corners'), L('Azioni rare', 'Rare actions')], ['44×44 pt', 'Apple'], ['48×48 dp', 'Material']], L('Il pollice comanda.', 'The thumb rules.')),
            dBlanks(pFill(), ".tasto {\n  min-width: ___;\n  min-height: ___;\n}", ['48px', '48px'], ['12px', '200px'], L('Almeno 48 per lato.', 'At least 48 per side.')),
        ]),
        lesson('navigation', L('Navigazione e gesti', 'Navigation and gestures'), [
            P('La **tab bar** in basso (3-5 voci) è lo schema più chiaro per le sezioni principali: sempre visibile, a portata di pollice. Il **menu hamburger** nasconde le voci e le fa usare meno: va bene per le cose secondarie. Per le azioni dentro una schermata si usano pulsanti, **bottom sheet** e menu contestuali.', 'The bottom **tab bar** (3-5 items) is the clearest pattern for main sections: always visible, within thumb reach. The **hamburger menu** hides items and makes them used less: fine for secondary things. For actions within a screen you use buttons, **bottom sheets** and contextual menus.'),
            Fig(L(fig_uxmob_tabs('it'), fig_uxmob_tabs('en')),
                L('Due telefoni. Il primo ha solo l’icona del menu hamburger in alto a sinistra: le sezioni sono nascoste. Il secondo ha una barra in basso con quattro schede sempre visibili, Home, Cerca, Ordini e Profilo, con la scheda attiva evidenziata.', 'Two phones. The first has only the hamburger menu icon at the top left: the sections are hidden. The second has a bar at the bottom with four always-visible tabs, Home, Search, Orders and Profile, with the active tab highlighted.'),
                L('Ciò che non si vede si usa meno: le sezioni principali vanno in vista.', 'What is out of sight gets used less: main sections belong in view.')),
            P('I **gesti** (scorrere, trascinare, pizzicare, scorrere per eliminare) sono veloci ma **invisibili**: chi non li conosce non li scopre. Ogni gesto deve avere anche un’alternativa visibile (un pulsante, un menu), e non bisogna litigare con i gesti di sistema (scorrere dal bordo per tornare indietro, dal basso per la home).', '**Gestures** (scroll, drag, pinch, swipe to delete) are fast but **invisible**: those who do not know them will not discover them. Every gesture needs a visible alternative too (a button, a menu), and you must not fight system gestures (swipe from the edge to go back, from the bottom for home).'),
        ], [
            Q('n1', L('Quante voci ha di solito una tab bar?', 'How many items does a tab bar usually have?'), [L('Da 3 a 5', '3 to 5'), L('Da 8 a 12', '8 to 12'), L('Una sola', 'Just one'), L('Più di 20', 'More than 20')], 0, L('Poche e sempre visibili.', 'Few and always visible.')),
            Q('n2', L('Qual è il limite del menu hamburger?', 'What is the hamburger menu’s limit?'), [L('Nasconde le voci, che si usano meno', 'It hides items, which get used less'), L('È troppo colorato', 'It is too colourful'), L('Non funziona su Android', 'It does not work on Android'), L('Occupa tutto lo schermo sempre', 'It always fills the screen')], 0, L('Lontano dagli occhi…', 'Out of sight…')),
            Q('n3', L('Che cosa serve a ogni gesto personalizzato?', 'What does every custom gesture need?'), [L('Un’alternativa visibile', 'A visible alternative'), L('Un suono', 'A sound'), L('Una vibrazione forte', 'A strong vibration'), L('Un tutorial di dieci pagine', 'A ten-page tutorial')], 0, L('I gesti non si vedono.', 'Gestures are invisible.')),
        ], task('text', L('Un’app di ricette ha 7 sezioni: Home, Cerca, Preferiti, Lista spesa, Profilo, Impostazioni, Aiuto. Scrivi su una riga `Tab bar: ` con al massimo 5 voci e su un’altra `Altro: ` con le restanti.', 'A recipe app has 7 sections: Home, Search, Favourites, Shopping list, Profile, Settings, Help. Write one line `Tab bar: ` with at most 5 items and another `More: ` with the rest.'), "Tab bar: \nAltro: ", [re('^tab bar:\s*[^,\n]+(,[^,\n]+){2,4}\s*$', L('Da 3 a 5 voci nella tab bar', '3 to 5 items in the tab bar')), re('^(altro|more):[^\n]*(impostazion|settings|aiuto|help)', L('Le voci secondarie in Altro', 'Secondary items in More'))], '', "Tab bar: Home, Cerca, Preferiti, Lista spesa, Profilo\nAltro: Impostazioni, Aiuto"), [
            dMatch(pMatch(), [['Tab bar', L('Sezioni principali', 'Main sections')], ['Hamburger', L('Voci secondarie', 'Secondary items')], ['Bottom sheet', L('Azioni dal basso', 'Actions from the bottom')], [L('Scorri per eliminare', 'Swipe to delete'), L('Gesto con alternativa', 'Gesture with an alternative')]], L('Muoversi nell’app.', 'Moving around the app.')),
            dFlash(pFlash(), [[L('I gesti personalizzati sono difficili da scoprire', 'Custom gestures are hard to discover'), true], [L('Conviene sovrascrivere il gesto indietro di sistema', 'It is wise to override the system back gesture'), false], [L('La tab bar resta visibile tra le sezioni', 'The tab bar stays visible across sections'), true], [L('Il menu hamburger aumenta l’uso delle voci', 'The hamburger menu increases item use'), false]], L('Navigazione chiara.', 'Clear navigation.')),
        ]),
        lesson('forms', L('Moduli, permessi e notifiche', 'Forms, permissions and notifications'), [
            P('Scrivere sul telefono è faticoso: chiedi **meno campi** possibile, usa il **tipo di tastiera** giusto (`type="email"`, `type="tel"`, `inputmode="numeric"`), l’**autocompletamento** (`autocomplete="postal-code"`), etichette sempre visibili e validazione mentre si scrive. Accedere con passkey o con un link è meglio che digitare password.', 'Typing on a phone is tiring: ask for as **few fields** as possible, use the right **keyboard type** (`type="email"`, `type="tel"`, `inputmode="numeric"`), **autofill** (`autocomplete="postal-code"`), always-visible labels and validation as you type. Signing in with a passkey or a link beats typing passwords.'),
            P('I **permessi** (posizione, fotocamera, notifiche) vanno chiesti **nel momento giusto**, quando l’utente capisce perché servono (“Per trovare i negozi vicini…”), non tutti all’avvio. Le **notifiche** devono essere utili e controllabili: troppe portano a disattivarle o a disinstallare l’app.', '**Permissions** (location, camera, notifications) should be requested **at the right moment**, when the user understands why they are needed (“To find nearby shops…”), not all at launch. **Notifications** must be useful and controllable: too many lead people to turn them off or uninstall the app.'),
        ], [
            Q('f1', L('Quale attributo mostra la tastiera numerica?', 'Which attribute shows the numeric keypad?'), ['inputmode="numeric"', 'type="text"', 'lang="num"', 'role="number"'], 0, L('Anche type="tel" per i telefoni.', 'Also type="tel" for phones.')),
            Q('f2', L('Quando chiedere il permesso della posizione?', 'When to ask for location permission?'), [L('Quando serve e se ne capisce il motivo', 'When needed and its reason is clear'), L('Appena si apre l’app', 'As soon as the app opens'), L('Mai, si attiva da sola', 'Never, it turns on by itself'), L('Dopo l’acquisto', 'After purchase')], 0, L('Contesto prima della richiesta.', 'Context before the request.')),
            Q('f3', L('Che rischio hanno troppe notifiche?', 'What is the risk of too many notifications?'), [L('Vengono disattivate o l’app disinstallata', 'They get turned off or the app uninstalled'), L('Il telefono si rompe', 'The phone breaks'), L('Migliora la retention', 'Retention improves'), L('Nessuno', 'None')], 0, L('Poche e utili.', 'Few and useful.')),
        ], task('html', L('Completa il modulo: il campo email deve avere `type="email"` e `autocomplete="email"`, il telefono `type="tel"`.', 'Complete the form: the email field must have `type="email"` and `autocomplete="email"`, the phone `type="tel"`.'), "<label for=\"m\">Email</label>\n<input id=\"m\">\n<label for=\"t\">Telefono</label>\n<input id=\"t\">", [has('input[type="email"][autocomplete="email"]', L('Email con tastiera e autocompletamento', 'Email with keyboard and autofill')), has('input[type="tel"]', L('Telefono con tastiera numerica', 'Phone with numeric keypad'))], '', "<input id=\"m\" type=\"email\" autocomplete=\"email\">\n<input id=\"t\" type=\"tel\" autocomplete=\"tel\">"), [
            dMatch(pMatch(), [['type="email"', L('Tastiera con @', 'Keyboard with @')], ['type="tel"', L('Tastierino telefonico', 'Phone keypad')], ['autocomplete', L('Compila in automatico', 'Fills in automatically')], [L('Permesso nel contesto', 'Permission in context'), L('Spiega il perché', 'Explains why')]], L('Moduli comodi.', 'Comfortable forms.')),
            dBug(pBug(), ['<label for="c">CAP</label>', '<input id="c" type="password" autocomplete="postal-code">'], 1, '<input id="c" inputmode="numeric" autocomplete="postal-code">', L('Il CAP vuole la tastiera numerica, non un campo password.', 'A postcode wants the numeric keypad, not a password field.')),
        ]),
        lesson('performance', L('Velocità percepita e offline', 'Perceived speed and offline'), [
            P('Su mobile la rete è lenta e instabile. Conta la **velocità percepita**: mostrare subito uno **scheletro** della pagina (skeleton), caricare prima ciò che si vede, dare un riscontro immediato a ogni tocco. Uno spinner senza fine fa sembrare tutto rotto.', 'On mobile the network is slow and unstable. **Perceived speed** matters: show a page **skeleton** right away, load what is visible first, give immediate feedback to every tap. An endless spinner makes everything feel broken.'),
            Fig(L(fig_uxmob_skeleton('it'), fig_uxmob_skeleton('en')),
                L('Due telefoni in caricamento. Nel primo c’è solo uno spinner che gira al centro di uno schermo vuoto. Nel secondo uno skeleton: blocchi grigi che pulsano piano al posto del titolo, dell’immagine, del testo e di un commento, già nella forma della pagina.', 'Two phones loading. The first shows only a spinner turning in the middle of an empty screen. The second shows a skeleton: grey blocks gently pulsing where the heading, image, text and a comment will be, already in the shape of the page.'),
                L('Lo skeleton fa sembrare l’attesa più breve perché mostra subito che cosa sta arrivando.', 'A skeleton makes the wait feel shorter because it shows at once what is coming.')),
            P('Pensa all’**offline**: salva le bozze sul dispositivo, metti in coda le azioni e inviale quando torna la rete, dì chiaramente cosa è disponibile senza connessione. L’**aggiornamento ottimistico** mostra subito il risultato (un like) e lo annulla solo se il server rifiuta.', 'Think about **offline**: save drafts on the device, queue actions and send them when the network returns, say clearly what is available without a connection. An **optimistic update** shows the result at once (a like) and undoes it only if the server refuses.'),
        ], [
            Q('pf1', L('Che cos’è uno skeleton?', 'What is a skeleton?'), [L('La sagoma della pagina mentre carica', 'The page outline while loading'), L('Un errore', 'An error'), L('Un font', 'A font'), L('Un’animazione di festa', 'A celebration animation')], 0, L('Sembra già quasi pronta.', 'It already looks almost ready.')),
            Q('pf2', L('Che cosa fa un aggiornamento ottimistico?', 'What does an optimistic update do?'), [L('Mostra subito il risultato e lo annulla se fallisce', 'Shows the result at once and undoes it if it fails'), L('Aspetta sempre il server', 'Always waits for the server'), L('Ignora gli errori', 'Ignores errors'), L('Ricarica la pagina', 'Reloads the page')], 0, L('Reattività percepita.', 'Perceived responsiveness.')),
            Q('pf3', L('Come gestisci un’azione fatta offline?', 'How do you handle an action done offline?'), [L('La metti in coda e la invii dopo', 'Queue it and send it later'), L('La cancelli', 'Delete it'), L('Blocchi l’app', 'Block the app'), L('Mostri un errore e basta', 'Just show an error')], 0, L('Nulla va perso.', 'Nothing is lost.')),
        ], task('text', L('Scrivi due righe con `-`: una sullo skeleton durante il caricamento e una sulla coda offline.', 'Write two `-` lines: one on the skeleton while loading and one on the offline queue.'), '', [re('^-.*(skeleton|scheletro|sagoma)', L('Parli dello skeleton', 'You mention the skeleton')), re('^-.*(coda|queue|offline|senza rete)', L('Parli della coda offline', 'You mention the offline queue'))], '', "- Mostro uno skeleton della lista mentre carica\n- Le azioni fatte offline vanno in coda e partono al ritorno della rete"), [
            dMatch(pMatch(), [['Skeleton', L('Sagoma durante il caricamento', 'Outline while loading')], [L('Ottimistico', 'Optimistic'), L('Risultato subito', 'Result at once')], [L('Coda offline', 'Offline queue'), L('Invio al ritorno della rete', 'Sent when back online')], [L('Bozza locale', 'Local draft'), L('Nulla va perso', 'Nothing is lost')]], L('Mobile reattivo.', 'Responsive mobile.')),
            dFlash(pFlash(), [[L('Un riscontro immediato al tocco migliora la percezione', 'Immediate tap feedback improves perception'), true], [L('Uno spinner infinito rassicura', 'An endless spinner reassures'), false], [L('Le bozze si possono salvare sul dispositivo', 'Drafts can be saved on the device'), true], [L('Offline l’app non deve dire nulla', 'Offline the app should say nothing'), false]], L('Veloce anche quando non lo è.', 'Fast even when it is not.')),
        ]),
        lesson('accessibility', L('Accessibilità su mobile', 'Mobile accessibility'), [
            P('Su telefono molte persone usano un **lettore di schermo**: **VoiceOver** su iOS e **TalkBack** su Android. Si scorre con un dito per ascoltare gli elementi uno alla volta e si tocca due volte per attivarli. Perché funzioni, ogni controllo ha bisogno di un’**etichetta** che dica che cosa fa («Aggiungi ai preferiti», non «pulsante 3»), di un **ruolo** (pulsante, interruttore) e di un **ordine di lettura** logico.', 'On phones many people use a **screen reader**: **VoiceOver** on iOS and **TalkBack** on Android. You swipe with one finger to hear elements one at a time and double-tap to activate them. For it to work, every control needs a **label** saying what it does (“Add to favourites”, not “button 3”), a **role** (button, switch) and a logical **reading order**.'),
            Fig(L(fig_uxmob_reader('it'), fig_uxmob_reader('en')),
                L('La schermata di un prodotto con sei elementi numerati nell’ordine in cui li legge lo screen reader: indietro, immagine, nome del prodotto, prezzo, pulsante dei preferiti, pulsante per aggiungere al carrello. Il pulsante con il solo cuore viene annunciato come «Aggiungi ai preferiti, pulsante».', 'A product screen with six elements numbered in the order the screen reader reads them: back, image, product name, price, favourites button, add to basket button. The heart-only button is announced as “Add to favourites, button”.'),
                L('Etichetta e ruolo fanno capire anche un pulsante fatto solo di un’icona.', 'A label and a role make even an icon-only button understandable.')),
            P('Altre regole: il layout deve reggere il **testo ingrandito** (Dynamic Type, dimensione del carattere di Android) senza tagliare parole; i gesti complessi hanno un’alternativa semplice (le WCAG chiedono un’alternativa al **trascinamento** e ai gesti a più dita o a percorso); l’app funziona sia in **verticale** sia in **orizzontale**; i target sono grandi e distanziati. Poi prova davvero: attiva VoiceOver o TalkBack e completa un compito senza guardare lo schermo.', 'Other rules: the layout must cope with **enlarged text** (Dynamic Type, Android font size) without cutting words; complex gestures have a simple alternative (WCAG asks for an alternative to **dragging** and to multi-finger or path gestures); the app works in both **portrait** and **landscape**; targets are large and well spaced. Then really try it: turn on VoiceOver or TalkBack and complete a task without looking at the screen.'),
            C('<button aria-label="Aggiungi ai preferiti">
  <svg aria-hidden="true" viewBox="0 0 24 24">…</svg>
</button>', 'html'),
        ], [
            Q('m1', L('Come si chiama il lettore di schermo di Android?', 'What is Android’s screen reader called?'), ['VoiceOver', L('Assistente vocale', 'Narrator'), 'TalkBack', 'Siri'], 2, L('VoiceOver è quello di iOS.', 'VoiceOver is the iOS one.')),
            Q('m2', L('Che cosa serve a un pulsante con solo un’icona?', 'What does an icon-only button need?'), [L('Un colore più acceso', 'A brighter colour'), L('Un’etichetta che dica che cosa fa', 'A label saying what it does'), L('Un’animazione al tocco', 'An animation on tap'), L('Un bordo più spesso', 'A thicker border')], 1, L('Il lettore di schermo legge l’etichetta.', 'The screen reader reads the label.')),
            Q('m3', L('Che cosa chiedono le WCAG per un’azione che si fa trascinando?', 'What does WCAG ask for an action done by dragging?'), [L('Niente di particolare', 'Nothing in particular'), L('Un suono di conferma', 'A confirmation sound'), L('Di toglierla dall’app', 'Removing it from the app'), L('Un’alternativa senza trascinamento', 'An alternative without dragging')], 3, L('Per esempio pulsanti su e giù per riordinare.', 'For example up and down buttons to reorder.')),
        ], task('html', L('Il pulsante ha solo un’icona: aggiungi al `<button>` un `aria-label` come «Aggiungi ai preferiti» e all’`<svg>` `aria-hidden="true"`.', 'The button has only an icon: add an `aria-label` such as “Aggiungi ai preferiti” to the `<button>` and `aria-hidden="true"` to the `<svg>`.'), "<button class=\"preferito\">\n  <svg viewBox=\"0 0 24 24\" width=\"24\" height=\"24\"><path d=\"M12 21 4 13a5 5 0 0 1 8-6 5 5 0 0 1 8 6z\"/></svg>\n</button>", [has('button[aria-label]:not([aria-label=""])', L('Il pulsante ha un’etichetta', 'The button has a label')), has('button svg[aria-hidden="true"]', L('L’icona è nascosta ai lettori di schermo', 'The icon is hidden from screen readers'))], '', "<button class=\"preferito\" aria-label=\"Aggiungi ai preferiti\">\n  <svg aria-hidden=\"true\" viewBox=\"0 0 24 24\" width=\"24\" height=\"24\"><path d=\"M12 21 4 13a5 5 0 0 1 8-6 5 5 0 0 1 8 6z\"/></svg>\n</button>"), [
            dMatch(pMatch(), [['VoiceOver', 'iOS'], ['TalkBack', 'Android'], [L('Doppio tocco', 'Double tap'), L('Attiva l’elemento', 'Activates the element')], [L('Etichetta', 'Label'), L('Dice che cosa fa il controllo', 'Says what the control does')], ['aria-hidden', L('Nasconde le decorazioni', 'Hides decorations')]], L('Leggere lo schermo.', 'Reading the screen.')),
            dFlash(pFlash(), [[L('Il layout deve reggere il testo ingrandito', 'The layout must cope with enlarged text'), true], [L('Un’app può funzionare solo in verticale senza motivo', 'An app may work only in portrait for no reason'), false], [L('Provare con TalkBack attivo trova problemi reali', 'Testing with TalkBack on finds real problems'), true], [L('«Pulsante 3» è un’etichetta chiara', '“Button 3” is a clear label'), false]], L('Mobile per tutti.', 'Mobile for everyone.')),
        ]),
        lesson('beyond', L('Smartwatch, auto e voce', 'Watches, cars and voice'), [
            P('Il mobile non è solo il telefono. Sullo **smartwatch** un’interazione dura pochi secondi: informazioni **a colpo d’occhio**, testi brevissimi, una o due azioni, notifiche che si gestiscono con un tocco. I compiti lunghi restano sul telefono, che può riprendere da dove ci si è fermati.', 'Mobile is not just the phone. On a **smartwatch** an interaction lasts a few seconds: **glanceable** information, very short text, one or two actions, notifications handled with a tap. Long tasks stay on the phone, which can pick up where you left off.'),
            P('In **auto** (CarPlay, Android Auto) la regola è la sicurezza: lo schermo usa modelli approvati con pochi elementi grandi, niente testi lunghi né tastiera durante la guida, e la **voce** diventa l’input principale. Un’interfaccia vocale conferma che cosa ha capito, offre poche scelte alla volta e permette di correggere («No, intendevo Marco Rossi»).', 'In the **car** (CarPlay, Android Auto) the rule is safety: the screen uses approved templates with a few large elements, no long text or keyboard while driving, and **voice** becomes the main input. A voice interface confirms what it understood, offers few choices at a time and lets people correct it (“No, I meant Marco Rossi”).'),
        ], [
            Q('b1', L('Quanto dura di solito un’interazione con lo smartwatch?', 'How long does a smartwatch interaction usually last?'), [L('Mezz’ora', 'Half an hour'), L('Pochi secondi', 'A few seconds'), L('Un’ora', 'An hour'), L('Tutta la giornata', 'All day')], 1, L('A colpo d’occhio.', 'At a glance.')),
            Q('b2', L('Che cosa evitare in un’app per l’auto durante la guida?', 'What should a car app avoid while driving?'), [L('I comandi vocali', 'Voice commands'), L('Le icone grandi', 'Large icons'), L('Testi lunghi e tastiera', 'Long text and the keyboard'), L('Pochi elementi per schermata', 'Few elements per screen')], 2, L('Occhi sulla strada.', 'Eyes on the road.')),
            Q('b3', L('Che cosa deve fare un’interfaccia vocale dopo un comando?', 'What should a voice interface do after a command?'), [L('Confermare che cosa ha capito', 'Confirm what it understood'), L('Restare in silenzio', 'Stay silent'), L('Elencare venti opzioni', 'List twenty options'), L('Chiedere la password', 'Ask for the password')], 0, L('E permettere di correggere.', 'And allow corrections.')),
        ], task('text', L('Riscrivi questa notifica per lo smartwatch su una riga che inizia con `Watch:`, con al massimo 40 caratteri dopo i due punti: «Il tuo ordine numero 48213 è stato spedito e arriverà domani tra le 9 e le 13 presso il tuo indirizzo di casa».', 'Rewrite this notification for a smartwatch on one line starting with `Watch:`, with at most 40 characters after the colon: “Your order number 48213 has been shipped and will arrive tomorrow between 9 and 13 at your home address”.'), 'Watch: ', [re('^Watch:[ \t]*\S.{3,39}$', L('Breve, al massimo 40 caratteri', 'Short, 40 characters at most')), re('domani|tomorrow|\b9\b|\b13\b', L('Dice quando arriva', 'Says when it arrives'))], '', 'Watch: Ordine spedito, arriva domani 9-13'), [
            dMatch(pMatch(), [['Smartwatch', L('A colpo d’occhio', 'At a glance')], [L('Auto', 'Car'), L('Pochi elementi grandi', 'Few large elements')], [L('Voce', 'Voice'), L('Conferma che cosa ha capito', 'Confirms what it understood')], [L('Telefono', 'Phone'), L('Compiti lunghi', 'Long tasks')]], L('Il dispositivo giusto per il compito.', 'The right device for the task.')),
            dFlash(pFlash(), [[L('Sullo smartwatch bastano una o due azioni', 'On a watch one or two actions are enough'), true], [L('In auto conviene scrivere testi lunghi', 'In the car long text is a good idea'), false], [L('Un’interfaccia vocale deve permettere di correggere', 'A voice interface must allow corrections'), true], [L('Lo smartwatch sostituisce il telefono in ogni compito', 'The watch replaces the phone for every task'), false]], L('Oltre il telefono.', 'Beyond the phone.')),
        ]),
    ],
];
