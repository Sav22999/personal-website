<?php
/** UI basics: visual hierarchy, colour and contrast, typography, spacing and grids, Gestalt, buttons, links and fields, responsive layout. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uibase_hierarchy')) {
    /** The same card twice: everything equally loud, then with a clear hierarchy and one main action. */
    function fig_uibase_hierarchy(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Senza gerarchia', 'Con gerarchia', 'Corso di ceramica', 'Sabato 10:00 · 3 ore', '45 €', 'Prenota', 'Dettagli', 'tutto grida: dove guardo?', 'prima il titolo, poi l’azione']
            : ['No hierarchy', 'With hierarchy', 'Pottery class', 'Saturday 10:00 · 3 hours', '£45', 'Book', 'Details', 'everything shouts: where do I look?', 'first the title, then the action'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-weight="700" text-anchor="middle"><text x="160" y="24">{$t[0]}</text><text x="480" y="24">{$t[1]}</text></g>
<rect x="30" y="40" width="260" height="200" rx="14" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<g font-weight="700" fill="#B84508"><text x="50" y="76">{$t[2]}</text><text x="50" y="104">{$t[3]}</text><text x="50" y="132">{$t[4]}</text></g>
<rect x="50" y="170" width="105" height="44" rx="10" fill="#B84508"/><rect x="165" y="170" width="105" height="44" rx="10" fill="#B84508"/>
<g font-weight="700" fill="#FFFFFF" text-anchor="middle"><text x="102" y="197">{$t[5]}</text><text x="217" y="197">{$t[6]}</text></g>
<rect x="350" y="40" width="260" height="200" rx="14" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="370" y="80" font-size="17" font-weight="800">{$t[2]}</text>
<text x="370" y="106" font-size="14" fill="#5A5880">{$t[3]}</text>
<text x="370" y="140" font-size="17" font-weight="700">{$t[4]}</text>
<rect x="370" y="170" width="130" height="44" rx="10" fill="#B84508"/>
<text x="435" y="197" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[5]}</text>
<text x="550" y="197" text-anchor="middle" fill="#1B66BC" text-decoration="underline">{$t[6]}</text>
<g font-size="14" font-weight="700" text-anchor="middle"><text x="160" y="270" fill="#C2255C">✗ {$t[7]}</text><text x="480" y="270" fill="#0B7F5C">✓ {$t[8]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uibase_contrast')) {
    /** Text and background pairs with their contrast ratio against the WCAG thresholds. */
    function fig_uibase_contrast(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Testo di esempio', '✓ ok per tutto', '✓ ok, al limite', '✗ illeggibile', '✓ solo testo grande', '✓ ok', 'minimo 4,5:1 per il testo normale, 3:1 per quello grande', '15,6:1', '4,5:1', '2,3:1', '3,6:1']
            : ['Sample text', '✓ fine for all', '✓ fine, just', '✗ unreadable', '✓ large text only', '✓ fine', 'at least 4.5:1 for normal text, 3:1 for large text', '15.6:1', '4.5:1', '2.3:1', '3.6:1'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#E4E2F0" stroke-width="2">
<rect x="20" y="20" width="190" height="80" rx="12" fill="#FFFFFF"/><rect x="225" y="20" width="190" height="80" rx="12" fill="#FFFFFF"/><rect x="430" y="20" width="190" height="80" rx="12" fill="#FFFFFF"/></g>
<rect x="20" y="130" width="190" height="80" rx="12" fill="#E8590C"/><rect x="225" y="130" width="190" height="80" rx="12" fill="#0B7F5C"/><rect x="430" y="130" width="190" height="80" rx="12" fill="#FFD43B"/>
<g font-weight="700" text-anchor="middle">
<text x="115" y="56" fill="#1E1C52">{$t[0]}</text><text x="320" y="56" fill="#767676">{$t[0]}</text><text x="525" y="56" fill="#ABABAB">{$t[0]}</text>
<text x="115" y="166" fill="#FFFFFF">{$t[0]}</text><text x="320" y="166" fill="#FFFFFF">{$t[0]}</text><text x="525" y="166" fill="#1E1C52">{$t[0]}</text>
<text x="115" y="84" font-size="14" fill="#5A5880">{$t[7]}</text><text x="320" y="84" font-size="14" fill="#5A5880">{$t[8]}</text><text x="525" y="84" font-size="14" fill="#5A5880">{$t[9]}</text>
<text x="115" y="194" font-size="14" fill="#FFFFFF">{$t[10]}</text><text x="320" y="194" font-size="14" fill="#FFFFFF">5:1</text><text x="525" y="194" font-size="14" fill="#1E1C52">11:1</text></g>
<g font-size="13" font-weight="700" text-anchor="middle">
<text x="115" y="117" fill="#0B7F5C">{$t[1]}</text><text x="320" y="117" fill="#0B7F5C">{$t[2]}</text><text x="525" y="117" fill="#C2255C">{$t[3]}</text>
<text x="115" y="227" fill="#B84508">{$t[4]}</text><text x="320" y="227" fill="#0B7F5C">{$t[5]}</text><text x="525" y="227" fill="#0B7F5C">{$t[1]}</text></g>
<text x="320" y="252" text-anchor="middle" font-size="13" fill="#5A5880">{$t[6]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uibase_scale')) {
    /** A type scale with ratio 1.25: each step is the previous one times 1.25. */
    function fig_uibase_scale(string $lg): string
    {
        $t = $lg === 'it'
            ? ['nota', 'testo', 'titolo 3', 'titolo 2', 'titolo 1', '× 1,25 a ogni gradino']
            : ['caption', 'body', 'heading 3', 'heading 2', 'heading 1', '× 1.25 at every step'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<line x1="20" y1="150" x2="620" y2="150" stroke="#E4E2F0" stroke-width="2"/>
<g font-weight="700"><text x="40" y="150" font-size="13">Aa</text><text x="120" y="150" font-size="16">Aa</text><text x="210" y="150" font-size="20">Aa</text><text x="315" y="150" font-size="25">Aa</text><text x="440" y="150" font-size="31">Aa</text></g>
<g fill="#5A5880" font-size="13"><text x="40" y="178">13 px</text><text x="120" y="178">16 px</text><text x="210" y="178">20 px</text><text x="315" y="178">25 px</text><text x="440" y="178">31 px</text>
<text x="40" y="198">{$t[0]}</text><text x="120" y="198">{$t[1]}</text><text x="210" y="198">{$t[2]}</text><text x="315" y="198">{$t[3]}</text><text x="440" y="198">{$t[4]}</text></g>
<g fill="none" stroke="#E8590C" stroke-width="2"><path d="M70 100 q25 -24 50 0"/><path d="M152 96 q30 -28 58 0"/><path d="M248 90 q34 -30 67 0"/><path d="M362 84 q40 -34 78 0"/></g>
<text x="330" y="40" text-anchor="middle" font-size="15" font-weight="700" fill="#B84508">{$t[5]}</text>
<rect x="114" y="130" width="34" height="27" rx="6" fill="none" stroke="#2F8CF0" stroke-width="2"/>
</svg>
SVG;
    }
}
if (!function_exists('fig_uibase_grid')) {
    /** The 8-point spacing scale and a 12-column layout grid with margins and gutters. */
    function fig_uibase_grid(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Scala a 8 punti', 'Griglia a 12 colonne', 'margine', 'colonna', 'gutter']
            : ['8-point scale', '12-column grid', 'margin', 'column', 'gutter'];
        $cols = '';
        for ($i = 0; $i < 12; $i++) $cols .= '<rect x="' . (60 + $i * 44) . '" y="150" width="36" height="150" rx="4" fill="#FFE3EE"/><text x="' . (78 + $i * 44) . '" y="232" text-anchor="middle" fill="#C2255C">' . ($i + 1) . '</text>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 350" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<text x="20" y="26" font-size="15" font-weight="700">{$t[0]}</text>
<g fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"><rect x="20" y="76" width="4" height="4"/><rect x="60" y="72" width="8" height="8"/><rect x="100" y="64" width="16" height="16" rx="2"/><rect x="150" y="56" width="24" height="24" rx="3"/><rect x="210" y="48" width="32" height="32" rx="4"/><rect x="280" y="32" width="48" height="48" rx="6"/><rect x="370" y="16" width="64" height="64" rx="8"/></g>
<g text-anchor="middle" fill="#5A5880"><text x="22" y="100">4</text><text x="64" y="100">8</text><text x="108" y="100">16</text><text x="162" y="100">24</text><text x="226" y="100">32</text><text x="304" y="100">48</text><text x="402" y="100">64</text></g>
<text x="20" y="136" font-size="15" font-weight="700">{$t[1]}</text>
<rect x="20" y="146" width="600" height="158" rx="8" fill="none" stroke="#8A87B0" stroke-width="2"/>
{$cols}
<g stroke="#1E1C52" stroke-width="2" fill="none"><path d="M20 312 v8 h40 v-8"/><path d="M104 312 v8 h36 v-8"/><path d="M140 312 v8 h8 v-8"/><path d="M144 320 L176 330"/></g>
<g font-size="13" text-anchor="middle"><text x="40" y="340">{$t[2]}</text><text x="122" y="340" fill="#C2255C" font-weight="700">{$t[3]}</text><text x="180" y="340" text-anchor="start" fill="#5A5880">{$t[4]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uibase_gestalt')) {
    /** Proximity, similarity and common region drawn with dots. */
    function fig_uibase_gestalt(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Vicinanza', 'Somiglianza', 'Regione comune', 'vedi due gruppi', 'vedi righe di colore', 'il riquadro unisce']
            : ['Proximity', 'Similarity', 'Common region', 'you see two groups', 'you see rows by colour', 'the box joins them'];
        $a = ''; $b = ''; $c = '';
        foreach ([0, 1, 2] as $r) foreach ([0, 1] as $k) {
            $a .= '<circle cx="' . (50 + $k * 26) . '" cy="' . (70 + $r * 30) . '" r="9" fill="#8A87B0"/><circle cx="' . (140 + $k * 26) . '" cy="' . (70 + $r * 30) . '" r="9" fill="#8A87B0"/>';
        }
        foreach ([0, 1, 2] as $r) foreach ([0, 1, 2, 3] as $k) {
            $b .= $r === 1 ? '<rect x="' . (251 + $k * 34) . '" y="' . (61 + $r * 30) . '" width="18" height="18" rx="3" fill="#E8590C"/>' : '<circle cx="' . (260 + $k * 34) . '" cy="' . (70 + $r * 30) . '" r="9" fill="#8A87B0"/>';
        }
        foreach ([0, 1, 2, 3] as $k) $c .= '<circle cx="' . (470 + $k * 34) . '" cy="100" r="9" fill="#8A87B0"/>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-size="15" font-weight="700" text-anchor="middle"><text x="108" y="28">{$t[0]}</text><text x="311" y="28">{$t[1]}</text><text x="521" y="28">{$t[2]}</text></g>
{$a}{$b}
<rect x="488" y="78" width="66" height="44" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
{$c}
<g font-size="13" fill="#5A5880" text-anchor="middle"><text x="108" y="182">{$t[3]}</text><text x="311" y="182">{$t[4]}</text><text x="521" y="182">{$t[5]}</text></g>
<g stroke="#E4E2F0" stroke-width="2"><line x1="210" y1="40" x2="210" y2="190"/><line x1="420" y1="40" x2="420" y2="190"/></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uibase_breakpoints')) {
    /** The same grid of cards in one, two and three columns as the screen grows. */
    function fig_uibase_breakpoints(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Telefono', 'Tablet', 'Desktop', '1 colonna', '2 colonne', '3 colonne', 'si cambia layout dove il contenuto si rompe']
            : ['Phone', 'Tablet', 'Desktop', '1 column', '2 columns', '3 columns', 'change the layout where the content breaks'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="40" width="100" height="190" rx="16" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<g fill="#DCEBFF"><rect x="32" y="56" width="76" height="46" rx="6"/><rect x="32" y="110" width="76" height="46" rx="6"/><rect x="32" y="164" width="76" height="46" rx="6"/></g>
<rect x="150" y="40" width="190" height="190" rx="16" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<g fill="#D5F6EA"><rect x="164" y="56" width="77" height="70" rx="6"/><rect x="249" y="56" width="77" height="70" rx="6"/><rect x="164" y="134" width="77" height="70" rx="6"/><rect x="249" y="134" width="77" height="70" rx="6"/></g>
<rect x="370" y="40" width="250" height="160" rx="10" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<rect x="470" y="200" width="50" height="20" fill="#1E1C52"/><rect x="440" y="220" width="110" height="8" rx="4" fill="#1E1C52"/>
<g fill="#ECE6FF"><rect x="384" y="56" width="70" height="60" rx="6"/><rect x="460" y="56" width="70" height="60" rx="6"/><rect x="536" y="56" width="70" height="60" rx="6"/><rect x="384" y="124" width="70" height="60" rx="6"/><rect x="460" y="124" width="70" height="60" rx="6"/><rect x="536" y="124" width="70" height="60" rx="6"/></g>
<g font-weight="700" text-anchor="middle"><text x="70" y="28">{$t[0]}</text><text x="245" y="28">{$t[1]}</text><text x="495" y="28">{$t[2]}</text></g>
<g fill="#5A5880" font-size="13" text-anchor="middle"><text x="70" y="250">{$t[3]}</text><text x="245" y="250">{$t[4]}</text><text x="495" y="250">{$t[5]}</text></g>
<text x="320" y="280" text-anchor="middle" font-size="13" font-weight="700" fill="#5F3DC4">{$t[6]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'ui-base', 'family' => 'ui', 'after' => null, 'categories' => ['design', 'frontend'],
    'title' => L('UI: le basi', 'UI Basics'), 'short' => L('UI base', 'UI Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('I fondamenti del design di interfacce: gerarchia visiva, colore e contrasto, tipografia, spaziatura e griglie, Gestalt, pulsanti, link e campi, layout responsive.', 'The foundations of interface design: visual hierarchy, colour and contrast, typography, spacing and grids, Gestalt, buttons, links and fields, responsive layout.'),
    'lessons' => [
        lesson('hierarchy', L('Gerarchia visiva', 'Visual hierarchy'), [
            P('La **gerarchia visiva** decide in che ordine l’occhio legge una schermata. Gli strumenti sono **dimensione** (più grande = più importante), **peso** del carattere, **colore e contrasto**, **posizione** (in alto e a sinistra si guarda prima, nelle lingue che si leggono da sinistra) e **spazio** intorno a un elemento.', '**Visual hierarchy** decides in which order the eye reads a screen. The tools are **size** (bigger = more important), font **weight**, **colour and contrast**, **position** (top and left are seen first, in left-to-right languages) and **space** around an element.'),
            P('Ogni schermata dovrebbe avere **un’azione principale** evidente: un solo pulsante pieno e colorato, le azioni secondarie meno vistose (contorno o solo testo). Se tutto è in grassetto, niente lo è. I **principi della Gestalt** spiegano come raggruppiamo: elementi **vicini** o **simili** sembrano legati, un **contenitore** comune li unisce.', 'Every screen should have **one obvious main action**: a single filled, coloured button, with secondary actions less prominent (outline or text only). If everything is bold, nothing is. The **Gestalt principles** explain how we group: **close** or **similar** elements seem related, a common **container** unites them.'),
            Fig(L(fig_uibase_hierarchy('it'), fig_uibase_hierarchy('en')),
                L('La stessa scheda di un corso due volte. A sinistra titolo, orario e prezzo hanno tutti lo stesso colore e peso, e i due pulsanti sono identici. A destra il titolo è grande e scuro, l’orario più piccolo e grigio, c’è un solo pulsante pieno, «Prenota», e «Dettagli» è un semplice link.', 'The same class card twice. On the left the title, time and price all share the same colour and weight, and the two buttons are identical. On the right the title is large and dark, the time smaller and grey, there is a single filled button, “Book”, and “Details” is a plain link.'),
                L('Dimensione, peso e colore decidono che cosa si legge per primo.', 'Size, weight and colour decide what is read first.')),
            Tip('Prova dello strizzare gli occhi: guarda la schermata sfocata. Ciò che resta visibile è ciò che l’utente nota per primo.', 'The squint test: look at the screen blurred. What remains visible is what the user notices first.'),
        ], [
            Q('h1', L('Quale strumento non crea gerarchia visiva?', 'Which tool does not create visual hierarchy?'), [L('Il nome del file', 'The file name'), L('La dimensione', 'Size'), L('Il contrasto', 'Contrast'), L('La posizione', 'Position')], 0, L('Il resto guida l’occhio.', 'The rest guides the eye.')),
            Q('h2', L('Quante azioni principali dovrebbe avere una schermata?', 'How many main actions should a screen have?'), [L('Una, ben evidente', 'One, clearly prominent'), L('Tutte quelle possibili', 'As many as possible'), L('Nessuna', 'None'), L('Almeno cinque', 'At least five')], 0, L('Le altre sono secondarie.', 'Others are secondary.')),
            Q('h3', L('Che cosa dice il principio di vicinanza?', 'What does the proximity principle say?'), [L('Elementi vicini sembrano collegati', 'Close elements seem related'), L('Gli elementi lontani sono più importanti', 'Distant elements are more important'), L('Bisogna avvicinare lo schermo', 'Move the screen closer'), L('I colori vicini si mescolano', 'Nearby colours blend')], 0, L('Gestalt.', 'Gestalt.')),
        ], task('css', L('Rendi `.primario` l’azione principale: sfondo colorato con `background` e testo bianco (`color: white`). Lascia `.secondario` senza sfondo, con solo un `border`.', 'Make `.primario` the main action: coloured `background` and white text (`color: white`). Leave `.secondario` without a background, with just a `border`.'), ".primario {\n}\n.secondario {\n}\n", [css('.primario', 'color', 'rgb(255, 255, 255)', L('Testo bianco sul principale', 'White text on the main button')), re('\.primario\s*\{[^}]*background', L('Sfondo sul principale', 'Background on the main button')), re('\.secondario\s*\{[^}]*border\s*:', L('Bordo sul secondario', 'Border on the secondary button'))], '<button class="primario">Paga</button> <button class="secondario">Annulla</button>', ".primario {\n  background: #4f46e5;\n  color: white;\n}\n.secondario {\n  background: none;\n  border: 1px solid #4f46e5;\n}"), [
            dMatch(pMatch(), [[L('Dimensione', 'Size'), L('Più grande, più importante', 'Bigger, more important')], [L('Vicinanza', 'Proximity'), L('Vicini sembrano un gruppo', 'Close ones look like a group')], [L('Somiglianza', 'Similarity'), L('Simili sembrano legati', 'Similar ones seem linked')], [L('Pulsante pieno', 'Filled button'), L('Azione principale', 'Main action')], [L('Solo testo', 'Text only'), L('Azione secondaria', 'Secondary action')]], L('Guidare l’occhio.', 'Guiding the eye.')),
            dFlash(pFlash(), [[L('Se tutto è evidenziato, niente risalta', 'If everything is highlighted, nothing stands out'), true], [L('Tre pulsanti pieni uguali chiariscono la scelta', 'Three identical filled buttons clarify the choice'), false], [L('Lo spazio vuoto può dare importanza', 'White space can give importance'), true], [L('La posizione non conta nella lettura', 'Position does not matter in reading'), false]], L('Gerarchia.', 'Hierarchy.')),
        ]),
        lesson('color', L('Colore e contrasto', 'Colour and contrast'), [
            P('Una palette di interfaccia di solito ha un colore **primario** (brand e azioni), uno o due **secondari**, una scala di **neutri** (grigi per testi, bordi, sfondi) e i colori **semantici**: verde successo, rosso errore, giallo/arancio avviso, blu informazione. La regola **60-30-10** aiuta l’equilibrio: 60% neutro dominante, 30% secondario, 10% accento.', 'An interface palette usually has a **primary** colour (brand and actions), one or two **secondaries**, a scale of **neutrals** (greys for text, borders, backgrounds) and **semantic** colours: green success, red error, yellow/orange warning, blue info. The **60-30-10** rule helps balance: 60% dominant neutral, 30% secondary, 10% accent.'),
            P('Il **contrasto** tra testo e sfondo deve rispettare le WCAG: almeno **4,5:1** per il testo normale, **3:1** per testo grande ed elementi grafici. E il colore **non deve essere l’unico segnale**: un campo in errore ha anche un’icona e un messaggio, perché circa 1 uomo su 12 ha un deficit nella visione dei colori.', 'The **contrast** between text and background must meet WCAG: at least **4.5:1** for normal text, **3:1** for large text and graphical elements. And colour **must not be the only signal**: a field in error also has an icon and a message, because about 1 man in 12 has a colour vision deficiency.'),
            Fig(L(fig_uibase_contrast('it'), fig_uibase_contrast('en')),
                L('Sei riquadri con un testo di esempio e il loro rapporto di contrasto: blu notte su bianco 15,6:1, grigio medio su bianco 4,5:1, grigio chiaro su bianco 2,3:1 (illeggibile); bianco su arancione 3,6:1 (solo testo grande), bianco su verde 5:1, blu notte su giallo 11:1.', 'Six boxes with sample text and their contrast ratio: midnight blue on white 15.6:1, mid grey on white 4.5:1, light grey on white 2.3:1 (unreadable); white on orange 3.6:1 (large text only), white on green 5:1, midnight blue on yellow 11:1.'),
                L('Un colore vivace non garantisce il contrasto: va sempre misurato.', 'A bright colour does not guarantee contrast: always measure it.')),
        ], [
            Q('c1', L('Che contrasto minimo chiede la WCAG per il testo normale?', 'What minimum contrast does WCAG require for normal text?'), ['4,5:1', '1,5:1', '2:1', '10:1'], 0, L('3:1 per il testo grande.', '3:1 for large text.')),
            Q('c2', L('Perché il colore non deve essere l’unico segnale?', 'Why must colour not be the only signal?'), [L('Molte persone distinguono male i colori', 'Many people struggle to tell colours apart'), L('I colori costano', 'Colours cost money'), L('Gli schermi non mostrano il rosso', 'Screens cannot show red'), L('È più veloce', 'It is faster')], 0, L('Icona e testo in aiuto.', 'Icon and text to help.')),
            Q('c3', L('Che cosa indica di solito il rosso in un’interfaccia?', 'What does red usually mean in an interface?'), [L('Errore o pericolo', 'Error or danger'), L('Successo', 'Success'), L('Informazione neutra', 'Neutral information'), L('Elemento disattivato', 'Disabled element')], 0, L('Colori semantici.', 'Semantic colours.')),
        ], task('css', L('Il messaggio `.errore` usa solo il rosso. Aggiungi un segnale in più: un `border-left` e un’icona con `.errore::before { content: "⚠ "; }`.', 'The `.errore` message uses only red. Add another signal: a `border-left` and an icon with `.errore::before { content: "⚠ "; }`.'), ".errore {\n  color: #b91c1c;\n}\n", [re('\.errore\s*\{[^}]*border-left\s*:', L('Bordo a sinistra', 'Left border')), re('\.errore::?before\s*\{[^}]*content\s*:\s*"[^"]+"', L('Icona prima del testo', 'Icon before the text'))], '<p class="errore">La password è troppo corta</p>', ".errore {\n  color: #b91c1c;\n  border-left: 4px solid #b91c1c;\n  padding-left: 8px;\n}\n.errore::before {\n  content: \"⚠ \";\n}"), [
            dMatch(pMatch(), [[L('Primario', 'Primary'), L('Brand e azioni', 'Brand and actions')], [L('Neutri', 'Neutrals'), L('Testi, bordi, sfondi', 'Text, borders, backgrounds')], [L('Verde', 'Green'), L('Successo', 'Success')], [L('Rosso', 'Red'), L('Errore', 'Error')], ['60-30-10', L('Equilibrio della palette', 'Palette balance')]], L('Colori con un ruolo.', 'Colours with a role.')),
            dFlash(pFlash(), [[L('Il testo grande può avere contrasto 3:1', 'Large text may have 3:1 contrast'), true], [L('Grigio chiaro su bianco è sempre leggibile', 'Light grey on white is always readable'), false], [L('Un errore dovrebbe avere anche un messaggio', 'An error should also have a message'), true], [L('Più colori diversi rendono l’interfaccia più chiara', 'More different colours make the interface clearer'), false]], L('Contrasto e significato.', 'Contrast and meaning.')),
        ]),
        lesson('type', L('Tipografia', 'Typography'), [
            P('Nelle interfacce si usano poche famiglie di caratteri (una o due) e una **scala tipografica** coerente: dimensioni che crescono con un rapporto fisso (per esempio 1,25), come 13, 16, 20, 25, 31 px. Il testo del corpo sta di solito tra **16 e 18 px** sul web; sotto i 14 px diventa faticoso.', 'Interfaces use few typefaces (one or two) and a consistent **type scale**: sizes growing by a fixed ratio (for example 1.25), like 13, 16, 20, 25, 31 px. Body text usually sits between **16 and 18 px** on the web; below 14 px it becomes tiring.'),
            Fig(L(fig_uibase_scale('it'), fig_uibase_scale('en')),
                L('Cinque «Aa» sempre più grandi su una linea di base: 13, 16, 20, 25 e 31 pixel, con i nomi nota, testo, titolo 3, titolo 2 e titolo 1. Archi arancioni tra uno e l’altro dicono «× 1,25 a ogni gradino»; il testo da 16 px è evidenziato.', 'Five “Aa” getting bigger on one baseline: 13, 16, 20, 25 and 31 pixels, named caption, body, heading 3, heading 2 and heading 1. Orange arcs between them say “× 1.25 at every step”; the 16 px body text is highlighted.'),
                L('Una scala con un rapporto fisso dà ritmo: le dimensioni non si scelgono a caso.', 'A scale with a fixed ratio gives rhythm: sizes are not picked at random.')),
            P('L’**interlinea** (`line-height`) del corpo va intorno a **1,4-1,6** volte la dimensione, quella dei titoli più stretta (1,1-1,3). Il **peso** distingue senza cambiare dimensione (400 normale, 600 semibold, 700 bold). Evita testi lunghi tutti in maiuscolo o centrati: si leggono più lentamente.', 'Body **line height** (`line-height`) should be around **1.4-1.6** times the size, headings tighter (1.1-1.3). **Weight** distinguishes without changing size (400 regular, 600 semibold, 700 bold). Avoid long texts all in capitals or centred: they read more slowly.'),
            C(':root {
  --testo: 1rem;       /* 16px */
  --titolo-3: 1.25rem; /* 20px */
  --titolo-2: 1.563rem;
  --titolo-1: 1.953rem;
}
body { font-size: var(--testo); line-height: 1.5; }
h1, h2, h3 { line-height: 1.2; }', 'css', L('<h1 style="font-size:var(--titolo-1)">Titolo 1 · 31 px</h1><h2 style="font-size:var(--titolo-2)">Titolo 2 · 25 px</h2><h3 style="font-size:var(--titolo-3)">Titolo 3 · 20 px</h3><p>Il testo del corpo è a 16 px con interlinea 1,5: le righe respirano e si leggono senza fatica anche quando il paragrafo va a capo più volte.</p>', '<h1 style="font-size:var(--titolo-1)">Heading 1 · 31 px</h1><h2 style="font-size:var(--titolo-2)">Heading 2 · 25 px</h2><h3 style="font-size:var(--titolo-3)">Heading 3 · 20 px</h3><p>Body text is 16 px with a line height of 1.5: the lines can breathe and read easily even when the paragraph wraps several times.</p>')),
        ], [
            Q('t1', L('Che cos’è una scala tipografica?', 'What is a type scale?'), [L('Dimensioni che crescono con un rapporto fisso', 'Sizes growing by a fixed ratio'), L('Una bilancia per i font', 'A scale for weighing fonts'), L('L’elenco dei font installati', 'The list of installed fonts'), L('Una sola dimensione per tutto', 'A single size for everything')], 0, L('Coerenza tra i livelli.', 'Consistency between levels.')),
            Q('t2', L('Che interlinea è adatta al testo del corpo?', 'What line height suits body text?'), [L('Circa 1,4-1,6', 'About 1.4-1.6'), L('Circa 0,8', 'About 0.8'), L('Esattamente 3', 'Exactly 3'), L('Zero', 'Zero')], 0, L('I titoli più stretti.', 'Headings tighter.')),
            Q('t3', L('Perché evitare paragrafi lunghi in maiuscolo?', 'Why avoid long paragraphs in capitals?'), [L('Si leggono più lentamente', 'They read more slowly'), L('Pesano di più', 'They weigh more'), L('Non si possono stampare', 'They cannot be printed'), L('Sono vietati', 'They are forbidden')], 0, L('Le forme delle parole si perdono.', 'Word shapes get lost.')),
        ], task('css', L('Imposta il testo: `body` a `font-size: 16px` con `line-height: 1.5`, e `h1` a `line-height: 1.2`.', 'Set the text: `body` at `font-size: 16px` with `line-height: 1.5`, and `h1` at `line-height: 1.2`.'), "body {\n}\nh1 {\n}\n", [css('body', 'font-size', '16px', L('Corpo a 16px', 'Body at 16px')), re('body\s*\{[^}]*line-height\s*:\s*1\.5', L('Interlinea 1,5', 'Line height 1.5')), re('h1\s*\{[^}]*line-height\s*:\s*1\.2', L('Titolo più stretto', 'Tighter heading'))], '<h1>Ciao</h1><p>Un paragrafo di testo.</p>', "body {\n  font-size: 16px;\n  line-height: 1.5;\n}\nh1 {\n  line-height: 1.2;\n}"), [
            dMatch(pMatch(), [['16-18 px', L('Testo del corpo', 'Body text')], ['1,5', L('Interlinea del corpo', 'Body line height')], ['1,2', L('Interlinea dei titoli', 'Heading line height')], ['600', 'Semibold'], [L('Rapporto 1,25', '1.25 ratio'), L('Scala tipografica', 'Type scale')]], L('Testi leggibili.', 'Readable text.')),
            dBlanks(pFill(), "body {\n  font-size: ___;\n  line-height: ___;\n}", ['16px', '1.5'], ['9px', '0.8'], L('Corpo leggibile e arioso.', 'Readable, airy body.')),
        ]),
        lesson('spacing', L('Spaziatura e griglie', 'Spacing and grids'), [
            P('Le spaziature si scelgono da una **scala**, non a caso: il sistema più diffuso è la **griglia a 8 punti** (4, 8, 16, 24, 32, 48…), che rende coerenti margini, padding e dimensioni e si divide bene su tutti gli schermi. Lo spazio comunica: **meno spazio dentro** un gruppo, **più spazio tra** gruppi diversi.', 'Spacing is picked from a **scale**, not at random: the most widespread system is the **8-point grid** (4, 8, 16, 24, 32, 48…), which makes margins, padding and sizes consistent and divides well on every screen. Space communicates: **less space within** a group, **more space between** different groups.'),
            P('Le **griglie di layout** dividono la pagina in colonne (12 su desktop, 8 su tablet, 4 su telefono) con **gutter** (spazi tra colonne) e **margini** laterali. Allineare gli elementi alle colonne dà ordine anche quando il contenuto cambia. Lo **spazio bianco** non è spazio sprecato: aumenta la comprensione e la sensazione di qualità.', '**Layout grids** split the page into columns (12 on desktop, 8 on tablet, 4 on phones) with **gutters** (space between columns) and side **margins**. Aligning elements to columns gives order even as content changes. **White space** is not wasted space: it improves comprehension and the feeling of quality.'),
            Fig(L(fig_uibase_grid('it'), fig_uibase_grid('en')),
                L('In alto, quadrati sempre più grandi della scala a 8 punti: 4, 8, 16, 24, 32, 48 e 64. Sotto, una pagina divisa in 12 colonne rosa numerate, con un margine a sinistra e a destra e uno spazio stretto, il gutter, tra una colonna e l’altra.', 'At the top, growing squares of the 8-point scale: 4, 8, 16, 24, 32, 48 and 64. Below, a page split into 12 numbered pink columns, with a margin on the left and right and a narrow space, the gutter, between columns.'),
                L('Spaziature dalla scala e contenuti allineati alle colonne: ordine anche quando il contenuto cambia.', 'Spacing from the scale and content aligned to columns: order even as the content changes.')),
        ], [
            Q('s1', L('Quale serie segue la griglia a 8 punti?', 'Which series does the 8-point grid follow?'), ['8, 16, 24, 32', '5, 10, 15, 20', '7, 13, 19, 23', '3, 9, 27, 81'], 0, L('Con 4 per i dettagli.', 'With 4 for details.')),
            Q('s2', L('Come si usa lo spazio per raggruppare?', 'How is space used to group?'), [L('Meno dentro il gruppo, più tra i gruppi', 'Less within the group, more between groups'), L('Lo stesso spazio ovunque', 'The same space everywhere'), L('Più dentro, meno fuori', 'More within, less outside'), L('Nessuno spazio', 'No space at all')], 0, L('Vicinanza della Gestalt.', 'Gestalt proximity.')),
            Q('s3', L('Che cos’è il gutter?', 'What is the gutter?'), [L('Lo spazio tra le colonne', 'The space between columns'), L('Il bordo della pagina', 'The page border'), L('Una colonna larga', 'A wide column'), L('Un tipo di font', 'A kind of font')], 0, L('Respiro tra i contenuti.', 'Breathing room between content.')),
        ], task('css', L('Applica la griglia a 8 punti alla `.card`: `padding: 16px` e `margin-bottom: 24px`.', 'Apply the 8-point grid to `.card`: `padding: 16px` and `margin-bottom: 24px`.'), ".card {\n  background: #fff;\n}\n", [css('.card', 'padding-top', '16px', L('Padding di 16px', '16px padding')), css('.card', 'margin-bottom', '24px', L('Margine di 24px', '24px margin'))], '<div class="card">Uno</div><div class="card">Due</div>', ".card {\n  padding: 16px;\n  margin-bottom: 24px;\n}"), [
            dMatch(pMatch(), [[L('Griglia a 8 punti', '8-point grid'), L('Spaziature coerenti', 'Consistent spacing')], ['Gutter', L('Spazio tra colonne', 'Space between columns')], [L('12 colonne', '12 columns'), 'Desktop'], [L('4 colonne', '4 columns'), L('Telefono', 'Phone')], [L('Spazio bianco', 'White space'), L('Chiarezza e qualità', 'Clarity and quality')]], L('Ordine e respiro.', 'Order and breathing room.')),
            dFlash(pFlash(), [[L('Lo spazio bianco aiuta la comprensione', 'White space aids comprehension'), true], [L('Ogni margine dovrebbe essere un numero diverso a caso', 'Every margin should be a different random number'), false], [L('Su telefono si usano meno colonne', 'Phones use fewer columns'), true], [L('Lo spazio tra gruppi va ridotto al minimo', 'Space between groups should be minimal'), false]], L('Spaziare bene.', 'Spacing well.')),
        ]),
        lesson('gestalt', L('Allineamento e principi della Gestalt', 'Alignment and Gestalt principles'), [
            P('L’occhio raggruppa ciò che vede secondo regole precise, i **principi della Gestalt**. **Vicinanza**: elementi vicini sembrano collegati (un’etichetta va vicina al suo campo). **Somiglianza**: elementi uguali per forma o colore sembrano dello stesso tipo. **Regione comune**: ciò che sta nella stessa scheda va insieme.', 'The eye groups what it sees by precise rules, the **Gestalt principles**. **Proximity**: elements close together seem related (a label belongs near its field). **Similarity**: elements alike in shape or colour seem of the same kind. **Common region**: what sits in the same card goes together.'),
            Fig(L(fig_uibase_gestalt('it'), fig_uibase_gestalt('en')),
                L('Tre esempi con dei pallini. Vicinanza: dodici pallini grigi in due blocchi separati da uno spazio, che si leggono come due gruppi. Somiglianza: tre righe, quella centrale fatta di quadrati arancioni, che si legge come un gruppo a sé. Regione comune: quattro pallini in fila, due dei quali dentro un riquadro azzurro che li unisce.', 'Three examples with dots. Proximity: twelve grey dots in two blocks separated by a gap, read as two groups. Similarity: three rows, the middle one made of orange squares, read as a group of its own. Common region: four dots in a row, two of them inside a blue box that joins them.'),
                L('Spazio, forma e contenitori dicono che cosa va insieme, prima ancora delle parole.', 'Space, shape and containers say what belongs together, before any words.')),
            C('.campo label { margin-bottom: 4px; }   /* vicina al suo campo */
.campo { margin-bottom: 24px; }         /* lontana dal campo dopo */
.btn-secondario { background: none; border: 1px solid; }  /* diverso: azione diversa */', 'css', L('<style>label{display:block}input{padding:6px}button{padding:8px 14px}</style><div class="campo"><label for="n">Nome</label><input id="n"></div><div class="campo"><label for="m">Email</label><input id="m"></div><button>Invia</button> <button class="btn-secondario">Annulla</button>', '<style>label{display:block}input{padding:6px}button{padding:8px 14px}</style><div class="campo"><label for="n">Name</label><input id="n"></div><div class="campo"><label for="m">Email</label><input id="m"></div><button>Send</button> <button class="btn-secondario">Cancel</button>')),
            P('Allinea tutto a poche **linee invisibili**: un margine sinistro comune rende ordinata anche una pagina piena. Evita il testo centrato su più righe: senza un bordo fisso l’occhio fatica a trovare l’inizio della riga successiva.', 'Align everything to a few **invisible lines**: a shared left edge makes even a busy page tidy. Avoid multi-line centred text: without a fixed edge the eye struggles to find the start of the next line.'),
        ], [
            Q('gs1', L('Che cosa dice il principio di vicinanza?', 'What does the proximity principle say?'), [L('Elementi vicini sembrano collegati', 'Close elements seem related'), L('Elementi grandi sono importanti', 'Large elements are important'), L('I colori caldi avanzano', 'Warm colours advance'), L('Il testo va centrato', 'Text should be centred')], 0, L('Etichetta vicina al suo campo.', 'Label near its field.')),
            Q('gs2', L('Perché il margine dopo il campo è più grande di quello tra etichetta e campo?', 'Why is the gap after a field larger than the label-field gap?'), [L('Per raggruppare etichetta e campo', 'To group label and field'), L('Per riempire lo spazio', 'To fill space'), L('Per la SEO', 'For SEO'), L('Non conta', 'It does not matter')], 0, L('Lo spazio crea i gruppi.', 'Space creates groups.')),
            Q('gs3', L('Perché evitare testo centrato su più righe?', 'Why avoid multi-line centred text?'), [L('Manca un bordo fisso per l’occhio', 'There is no fixed edge for the eye'), L('Occupa più spazio', 'It takes more space'), L('Non si stampa', 'It cannot be printed'), L('È vietato', 'It is forbidden')], 0, L('Leggere diventa faticoso.', 'Reading gets tiring.')),
        ], task('css', L('Dai a `label` un `margin-bottom` di `4px` e a `.campo` un `margin-bottom` di `24px`.', 'Give `label` a `margin-bottom` of `4px` and `.campo` a `margin-bottom` of `24px`.'), '', [css('label', 'margin-bottom', '4px', L('Etichetta vicina al campo', 'Label close to its field')), css('.campo', 'margin-bottom', '24px', L('Campi ben separati', 'Fields well separated'))], '<div class="campo"><label for="n">Nome</label><input id="n"></div>
<div class="campo"><label for="e">Email</label><input id="e"></div>', "label { margin-bottom: 4px; }\n.campo { margin-bottom: 24px; }"), [
            dMatch(pMatch(), [[L('Vicinanza', 'Proximity'), L('Vicini = collegati', 'Close = related')], [L('Somiglianza', 'Similarity'), L('Uguali = stesso tipo', 'Alike = same kind')], [L('Regione comune', 'Common region'), L('Stessa scheda = insieme', 'Same card = together')], [L('Allineamento', 'Alignment'), L('Linee invisibili', 'Invisible lines')]], L('Come l’occhio raggruppa.', 'How the eye groups.')),
            dFlash(pFlash(), [[L('Lo spazio bianco crea gruppi', 'White space creates groups'), true], [L('Un’etichetta va più vicina al campo precedente', 'A label belongs closer to the previous field'), false], [L('Un margine sinistro comune dà ordine', 'A shared left edge gives order'), true], [L('Pulsanti diversi devono sembrare identici', 'Different buttons should look identical'), false]], L('Ordine visivo.', 'Visual order.')),
        ]),
        lesson('controls', L('Pulsanti, link e campi', 'Buttons, links and fields'), [
            P('Alcuni elementi tornano in ogni interfaccia. I **pulsanti** fanno un’**azione** (Salva, Invia), i **link** portano in un **altro posto** (un’altra pagina o sezione): confonderli disorienta. Un pulsante si riconosce dalla forma (sfondo o bordo, padding generoso) e ha un’etichetta con un verbo; un link nel testo si distingue per colore **e** sottolineatura, perché il colore da solo non basta.', 'Some elements appear in every interface. **Buttons** perform an **action** (Save, Send), **links** take you **somewhere else** (another page or section): mixing them up disorients people. A button is recognisable by its shape (background or border, generous padding) and has a verb as its label; a link in text stands out by colour **and** underline, because colour alone is not enough.'),
            P('Un **campo** di testo ha un’**etichetta** sempre visibile sopra, un bordo ben visibile (se è il bordo a far riconoscere il campo, serve almeno 3:1 di contrasto con lo sfondo), un testo d’aiuto se serve e uno spazio per l’errore sotto. Tutti gli elementi interattivi hanno uno **stato di focus** evidente. E le stesse forme e gli stessi colori vanno usati sempre per gli stessi ruoli.', 'A text **field** has a **label** always visible above it, a clearly visible border (if the border is what identifies the field, it needs at least 3:1 contrast with the background), help text if needed and room for the error below. Every interactive element has an obvious **focus state**. And the same shapes and colours must always be used for the same roles.'),
            C('.btn   { padding: 12px 20px; border-radius: 8px; background: #4f46e5; color: #fff; }
a      { color: #4338ca; text-decoration: underline; }
input  { border: 1px solid #6b7280; border-radius: 6px; padding: 10px 12px; }', 'css', L('<p><button class="btn" style="border:0">Salva</button> oppure <a href="#">vai al profilo</a></p><label for="e" style="display:block;margin-bottom:4px">Email</label><input id="e" placeholder="nome@esempio.it">', '<p><button class="btn" style="border:0">Save</button> or <a href="#">go to your profile</a></p><label for="e" style="display:block;margin-bottom:4px">Email</label><input id="e" placeholder="name@example.com">')),
        ], [
            Q('b1', L('Quando usare un link invece di un pulsante?', 'When should you use a link instead of a button?'), [L('Per salvare un modulo', 'To save a form'), L('Per portare a un’altra pagina', 'To take you to another page'), L('Per eliminare un elemento', 'To delete an item'), L('Per inviare un pagamento', 'To send a payment')], 1, L('Il pulsante agisce, il link porta altrove.', 'Buttons act, links go elsewhere.')),
            Q('b2', L('Se è il bordo a far riconoscere un campo, che contrasto minimo deve avere con lo sfondo?', 'If the border is what identifies a field, what minimum contrast must it have with the background?'), ['1,5:1', '2:1', '3:1', '7:1'], 2, L('WCAG 1.4.11, contrasto degli elementi non testuali.', 'WCAG 1.4.11, non-text contrast.')),
            Q('b3', L('Perché un link nel testo va anche sottolineato?', 'Why should a link in text also be underlined?'), [L('È più elegante', 'It is more elegant'), L('Lo impone il browser', 'The browser requires it'), L('Rende il testo più corto', 'It makes the text shorter'), L('Il colore da solo non basta a riconoscerlo', 'Colour alone is not enough to recognise it')], 3, L('Pensa a chi distingue male i colori.', 'Think of people who struggle with colours.')),
        ], task('css', L('Rendi il link `.testo a` riconoscibile anche senza colore con `text-decoration: underline`, e dai a `input` un `border` di `1px solid #6b7280`.', 'Make the `.testo a` link recognisable without colour using `text-decoration: underline`, and give `input` a `border` of `1px solid #6b7280`.'), ".testo a {\n  color: #4338ca;\n  text-decoration: none;\n}\ninput {\n  border: 1px solid #e5e7eb;\n}\n", [css('.testo a', 'text-decoration-line', 'underline', L('Link sottolineato', 'Underlined link')), css('input', 'border-top-color', 'rgb(107, 114, 128)', L('Bordo del campo ben visibile', 'Clearly visible field border'))], '<p class="testo">Leggi le <a href="#">condizioni</a>.</p><label for="e">Email</label> <input id="e">', ".testo a {\n  color: #4338ca;\n  text-decoration: underline;\n}\ninput {\n  border: 1px solid #6b7280;\n}"), [
            dMatch(pMatch(), [[L('Pulsante', 'Button'), L('Fa un’azione', 'Performs an action')], ['Link', L('Porta altrove', 'Goes elsewhere')], [L('Etichetta', 'Label'), L('Sempre visibile sopra il campo', 'Always visible above the field')], ['Focus', L('Mostra dove si è da tastiera', 'Shows where you are by keyboard')]], L('Gli elementi di base.', 'The basic elements.')),
            dBlanks(pFill(), "a {\n  text-decoration: ___;\n}\ninput {\n  border: 1px ___ #6b7280;\n}", ['underline', 'solid'], ['none', 'hidden'], L('Link sottolineato, bordo pieno.', 'Underlined link, solid border.')),
        ]),
        lesson('responsive', L('Layout responsive e breakpoint', 'Responsive layout and breakpoints'), [
            P('Le interfacce si vedono su schermi da 320 a oltre 2000 px. Il **design responsive** usa layout **fluidi** (percentuali, unità `fr`, `max-width`) che si adattano, e **breakpoint**: larghezze a cui il layout cambia, per esempio una colonna sul telefono, due sul tablet, tre sul desktop. I breakpoint si scelgono dove il **contenuto** si rompe, non sui modelli di telefono, che cambiano ogni anno.', 'Interfaces are seen on screens from 320 to over 2000 px. **Responsive design** uses **fluid** layouts (percentages, `fr` units, `max-width`) that adapt, and **breakpoints**: widths at which the layout changes, for example one column on phones, two on tablets, three on desktop. Breakpoints are chosen where the **content** breaks, not by phone models, which change every year.'),
            Fig(L(fig_uibase_breakpoints('it'), fig_uibase_breakpoints('en')),
                L('La stessa griglia di schede su tre schermi: su un telefono in una colonna, su un tablet in due colonne, su un monitor desktop in tre colonne.', 'The same grid of cards on three screens: on a phone in one column, on a tablet in two columns, on a desktop monitor in three columns.'),
                L('Il contenuto resta lo stesso, cambia solo quante colonne ci stanno.', 'The content stays the same, only the number of columns that fit changes.')),
            P('Di solito si parte **mobile first**: prima la versione stretta, poi le media query `min-width` aggiungono colonne. Le immagini usano `max-width: 100%` per non uscire dal contenitore e i testi restano leggibili. Con **CSS Grid** e `repeat(auto-fit, minmax(240px, 1fr))` una griglia di card si riorganizza da sola, anche senza breakpoint.', 'You usually start **mobile first**: the narrow version first, then `min-width` media queries add columns. Images use `max-width: 100%` so they do not overflow their container, and text stays readable. With **CSS Grid** and `repeat(auto-fit, minmax(240px, 1fr))` a grid of cards rearranges itself, even without breakpoints.'),
            C('.griglia {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}
img { max-width: 100%; height: auto; }
@media (min-width: 900px) {
  .pagina { grid-template-columns: 240px 1fr; }
}', 'css', L('<style>.griglia div{background:#DCEBFF;border-radius:8px;padding:16px;font:15px system-ui}</style><div class="griglia"><div>Scheda 1</div><div>Scheda 2</div><div>Scheda 3</div><div>Scheda 4</div></div>', '<style>.griglia div{background:#DCEBFF;border-radius:8px;padding:16px;font:15px system-ui}</style><div class="griglia"><div>Card 1</div><div>Card 2</div><div>Card 3</div><div>Card 4</div></div>')),
        ], [
            Q('r1', L('Dove conviene mettere un breakpoint?', 'Where is it best to put a breakpoint?'), [L('Alla larghezza dell’iPhone più venduto', 'At the width of the best-selling iPhone'), L('Dove il contenuto si rompe', 'Where the content breaks'), L('Ogni 100 px esatti', 'Every 100 px exactly'), L('Solo a 1920 px', 'Only at 1920 px')], 1, L('Il contenuto decide.', 'The content decides.')),
            Q('r2', L('Che cosa fa una media query `min-width: 768px` in un approccio mobile first?', 'What does a `min-width: 768px` media query do in a mobile-first approach?'), [L('Si applica solo ai telefoni', 'It applies only to phones'), L('Nasconde la pagina sugli schermi grandi', 'It hides the page on large screens'), L('Aggiunge regole da 768 px in su', 'It adds rules from 768 px up'), L('Cambia il font di sistema', 'It changes the system font')], 2, L('Si parte dallo stretto e si aggiunge.', 'Start narrow and add.')),
            Q('r3', L('Che cosa fa `repeat(auto-fit, minmax(240px, 1fr))`?', 'What does `repeat(auto-fit, minmax(240px, 1fr))` do?'), [L('Crea sempre tre colonne', 'It always creates three columns'), L('Blocca ogni colonna a 240 px', 'It locks every column at 240 px'), L('Nasconde le card in eccesso', 'It hides the extra cards'), L('Crea tante colonne quante ne entrano, larghe almeno 240 px', 'It creates as many columns as fit, at least 240 px wide')], 3, L('Una griglia che si adatta da sola.', 'A grid that adapts by itself.')),
        ], task('css', L('Rendi responsive la `.griglia`: `display: grid`, `grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))` e un `gap` di `16px`.', 'Make `.griglia` responsive: `display: grid`, `grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))` and a `gap` of `16px`.'), ".griglia {\n  display: block;\n}\n", [css('.griglia', 'display', 'grid', L('È una griglia', 'It is a grid')), re('\.griglia\s*\{[^}]*grid-template-columns\s*:\s*repeat\(\s*auto-(fit|fill)\s*,\s*minmax\(', L('Colonne automatiche', 'Automatic columns')), css('.griglia', 'row-gap', '16px', L('Spazio di 16px', '16px gap'))], '<div class="griglia"><div>Uno</div><div>Due</div><div>Tre</div><div>Quattro</div></div>', ".griglia {\n  display: grid;\n  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));\n  gap: 16px;\n}"), [
            dMatch(pMatch(), [['Breakpoint', L('Dove il layout cambia', 'Where the layout changes')], ['Mobile first', L('Prima lo schermo stretto', 'Narrow screen first')], ['auto-fit', L('Colonne quante ne entrano', 'As many columns as fit')], ['max-width: 100%', L('Immagini che non escono', 'Images that do not overflow')]], L('Layout che si adattano.', 'Layouts that adapt.')),
            dFlash(pFlash(), [[L('Un layout fluido si adatta tra un breakpoint e l’altro', 'A fluid layout adapts between breakpoints'), true], [L('I breakpoint vanno scelti sui modelli di telefono', 'Breakpoints should match phone models'), false], [L('Con auto-fit una griglia può fare a meno dei breakpoint', 'With auto-fit a grid can do without breakpoints'), true], [L('Mobile first significa ignorare il desktop', 'Mobile first means ignoring desktop'), false]], L('Responsive.', 'Responsive.')),
        ]),
    ],
];
