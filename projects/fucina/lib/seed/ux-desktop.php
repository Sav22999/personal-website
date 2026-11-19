<?php
/** UX on desktop: large screens and layouts, pointer and keyboard, power users and shortcuts, windows and multitasking, data tables, learning complex software, web and native apps. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uxdesk_listdetail')) {
    /** A desktop window in three panes: navigation, list and detail, with the text column kept narrow. */
    function fig_uxdesk_listdetail(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Navigazione', 'Elenco', 'Dettaglio', 'Posta in arrivo', 'Inviati', 'Bozze', 'Archivio', 'max 60-80 caratteri per riga']
            : ['Navigation', 'List', 'Detail', 'Inbox', 'Sent', 'Drafts', 'Archive', 'max 60-80 characters per line'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 310" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<rect x="10" y="30" width="620" height="270" rx="12" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<path d="M10 42 a12 12 0 0 1 12 -12 h596 a12 12 0 0 1 12 12 v14 h-620 z" fill="#F1F0F8"/>
<g fill="#8A87B0"><circle cx="28" cy="43" r="5"/><circle cx="44" cy="43" r="5"/><circle cx="60" cy="43" r="5"/></g>
<rect x="11" y="56" width="139" height="243" fill="#F1F0F8"/>
<g font-size="14" font-weight="700" text-anchor="middle"><text x="80" y="20" fill="#5F3DC4">{$t[0]}</text><text x="245" y="20" fill="#1B66BC">{$t[1]}</text><text x="485" y="20" fill="#0B7F5C">{$t[2]}</text></g>
<rect x="22" y="70" width="118" height="28" rx="8" fill="#ECE6FF"/>
<g><text x="32" y="89" font-weight="700" fill="#5F3DC4">{$t[3]}</text><text x="32" y="122">{$t[4]}</text><text x="32" y="150">{$t[5]}</text><text x="32" y="178">{$t[6]}</text></g>
<line x1="340" y1="56" x2="340" y2="299" stroke="#E4E2F0" stroke-width="2"/>
<rect x="160" y="66" width="170" height="52" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g fill="#E4E2F0"><rect x="172" y="78" width="90" height="10" rx="5"/><rect x="172" y="96" width="140" height="8" rx="4"/>
<rect x="172" y="136" width="90" height="10" rx="5"/><rect x="172" y="154" width="130" height="8" rx="4"/>
<rect x="172" y="186" width="90" height="10" rx="5"/><rect x="172" y="204" width="140" height="8" rx="4"/>
<rect x="172" y="236" width="90" height="10" rx="5"/><rect x="172" y="254" width="120" height="8" rx="4"/></g>
<rect x="172" y="78" width="90" height="10" rx="5" fill="#2F8CF0"/>
<rect x="364" y="70" width="180" height="16" rx="8" fill="#1E1C52"/>
<g fill="#E4E2F0"><rect x="364" y="104" width="230" height="8" rx="4"/><rect x="364" y="120" width="220" height="8" rx="4"/><rect x="364" y="136" width="230" height="8" rx="4"/><rect x="364" y="152" width="160" height="8" rx="4"/>
<rect x="364" y="176" width="230" height="8" rx="4"/><rect x="364" y="192" width="210" height="8" rx="4"/><rect x="364" y="208" width="120" height="8" rx="4"/></g>
<g stroke="#0B7F5C" stroke-width="2"><line x1="364" y1="236" x2="594" y2="236"/><line x1="364" y1="230" x2="364" y2="242"/><line x1="594" y1="230" x2="594" y2="242"/></g>
<text x="479" y="258" text-anchor="middle" fill="#0B7F5C" font-weight="700">{$t[7]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxdesk_palette')) {
    /** A command palette opened with Ctrl/Cmd+K: a search field and matching commands with their shortcuts. */
    function fig_uxdesk_palette(string $lg): string
    {
        $t = $lg === 'it'
            ? ['espo', 'Esporta come PDF', 'Esporta come immagine', 'Esposizione: regola', 'Cerca un comando per nome', '↑↓ per scegliere, Invio per eseguire']
            : ['expo', 'Export as PDF', 'Export as image', 'Exposure: adjust', 'Find a command by name', '↑↓ to choose, Enter to run'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="10" y="10" width="620" height="280" rx="12" fill="#F1F0F8"/>
<g fill="#E4E2F0"><rect x="30" y="30" width="120" height="240" rx="8"/><rect x="170" y="30" width="440" height="20" rx="6"/><rect x="170" y="62" width="300" height="12" rx="6"/></g>
<rect x="130" y="56" width="380" height="206" rx="14" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<circle cx="157" cy="84" r="7" fill="none" stroke="#5A5880" stroke-width="2"/><path d="M162 89 l6 6" stroke="#5A5880" stroke-width="2" stroke-linecap="round"/>
<text x="178" y="90" font-size="16">{$t[0]}</text><rect x="214" y="74" width="2" height="20" fill="#2F8CF0"/>
<rect x="430" y="72" width="66" height="24" rx="6" fill="#F1F0F8"/><text x="463" y="89" text-anchor="middle" font-size="13" fill="#5A5880">Ctrl K</text>
<line x1="130" y1="108" x2="510" y2="108" stroke="#E4E2F0" stroke-width="2"/>
<rect x="140" y="116" width="360" height="34" rx="8" fill="#DCEBFF"/>
<text x="156" y="138" font-weight="700" fill="#1B66BC">{$t[1]}</text><text x="488" y="138" text-anchor="end" font-size="13" fill="#1B66BC">Ctrl Shift E</text>
<text x="156" y="176">{$t[2]}</text>
<text x="156" y="210">{$t[3]}</text>
<text x="320" y="246" text-anchor="middle" font-size="13" fill="#5A5880">{$t[5]}</text>
<text x="320" y="282" text-anchor="middle" font-size="13" fill="#5F3DC4" font-weight="700">{$t[4]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxdesk_table')) {
    /** The anatomy of a data table: sticky header, sorted column, right-aligned numbers, selection and a bulk action bar. */
    function fig_uxdesk_table(string $lg): string
    {
        $t = $lg === 'it'
            ? ['3 selezionati', 'Archivia', 'Elimina', 'Cliente', 'Data', 'Importo', 'Rossi srl', 'Bianchi spa', 'Verdi &amp; C.', 'Neri snc', 'intestazione fissa', 'ordinata per data', 'numeri allineati a destra', 'azioni di massa', '12 mar', '9 mar', '7 mar', '2 mar', '1.250,00', '980,50', '12.400,00', '75,00']
            : ['3 selected', 'Archive', 'Delete', 'Customer', 'Date', 'Amount', 'Smith Ltd', 'Brown plc', 'Green &amp; Co', 'Black Ltd', 'sticky header', 'sorted by date', 'numbers aligned right', 'bulk actions', '12 Mar', '9 Mar', '7 Mar', '2 Mar', '1,250.00', '980.50', '12,400.00', '75.00'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="20" width="420" height="40" rx="10" fill="#1E1C52"/>
<text x="40" y="45" font-weight="700" fill="#FFFFFF">{$t[0]}</text>
<rect x="210" y="28" width="96" height="24" rx="6" fill="#FFFFFF"/><text x="258" y="45" text-anchor="middle" font-size="13">{$t[1]}</text>
<rect x="316" y="28" width="96" height="24" rx="6" fill="#FFE3EE"/><text x="364" y="45" text-anchor="middle" font-size="13" fill="#C2255C">{$t[2]}</text>
<rect x="20" y="70" width="420" height="36" rx="8" fill="#F1F0F8"/>
<g font-weight="700"><text x="74" y="93">{$t[3]}</text><text x="250" y="93" fill="#1B66BC">{$t[4]} ↓</text><text x="424" y="93" text-anchor="end">{$t[5]}</text></g>
<g fill="none" stroke="#8A87B0" stroke-width="2"><rect x="38" y="81" width="16" height="16" rx="4"/></g>
<g font-size="14">
<rect x="20" y="110" width="420" height="40" fill="#DCEBFF"/><rect x="20" y="152" width="420" height="40" fill="#DCEBFF"/>
<rect x="20" y="236" width="420" height="40" fill="#DCEBFF"/>
<g fill="#2F8CF0"><rect x="38" y="122" width="16" height="16" rx="4"/><rect x="38" y="164" width="16" height="16" rx="4"/><rect x="38" y="248" width="16" height="16" rx="4"/></g>
<g stroke="#FFFFFF" stroke-width="2" fill="none"><path d="M42 130 l3 3 l6 -6"/><path d="M42 172 l3 3 l6 -6"/><path d="M42 256 l3 3 l6 -6"/></g>
<rect x="38" y="206" width="16" height="16" rx="4" fill="none" stroke="#8A87B0" stroke-width="2"/>
<text x="74" y="135">{$t[6]}</text><text x="74" y="177">{$t[7]}</text><text x="74" y="219">{$t[8]}</text><text x="74" y="261">{$t[9]}</text>
<g fill="#5A5880"><text x="250" y="135">{$t[14]}</text><text x="250" y="177">{$t[15]}</text><text x="250" y="219">{$t[16]}</text><text x="250" y="261">{$t[17]}</text></g>
<g text-anchor="end" font-variant-numeric="tabular-nums"><text x="424" y="135">{$t[18]}</text><text x="424" y="177">{$t[19]}</text><text x="424" y="219">{$t[20]}</text><text x="424" y="261">{$t[21]}</text></g>
</g>
<g stroke="#E8590C" stroke-width="2"><line x1="444" y1="40" x2="470" y2="40"/><line x1="444" y1="88" x2="470" y2="88"/><line x1="444" y1="198" x2="470" y2="198"/></g>
<line x1="250" y1="102" x2="320" y2="102" stroke="#E8590C" stroke-width="2"/>
<g font-size="13" font-weight="700" fill="#B84508"><text x="476" y="45">{$t[13]}</text><text x="476" y="84">{$t[10]}</text><text x="476" y="194">{$t[12]}</text><text x="250" y="300">↑ {$t[11]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uxdesk_disclosure')) {
    /** Progressive disclosure: a short export panel, and the same panel with “More options” opened. */
    function fig_uxdesk_disclosure(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Esporta', 'Formato', 'Qualità', 'Altre opzioni', 'Meno opzioni', 'Profilo colore', 'Metadati', 'Margini di stampa', 'Esporta', 'All’inizio: l’essenziale', 'Su richiesta: il resto']
            : ['Export', 'Format', 'Quality', 'More options', 'Fewer options', 'Colour profile', 'Metadata', 'Print margins', 'Export', 'At first: the essentials', 'On request: the rest'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 340" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-size="15" font-weight="700" text-anchor="middle"><text x="160" y="24">{$t[9]}</text><text x="480" y="24">{$t[10]}</text></g>
<rect x="40" y="40" width="240" height="210" rx="12" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<text x="60" y="70" font-size="16" font-weight="700">{$t[0]}</text>
<text x="60" y="102">{$t[1]}</text><rect x="150" y="86" width="110" height="24" rx="6" fill="#F1F0F8"/><text x="160" y="103" font-size="13">PDF ▾</text>
<text x="60" y="138">{$t[2]}</text><rect x="150" y="128" width="110" height="6" rx="3" fill="#E4E2F0"/><rect x="150" y="128" width="80" height="6" rx="3" fill="#2F8CF0"/><circle cx="230" cy="131" r="7" fill="#2F8CF0"/>
<text x="60" y="176" fill="#1B66BC" font-weight="700">▸ {$t[3]}</text>
<rect x="150" y="204" width="110" height="32" rx="8" fill="#E8590C"/><text x="205" y="225" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[8]}</text>
<path d="M296 150 h28" stroke="#8A87B0" stroke-width="2"/><path d="M324 144 l8 6 l-8 6 z" fill="#8A87B0"/>
<rect x="360" y="40" width="240" height="290" rx="12" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<text x="380" y="70" font-size="16" font-weight="700">{$t[0]}</text>
<text x="380" y="102">{$t[1]}</text><rect x="470" y="86" width="110" height="24" rx="6" fill="#F1F0F8"/><text x="480" y="103" font-size="13">PDF ▾</text>
<text x="380" y="138">{$t[2]}</text><rect x="470" y="128" width="110" height="6" rx="3" fill="#E4E2F0"/><rect x="470" y="128" width="80" height="6" rx="3" fill="#2F8CF0"/><circle cx="550" cy="131" r="7" fill="#2F8CF0"/>
<text x="380" y="176" fill="#1B66BC" font-weight="700">▾ {$t[4]}</text>
<rect x="372" y="188" width="216" height="92" rx="8" fill="#F1F0F8"/>
<g font-size="13"><text x="386" y="212">{$t[5]}</text><text x="386" y="238">{$t[6]}</text><text x="386" y="264">{$t[7]}</text></g>
<g fill="#8A87B0"><rect x="540" y="202" width="36" height="14" rx="7"/><rect x="540" y="228" width="36" height="14" rx="7"/><rect x="540" y="254" width="36" height="14" rx="7"/></g>
<rect x="470" y="288" width="110" height="32" rx="8" fill="#E8590C"/><text x="525" y="309" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[8]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'ux-desktop', 'family' => 'ux', 'after' => 'ux-base', 'categories' => ['design', 'desktop'],
    'title' => L('UX su desktop', 'UX on Desktop'), 'short' => L('UX desktop', 'Desktop UX'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Progettare per schermi grandi: layout e densità, mouse e menu contestuali, tastiera e utenti esperti, finestre e multitasking, tabelle e annulla, software complessi da imparare un po’ alla volta, web app, PWA e app native.', 'Designing for large screens: layout and density, mouse and context menus, keyboard and power users, windows and multitasking, tables and undo, complex software learned step by step, web apps, PWAs and native apps.'),
    'lessons' => [
        lesson('layout', L('Schermi grandi, layout e densità', 'Large screens, layout and density'), [
            P('Sul desktop si lavora seduti, a lungo, spesso su compiti complessi: scrivere, analizzare dati, gestire molti elementi. Lo spazio permette **più informazioni in vista** e layout a più colonne: barra laterale di navigazione, area principale, pannello di dettaglio (lo schema **lista-dettaglio** di email e file manager).', 'On desktop people work seated, for long stretches, often on complex tasks: writing, analysing data, managing many items. The space allows **more information in view** and multi-column layouts: side navigation bar, main area, detail panel (the **list-detail** pattern of email clients and file managers).'),
            Fig(L(fig_uxdesk_listdetail('it'), fig_uxdesk_listdetail('en')),
                L('La finestra di un programma di posta in tre colonne: a sinistra la navigazione con posta in arrivo, inviati, bozze e archivio; al centro l’elenco dei messaggi con uno selezionato; a destra il dettaglio del messaggio, con le righe di testo limitate a 60-80 caratteri.', 'An email client window in three columns: on the left the navigation with inbox, sent, drafts and archive; in the middle the list of messages with one selected; on the right the message detail, with lines of text limited to 60-80 characters.'),
                L('Lo spazio in più serve a vedere più cose insieme, non a fare righe infinite.', 'Extra space is for seeing more things at once, not for endless lines.')),
            P('Più spazio non vuol dire riempirlo: i testi lunghi si leggono male oltre **60-80 caratteri per riga**, quindi si limita la larghezza delle colonne di testo. La **densità** si adatta all’uso: un’app per professionisti può essere compatta (tabelle fitte), una per il pubblico più ariosa; alcune app offrono entrambe le modalità.', 'More space does not mean filling it: long text reads poorly beyond **60-80 characters per line**, so the width of text columns is limited. **Density** adapts to use: a professional app can be compact (dense tables), a consumer one more airy; some apps offer both modes.'),
            C('.articolo {
  max-width: 70ch;   /* circa 70 caratteri per riga */
  margin-inline: auto;
}', 'css', L('<div class="articolo" style="background:#F1F0F8;padding:8px;font:16px/1.5 system-ui"><p>Su uno schermo largo questa colonna non supera i 70 caratteri circa per riga: l’occhio torna a capo senza perdersi e il testo resta comodo da leggere anche quando la finestra è molto più larga.</p></div>', '<div class="articolo" style="background:#F1F0F8;padding:8px;font:16px/1.5 system-ui"><p>On a wide screen this column never goes beyond about 70 characters per line: the eye finds the start of the next line without getting lost, and the text stays comfortable to read even when the window is much wider.</p></div>')),
        ], [
            Q('l1', L('Che cos’è lo schema lista-dettaglio?', 'What is the list-detail pattern?'), [L('Una lista e accanto il dettaglio dell’elemento scelto', 'A list with the chosen item’s detail beside it'), L('Una lista della spesa', 'A shopping list'), L('Un menu a tendina', 'A dropdown menu'), L('Una sola colonna centrata', 'A single centred column')], 0, L('Come le app di posta.', 'Like email apps.')),
            Q('l2', L('Quanti caratteri per riga si leggono meglio?', 'How many characters per line read best?'), [L('Circa 60-80', 'About 60-80'), L('Circa 200', 'About 200'), L('Meno di 10', 'Fewer than 10'), L('Tutta la larghezza dello schermo', 'The full screen width')], 0, L('Righe troppo lunghe stancano.', 'Lines too long tire the eye.')),
            Q('l3', L('Quando ha senso una densità compatta?', 'When does compact density make sense?'), [L('In app per professionisti con molti dati', 'In professional apps with lots of data'), L('Sempre, per tutti', 'Always, for everyone'), L('Mai su desktop', 'Never on desktop'), L('Solo nei giochi', 'Only in games')], 0, L('La densità segue l’uso.', 'Density follows use.')),
        ], task('css', L('Limita la colonna `.testo` a `max-width: 70ch` e centrala con `margin-inline: auto`.', 'Limit the `.testo` column to `max-width: 70ch` and centre it with `margin-inline: auto`.'), ".testo {\n  font-size: 18px;\n}\n", [re('\.testo\s*\{[^}]*max-width\s*:\s*70ch', L('Larghezza massima 70ch', 'Max width 70ch')), re('\.testo\s*\{[^}]*margin(-inline)?\s*:\s*(0\s+)?auto', L('Centrata', 'Centred'))], '<article class="testo"><p>Un lungo articolo…</p></article>', ".testo {\n  max-width: 70ch;\n  margin-inline: auto;\n}"), [
            dMatch(pMatch(), [[L('Lista-dettaglio', 'List-detail'), L('Email e file manager', 'Email and file managers')], ['70ch', L('Righe leggibili', 'Readable lines')], [L('Densità compatta', 'Compact density'), L('Molti dati in vista', 'Lots of data in view')], [L('Barra laterale', 'Sidebar'), L('Navigazione sempre visibile', 'Always-visible navigation')]], L('Usare lo spazio.', 'Using the space.')),
            dFlash(pFlash(), [[L('Sul desktop si svolgono spesso compiti lunghi', 'Long tasks are common on desktop'), true], [L('Più spazio va sempre riempito tutto', 'More space must always be filled entirely'), false], [L('Le righe di testo troppo lunghe si leggono male', 'Text lines that are too long read poorly'), true], [L('Il desktop non ha bisogno di gerarchia visiva', 'Desktop does not need visual hierarchy'), false]], L('Schermi grandi, scelte chiare.', 'Big screens, clear choices.')),
        ]),
        lesson('pointer', L('Mouse, hover e menu contestuali', 'Mouse, hover and context menus'), [
            P('Il **mouse** è preciso: i target possono essere più piccoli che sul touch (ma non minuscoli: almeno 24×24 px). L’**hover** è un aiuto prezioso: cambio di cursore e di colore sugli elementi cliccabili, **tooltip** sulle icone, anteprime. Mai però nascondere azioni essenziali solo dietro l’hover: devono funzionare anche da tastiera e touch (molti laptop hanno lo schermo touch).', 'The **mouse** is precise: targets can be smaller than on touch (but not tiny: at least 24×24 px). **Hover** is a valuable aid: cursor and colour change on clickable elements, **tooltips** on icons, previews. Never hide essential actions only behind hover, though: they must also work by keyboard and touch (many laptops have touch screens).'),
            P('Il **clic destro** apre il **menu contestuale** con le azioni sull’elemento indicato: gli utenti desktop lo cercano, ma va affiancato da un’alternativa visibile. Il **trascinamento** (drag and drop) è naturale per spostare file, riordinare liste, caricare allegati: mostra dove si può rilasciare e permetti di annullare.', '**Right-click** opens the **context menu** with actions on the pointed item: desktop users look for it, but it should come with a visible alternative. **Drag and drop** is natural for moving files, reordering lists, uploading attachments: show where you can drop and allow undo.'),
        ], [
            Q('p1', L('Che cosa non va mai nascosto solo dietro l’hover?', 'What should never be hidden only behind hover?'), [L('Le azioni essenziali', 'Essential actions'), L('Le decorazioni', 'Decorations'), L('Le ombre', 'Shadows'), L('Le animazioni', 'Animations')], 0, L('Tastiera e touch non hanno hover.', 'Keyboard and touch have no hover.')),
            Q('p2', L('Che cosa apre il clic destro?', 'What does right-click open?'), [L('Il menu contestuale', 'The context menu'), L('Il browser', 'The browser'), L('Le impostazioni di sistema', 'System settings'), L('Una nuova finestra sempre', 'A new window, always')], 0, L('Azioni sull’elemento.', 'Actions on the item.')),
            Q('p3', L('Che cosa serve per un buon drag and drop?', 'What does good drag and drop need?'), [L('Mostrare dove rilasciare e poter annullare', 'Showing where to drop and allowing undo'), L('Nessuna indicazione visiva', 'No visual cues'), L('Un tempo limite', 'A time limit'), L('Il doppio clic', 'A double-click')], 0, L('Feedback e sicurezza.', 'Feedback and safety.')),
        ], task('css', L('Dai alle `.riga` un effetto hover: con `.riga:hover` imposta `background` e `cursor: pointer`.', 'Give `.riga` a hover effect: with `.riga:hover` set a `background` and `cursor: pointer`.'), ".riga {\n  padding: 8px 12px;\n}\n", [re('\.riga:hover\s*\{[^}]*background', L('Sfondo al passaggio', 'Background on hover')), re('cursor\s*:\s*pointer', L('Cursore a mano', 'Hand cursor'))], '<div class="riga">Fattura 12</div>', ".riga:hover {\n  background: #eef2ff;\n  cursor: pointer;\n}"), [
            dMatch(pMatch(), [['Hover', L('Anteprime e tooltip', 'Previews and tooltips')], [L('Clic destro', 'Right-click'), L('Menu contestuale', 'Context menu')], ['Drag and drop', L('Spostare e riordinare', 'Move and reorder')], ['cursor: pointer', L('Indica ciò che si clicca', 'Shows what is clickable')]], L('Il puntatore.', 'The pointer.')),
            dFlash(pFlash(), [[L('Molti laptop hanno anche lo schermo touch', 'Many laptops also have touch screens'), true], [L('Sul desktop un target può essere di 4 pixel', 'On desktop a target can be 4 pixels'), false], [L('I tooltip aiutano a capire le icone', 'Tooltips help explain icons'), true], [L('Il menu contestuale basta come unica via', 'The context menu suffices as the only way'), false]], L('Mouse con giudizio.', 'Mouse with judgement.')),
        ]),
        lesson('keyboard', L('Tastiera, scorciatoie e utenti esperti', 'Keyboard, shortcuts and power users'), [
            P('Sul desktop molti lavorano con la **tastiera**: tutto deve essere raggiungibile con `Tab` in un ordine logico, con il **focus sempre visibile**, `Invio` e `Spazio` per attivare, `Esc` per chiudere finestre e menu. È anche un requisito di accessibilità.', 'On desktop many people work with the **keyboard**: everything must be reachable with `Tab` in a logical order, with **always-visible focus**, `Enter` and `Space` to activate, `Esc` to close dialogs and menus. It is also an accessibility requirement.'),
            P('Gli **utenti esperti** vogliono velocità: **scorciatoie** coerenti con le convenzioni (`Ctrl/Cmd+S` salva, `Ctrl/Cmd+Z` annulla, `Ctrl/Cmd+K` apre la ricerca o la **command palette**), mostrate accanto alle voci di menu perché si imparino, **azioni multiple** su più elementi selezionati e preferenze personalizzabili. Il principio: semplice per chi inizia, veloce per chi è esperto.', '**Power users** want speed: **shortcuts** consistent with conventions (`Ctrl/Cmd+S` saves, `Ctrl/Cmd+Z` undoes, `Ctrl/Cmd+K` opens search or the **command palette**), shown next to menu items so they get learned, **bulk actions** on several selected items and customisable preferences. The principle: simple for beginners, fast for experts.'),
            Fig(L(fig_uxdesk_palette('it'), fig_uxdesk_palette('en')),
                L('Una palette dei comandi aperta sopra un’applicazione con Ctrl K: nel campo di ricerca è scritto «espo» e sotto compaiono i comandi che corrispondono, il primo evidenziato con la sua scorciatoia da tastiera accanto.', 'A command palette opened over an application with Ctrl K: the search field reads “expo” and the matching commands appear below, the first highlighted with its keyboard shortcut next to it.'),
                L('Si cerca un comando per nome e intanto se ne impara la scorciatoia.', 'You find a command by name and learn its shortcut along the way.')),
            Tip('Non rubare le scorciatoie di sistema o del browser (come `Ctrl+W` o `Ctrl+T`): l’utente si aspetta che facciano quello che fanno sempre.', 'Do not steal system or browser shortcuts (like `Ctrl+W` or `Ctrl+T`): users expect them to do what they always do.'),
        ], [
            Q('k1', L('Quale tasto chiude di solito una finestra di dialogo?', 'Which key usually closes a dialog?'), ['Esc', 'Tab', 'Shift', 'F5'], 0, L('Una convenzione universale.', 'A universal convention.')),
            Q('k2', L('Che cos’è una command palette?', 'What is a command palette?'), [L('Una ricerca di comandi da tastiera', 'A keyboard command search'), L('Una tavolozza di colori', 'A colour palette'), L('Un menu a icone', 'An icon menu'), L('Un tipo di grafico', 'A kind of chart')], 0, L('Spesso con Ctrl/Cmd+K.', 'Often with Ctrl/Cmd+K.')),
            Q('k3', L('Perché mostrare le scorciatoie accanto alle voci di menu?', 'Why show shortcuts next to menu items?'), [L('Perché gli utenti le imparino', 'So users learn them'), L('Per riempire lo spazio', 'To fill space'), L('Per obbligo di legge', 'By legal obligation'), L('Per nasconderle', 'To hide them')], 0, L('Dal menu alla velocità.', 'From menu to speed.')),
        ], task('text', L('Scrivi le scorciatoie convenzionali per tre azioni, una per riga nel formato `azione: scorciatoia`: salva, annulla, cerca.', 'Write the conventional shortcuts for three actions, one per line in the format `action: shortcut`: save, undo, search.'), "salva: \nannulla: \ncerca: ", [re('^(salva|save)\s*:\s*(ctrl|cmd|⌘)\s*\+\s*s\b', L('Salva', 'Save')), re('^(annulla|undo)\s*:\s*(ctrl|cmd|⌘)\s*\+\s*z\b', L('Annulla', 'Undo')), re('^(cerca|search)\s*:\s*(ctrl|cmd|⌘)\s*\+\s*(k|f)\b', L('Cerca', 'Search'))], '', "salva: Ctrl+S\nannulla: Ctrl+Z\ncerca: Ctrl+K"), [
            dMatch(pMatch(), [['Ctrl/Cmd+S', L('Salva', 'Save')], ['Ctrl/Cmd+Z', L('Annulla', 'Undo')], ['Ctrl/Cmd+K', L('Command palette', 'Command palette')], ['Esc', L('Chiude il dialogo', 'Closes the dialog')], ['Tab', L('Elemento successivo', 'Next element')]], L('Scorciatoie che tutti conoscono.', 'Shortcuts everyone knows.')),
            dFlash(pFlash(), [[L('Il focus da tastiera deve essere sempre visibile', 'Keyboard focus must always be visible'), true], [L('È una buona idea ridefinire Ctrl+W', 'Redefining Ctrl+W is a good idea'), false], [L('Le azioni multiple aiutano gli utenti esperti', 'Bulk actions help power users'), true], [L('Le scorciatoie rendono inutili i menu', 'Shortcuts make menus useless'), false]], L('Veloci senza escludere.', 'Fast without excluding.')),
        ]),
        lesson('windows', L('Finestre e multitasking', 'Windows and multitasking'), [
            P('Sul desktop l’app convive con altre: finestre affiancate, ridimensionate, monitor multipli. Il layout deve essere **reattivo** anche alle finestre strette (non solo ai telefoni) e ricordare dimensioni e posizione. Le app desktop native seguono le convenzioni del sistema: **barra dei menu** su macOS, menu e barra del titolo su Windows, scorciatoie e finestre di dialogo standard.', 'On desktop the app lives alongside others: side-by-side windows, resized, multiple monitors. The layout must be **responsive** to narrow windows too (not just phones) and remember size and position. Native desktop apps follow system conventions: the **menu bar** on macOS, menus and title bar on Windows, standard shortcuts and dialogs.'),
            P('Il lavoro si interrompe spesso: **salvataggio automatico**, stato ripristinato alla riapertura, schede o documenti multipli, **annulla a più livelli**. Le operazioni lunghe (esportazioni, caricamenti) vanno in background con avanzamento visibile, senza bloccare l’intera interfaccia; le notifiche di sistema avvisano quando finiscono.', 'Work is often interrupted: **autosave**, state restored on reopening, multiple tabs or documents, **multi-level undo**. Long operations (exports, uploads) run in the background with visible progress, without blocking the whole interface; system notifications announce when they finish.'),
        ], [
            Q('w1', L('A quali finestre deve adattarsi il layout desktop?', 'Which windows should a desktop layout adapt to?'), [L('Anche a quelle strette e affiancate', 'Also narrow, side-by-side ones'), L('Solo a schermo intero', 'Only full screen'), L('Solo a 1920 pixel', 'Only 1920 pixels'), L('A nessuna', 'None')], 0, L('Il multitasking stringe le finestre.', 'Multitasking narrows windows.')),
            Q('w2', L('Come gestire un’esportazione lunga?', 'How to handle a long export?'), [L('In background con avanzamento visibile', 'In the background with visible progress'), L('Bloccando tutta l’app', 'Blocking the whole app'), L('Senza dire nulla', 'Without saying anything'), L('Chiedendo di non toccare il mouse', 'Asking not to touch the mouse')], 0, L('L’utente continua a lavorare.', 'The user keeps working.')),
            Q('w3', L('Perché il salvataggio automatico?', 'Why autosave?'), [L('Il lavoro viene spesso interrotto', 'Work is often interrupted'), L('Per occupare disco', 'To use disk space'), L('Per rallentare l’app', 'To slow the app'), L('Non serve', 'It is not needed')], 0, L('Niente lavoro perso.', 'No lost work.')),
        ], task('text', L('Scrivi tre comportamenti che un editor di documenti desktop dovrebbe avere per il multitasking e le interruzioni, uno per riga con `-`, tra cui il **salvataggio automatico**.', 'Write three behaviours a desktop document editor should have for multitasking and interruptions, one per line with `-`, including **autosave**.'), "- \n- \n- ", [re('^-\s*\S.*\n-\s*\S.*\n-\s*\S', L('Tre comportamenti', 'Three behaviours')), re('salvataggio automatico|autosave|auto-save|salva da solo|automatic sav', L('Il salvataggio automatico', 'Autosave'))], '', "- salvataggio automatico ogni pochi secondi\n- riapre i documenti e la posizione di prima\n- l’esportazione in PDF continua in background"), [
            dMatch(pMatch(), [[L('Salvataggio automatico', 'Autosave'), L('Niente lavoro perso', 'No lost work')], [L('Ripristino dello stato', 'State restore'), L('Si riparte da dove eri', 'Resume where you were')], [L('Lavori in background', 'Background jobs'), L('L’interfaccia resta libera', 'The interface stays free')], [L('Barra dei menu', 'Menu bar'), L('Convenzione di macOS', 'macOS convention')]], L('Convivere con le altre app.', 'Living with other apps.')),
            dFlash(pFlash(), [[L('Un’app desktop dovrebbe ricordare la dimensione della finestra', 'A desktop app should remember window size'), true], [L('Un’operazione lunga deve bloccare l’interfaccia', 'A long operation must block the interface'), false], [L('L’annulla a più livelli dà sicurezza', 'Multi-level undo gives confidence'), true], [L('Le convenzioni di sistema vanno ignorate', 'System conventions should be ignored'), false]], L('Finestre ben educate.', 'Well-behaved windows.')),
        ]),
        lesson('data', L('Tabelle, azioni in blocco e annulla', 'Tables, bulk actions and undo'), [
            P('Le app desktop gestiscono spesso **molti dati**. Una buona tabella ha intestazioni fisse, ordinamento per colonna, filtri, colonne ridimensionabili e numeri allineati a destra. La **selezione multipla** (con Maiusc e Ctrl/Cmd) permette azioni in blocco: archivia 30 email insieme.', 'Desktop apps often handle **lots of data**. A good table has sticky headers, sorting by column, filters, resizable columns and right-aligned numbers. **Multiple selection** (with Shift and Ctrl/Cmd) allows bulk actions: archive 30 emails at once.'),
            Fig(L(fig_uxdesk_table('it'), fig_uxdesk_table('en')),
                L('Una tabella di fatture con quattro righe, tre selezionate con la casella spuntata. In alto una barra scura dice «3 selezionati» con i pulsanti Archivia ed Elimina. L’intestazione resta fissa, la colonna Data ha una freccia che indica l’ordinamento e gli importi sono allineati a destra.', 'A table of invoices with four rows, three selected with their boxes ticked. At the top a dark bar says “3 selected” with Archive and Delete buttons. The header stays fixed, the Date column has an arrow showing the sort order and the amounts are aligned right.'),
                L('Con i numeri allineati a destra le cifre si confrontano a colpo d’occhio.', 'With numbers aligned right, the digits can be compared at a glance.')),
            P('Per le azioni distruttive preferisci **Annulla** (un avviso con “Annulla” per qualche secondo) a una finestra “Sei sicuro?”: la conferma si clicca per abitudine, l’annulla salva davvero dall’errore. Ctrl/Cmd+Z dovrebbe funzionare ovunque abbia senso.', 'For destructive actions prefer **Undo** (a notice with “Undo” for a few seconds) to an “Are you sure?” dialog: confirmation gets clicked out of habit, undo really saves you from the mistake. Ctrl/Cmd+Z should work wherever it makes sense.'),
        ], [
            Q('dt1', L('Come si allineano i numeri in una tabella?', 'How are numbers aligned in a table?'), [L('A destra', 'Right'), L('A sinistra', 'Left'), L('Al centro', 'Centre'), L('A caso', 'Randomly')], 0, L('Così le cifre si confrontano.', 'So digits line up.')),
            Q('dt2', L('Perché preferire Annulla a “Sei sicuro?”', 'Why prefer Undo to “Are you sure?”'), [L('La conferma si clicca per abitudine', 'Confirmation gets clicked out of habit'), L('Annulla è più bello', 'Undo is prettier'), L('Le conferme sono vietate', 'Confirmations are forbidden'), L('Non c’è differenza', 'There is no difference')], 0, L('L’annulla rimedia davvero.', 'Undo really fixes it.')),
            Q('dt3', L('Come selezioni un intervallo di righe?', 'How do you select a range of rows?'), [L('Clic, poi Maiusc+clic', 'Click, then Shift+click'), L('Doppio clic', 'Double-click'), L('Tasto destro', 'Right-click'), L('Trascinando la finestra', 'Dragging the window')], 0, L('Ctrl/Cmd per righe sparse.', 'Ctrl/Cmd for scattered rows.')),
        ], task('text', L('Scrivi il testo di un avviso dopo un’eliminazione, con il numero di elementi e la parola `Annulla`.', 'Write the text of a notice after a deletion, with the number of items and the word `Undo`.'), '', [re('\d+.*(eliminat|cancellat|archiviat|deleted)', L('Dici cosa è successo', 'You say what happened')), re('\b(Annulla|Undo)\b', L('Offri Annulla', 'You offer Undo'))], '', '3 email eliminate. Annulla'), [
            dMatch(pMatch(), [[L('Intestazione fissa', 'Sticky header'), L('Resta visibile scorrendo', 'Stays visible while scrolling')], [L('Maiusc+clic', 'Shift+click'), L('Intervallo', 'Range')], [L('Ctrl/Cmd+clic', 'Ctrl/Cmd+click'), L('Righe sparse', 'Scattered rows')], [L('Annulla', 'Undo'), L('Rimedia all’errore', 'Fixes the mistake')], ['Ctrl/Cmd+Z', L('Annulla da tastiera', 'Keyboard undo')]], L('Lavorare con i dati.', 'Working with data.')),
            dFlash(pFlash(), [[L('Le azioni in blocco fanno risparmiare tempo', 'Bulk actions save time'), true], [L('“Sei sicuro?” protegge meglio di Annulla', '“Are you sure?” protects better than Undo'), false], [L('Le colonne ridimensionabili aiutano con molti dati', 'Resizable columns help with lots of data'), true], [L('I numeri vanno centrati', 'Numbers should be centred'), false]], L('Dati su grande schermo.', 'Data on a big screen.')),
        ]),
        lesson('learning', L('Software complessi: imparare un po’ alla volta', 'Complex software: learning step by step'), [
            P('Le app desktop professionali (editor, gestionali, strumenti di grafica) hanno centinaia di funzioni. La **divulgazione progressiva** mostra prima l’essenziale e rivela il resto quando serve: impostazioni avanzate dietro «Altre opzioni», pannelli che compaiono solo quando si seleziona qualcosa, menu con le voci più usate in alto.', 'Professional desktop apps (editors, management tools, graphics software) have hundreds of features. **Progressive disclosure** shows the essentials first and reveals the rest when needed: advanced settings behind “More options”, panels that appear only when something is selected, menus with the most used items at the top.'),
            Fig(L(fig_uxdesk_disclosure('it'), fig_uxdesk_disclosure('en')),
                L('Due versioni di un pannello «Esporta». La prima mostra solo formato, qualità, il link «Altre opzioni» e il pulsante. La seconda, con il link aperto, aggiunge profilo colore, metadati e margini di stampa in un riquadro grigio.', 'Two versions of an “Export” panel. The first shows only format, quality, the “More options” link and the button. The second, with the link opened, adds colour profile, metadata and print margins in a grey box.'),
                L('Chi ne ha bisogno trova tutto, chi non ne ha bisogno non viene distratto.', 'Those who need it find everything, those who do not are not distracted.')),
            P('Per insegnare senza bloccare: **suggerimenti contestuali** che compaiono la prima volta che si usa una funzione, **modelli** e progetti di esempio per non partire dal foglio bianco, una **command palette** che fa trovare un comando cercandolo per nome, una guida cercabile dentro l’app. Evita i tour obbligatori di dieci passaggi: si saltano e si dimenticano.', 'To teach without blocking: **contextual tips** that appear the first time a feature is used, **templates** and sample projects so nobody starts from a blank page, a **command palette** to find a command by searching its name, a searchable help inside the app. Avoid mandatory ten-step tours: people skip them and forget them.'),
        ], [
            Q('o1', L('Che cos’è la divulgazione progressiva?', 'What is progressive disclosure?'), [L('Mostrare tutte le funzioni subito', 'Showing every feature at once'), L('Mostrare l’essenziale e rivelare il resto quando serve', 'Showing the essentials and revealing the rest when needed'), L('Nascondere per sempre le funzioni avanzate', 'Hiding advanced features forever'), L('Un tutorial obbligatorio all’avvio', 'A mandatory tutorial at launch')], 1, L('Semplice per chi inizia, completo per chi è esperto.', 'Simple for beginners, complete for experts.')),
            Q('o2', L('Perché offrire modelli e progetti di esempio?', 'Why offer templates and sample projects?'), [L('Per occupare spazio su disco', 'To take up disk space'), L('Per imporre uno stile unico', 'To impose a single style'), L('Per non partire dal foglio bianco', 'So nobody starts from a blank page'), L('Per vendere più licenze', 'To sell more licences')], 2, L('Si impara modificando.', 'You learn by editing.')),
            Q('o3', L('Che problema hanno i tour obbligatori lunghi?', 'What is the problem with long mandatory tours?'), [L('Sono troppo brevi', 'They are too short'), L('Funzionano solo su Mac', 'They only work on Mac'), L('Insegnano troppo in fretta', 'They teach too quickly'), L('Si saltano e si dimenticano', 'People skip and forget them')], 3, L('Meglio aiutare nel momento giusto.', 'Better to help at the right moment.')),
        ], task('text', L('Una finestra di esportazione ha 12 opzioni. Scrivi due righe: `Sempre visibili:` con al massimo 3 opzioni separate da virgole e `Altre opzioni:` con il resto (puoi riassumere).', 'An export dialog has 12 options. Write two lines: `Always visible:` with at most 3 comma-separated options and `More options:` with the rest (you may summarise).'), "Sempre visibili: \nAltre opzioni: ", [re('^(sempre visibili|always visible)[ \t]*:[ \t]*[^,\n]*\w[^,\n]*(,[^,\n]+){0,2}[ \t]*$', L('Al massimo 3 opzioni in vista', 'At most 3 visible options')), re('^(altre opzioni|more options)[ \t]*:[ \t]*\S', L('Il resto sotto «Altre opzioni»', 'The rest under “More options”'))], '', "Sempre visibili: formato, qualità, cartella\nAltre opzioni: profilo colore, metadati, compressione, nome dei file"), [
            dMatch(pMatch(), [[L('Divulgazione progressiva', 'Progressive disclosure'), L('L’essenziale prima', 'Essentials first')], [L('Suggerimento contestuale', 'Contextual tip'), L('Appare al primo uso', 'Appears on first use')], [L('Modello', 'Template'), L('Punto di partenza pronto', 'Ready starting point')], ['Command palette', L('Trovi un comando per nome', 'Find a command by name')]], L('Imparare senza fatica.', 'Learning without effort.')),
            dFlash(pFlash(), [[L('Le opzioni avanzate possono stare dietro «Altre opzioni»', 'Advanced options can sit behind “More options”'), true], [L('Mostrare tutto subito aiuta chi inizia', 'Showing everything at once helps beginners'), false], [L('Un suggerimento al primo uso insegna nel momento giusto', 'A tip on first use teaches at the right moment'), true], [L('Una guida cercabile è inutile nelle app desktop', 'Searchable help is useless in desktop apps'), false]], L('Complessità sotto controllo.', 'Complexity under control.')),
        ]),
        lesson('web-native', L('Web app, PWA e app native', 'Web apps, PWAs and native apps'), [
            P('Sul desktop un prodotto può essere una **web app** nel browser, una **PWA** installabile (icona propria, finestra separata, funziona anche offline), un’app **ibrida** (Electron, Tauri: tecnologie web dentro una finestra nativa) o un’app **nativa** (SwiftUI o AppKit su Mac, WinUI su Windows). Ogni scelta cambia ciò che l’utente si aspetta.', 'On desktop a product can be a **web app** in the browser, an installable **PWA** (its own icon, a separate window, works offline too), a **hybrid** app (Electron, Tauri: web technologies inside a native window) or a **native** app (SwiftUI or AppKit on Mac, WinUI on Windows). Each choice changes what users expect.'),
            P('In una finestra propria spariscono la barra degli indirizzi e i pulsanti del browser: l’app deve offrire da sola **indietro**, ricarica e link condivisibili. Gli utenti si aspettano poi comportamenti nativi: **menu** dell’app, scorciatoie di sistema, trascinare file dal desktop, notifiche di sistema, avvio rapido. Un’app ibrida che si comporta come una pagina web (testo selezionabile ovunque, menu del browser con il tasto destro) sembra «finta».', 'In its own window the address bar and browser buttons disappear: the app must provide **back**, reload and shareable links by itself. Users then expect native behaviours: app **menus**, system shortcuts, dragging files from the desktop, system notifications, fast start-up. A hybrid app that behaves like a web page (text selectable everywhere, the browser menu on right-click) feels “fake”.'),
        ], [
            Q('wn1', L('Che cos’è una PWA?', 'What is a PWA?'), [L('Un linguaggio di programmazione', 'A programming language'), L('Un tipo di monitor', 'A kind of monitor'), L('Una web app installabile che può funzionare offline', 'An installable web app that can work offline'), L('Un plugin di un programma di grafica', 'A plug-in for a graphics program')], 2, L('Progressive Web App.', 'Progressive Web App.')),
            Q('wn2', L('Che cosa usa un’app ibrida fatta con Electron?', 'What does a hybrid app built with Electron use?'), [L('Tecnologie web dentro una finestra nativa', 'Web technologies inside a native window'), L('Solo codice assembly', 'Only assembly code'), L('Solo il terminale', 'Only the terminal'), L('Un emulatore Android', 'An Android emulator')], 0, L('Come Visual Studio Code.', 'Like Visual Studio Code.')),
            Q('wn3', L('Che cosa deve offrire una web app installata, senza la barra del browser?', 'What must an installed web app offer, without the browser bar?'), [L('Più pubblicità', 'More advertising'), L('Un suo indietro e link condivisibili', 'Its own back button and shareable links'), L('Solo il tema scuro', 'Only a dark theme'), L('Una finestra più piccola', 'A smaller window')], 1, L('Il browser non c’è più ad aiutare.', 'The browser is no longer there to help.')),
        ], task('text', L('Scrivi tre comportamenti nativi che un utente si aspetta da un’app desktop installata, uno per riga con `-`, tra cui le **scorciatoie** da tastiera.', 'Write three native behaviours a user expects from an installed desktop app, one per line with `-`, including keyboard **shortcuts**.'), "- \n- \n- ", [re('^-[ \t]*\S.*\n-[ \t]*\S.*\n-[ \t]*\S', L('Tre comportamenti', 'Three behaviours')), re('scorciatoi|shortcut', L('Le scorciatoie', 'Shortcuts'))], '', "- scorciatoie da tastiera come Ctrl+S\n- trascinare file dal desktop nella finestra\n- notifiche di sistema quando finisce un’esportazione"), [
            dMatch(pMatch(), [['PWA', L('Web app installabile', 'Installable web app')], ['Electron', L('Web in una finestra nativa', 'Web in a native window')], ['WinUI', L('App native per Windows', 'Native Windows apps')], ['AppKit', L('App native per Mac', 'Native Mac apps')]], L('Modi di arrivare sul desktop.', 'Ways onto the desktop.')),
            dFlash(pFlash(), [[L('Una PWA può avere una finestra propria', 'A PWA can have its own window'), true], [L('In una finestra propria il browser offre ancora il suo indietro', 'In its own window the browser still provides its back button'), false], [L('Gli utenti desktop si aspettano il trascinamento dei file', 'Desktop users expect file drag and drop'), true], [L('Un’app ibrida deve comportarsi come una pagina web', 'A hybrid app should behave like a web page'), false]], L('Sentirsi a casa.', 'Feeling at home.')),
        ]),
    ],
];
