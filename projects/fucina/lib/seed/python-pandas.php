<?php
/** Data analysis with pandas: DataFrames, loading and exploring, selecting and filtering, cleaning, grouping and joining, charts, time series, new columns, pivots and big data. */

/** The anatomy of a DataFrame: index, column names, one row, one column as a Series. */
if (!function_exists('fig_pd_df')) {
    function fig_pd_df(array $t): string
    {
        $rows = [['0', '2026-01-03', 'Nord', '120.0'], ['1', '2026-01-05', 'Sud', '80.5'], ['2', '2026-01-09', 'Nord', '230.0'], ['3', '2026-01-12', 'Centro', '45.0']];
        $cells = '';
        foreach ($rows as $r => $row) {
            $y = 72 + $r * 32;
            $fill = $r === 2 ? '#FFF3EA' : '#FFFFFF';
            $cells .= "<rect x=\"190\" y=\"$y\" width=\"310\" height=\"32\" fill=\"$fill\"/>"
                . "<text x=\"170\" y=\"" . ($y + 21) . "\" text-anchor=\"middle\" fill=\"#5B5880\">{$row[0]}</text>"
                . "<text x=\"200\" y=\"" . ($y + 21) . "\">{$row[1]}</text><text x=\"318\" y=\"" . ($y + 21) . "\">{$row[2]}</text>"
                . "<text x=\"492\" y=\"" . ($y + 21) . "\" text-anchor=\"end\">{$row[3]}</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="ad" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="150" y="40" width="40" height="160" fill="#F1F0F8"/>
<rect x="190" y="40" width="310" height="32" fill="#ECE6FF"/>
<g font-family="ui-monospace, monospace" font-size="14">
<text x="200" y="61" font-weight="700">data</text><text x="318" y="61" font-weight="700">regione</text><text x="492" y="61" text-anchor="end" font-weight="700">importo</text>
$cells
</g>
<rect x="150" y="40" width="350" height="160" rx="6" fill="none" stroke="#8A87B0" stroke-width="2"/>
<line x1="190" y1="40" x2="190" y2="200" stroke="#8A87B0" stroke-width="2"/>
<line x1="150" y1="72" x2="500" y2="72" stroke="#8A87B0" stroke-width="2"/>
<rect x="414" y="36" width="90" height="168" rx="8" fill="none" stroke="#2F8CF0" stroke-width="3"/>
<rect x="146" y="136" width="358" height="32" rx="6" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="6 4"/>
<text x="16" y="124" font-weight="700" fill="#5B5880">{$t[0]}</text>
<line x1="80" y1="130" x2="144" y2="130" stroke="#1E1C52" stroke-width="2" marker-end="url(#ad)"/>
<text x="300" y="22" text-anchor="middle" font-weight="700" fill="#5B33C9">{$t[1]}</text>
<text x="530" y="60" font-weight="700" fill="#1B66BC" font-family="ui-monospace, monospace">Series</text>
<line x1="528" y1="66" x2="510" y2="80" stroke="#2F8CF0" stroke-width="2"/>
<text x="530" y="158" font-weight="700" fill="#B84508">{$t[2]}</text>
<text x="325" y="230" text-anchor="middle" font-family="ui-monospace, monospace">shape = (4, 3)</text>
</svg>
SVG;
    }
}

/** A boolean mask: the comparison gives True or False per row, and only True rows are kept. */
if (!function_exists('fig_pd_mask')) {
    function fig_pd_mask(array $t): string
    {
        $vals = [['0', '120.0', true], ['1', '80.5', false], ['2', '230.0', true], ['3', '45.0', false]];
        $cells = '';
        foreach ($vals as $r => [$i, $v, $ok]) {
            $y = 64 + $r * 34;
            $cells .= "<text x=\"34\" y=\"" . ($y + 22) . "\" fill=\"#5B5880\">$i</text>"
                . "<rect x=\"56\" y=\"$y\" width=\"100\" height=\"30\" rx=\"6\" fill=\"#FFFFFF\" stroke=\"#8A87B0\" stroke-width=\"2\"/><text x=\"146\" y=\"" . ($y + 21) . "\" text-anchor=\"end\">$v</text>"
                . "<rect x=\"250\" y=\"$y\" width=\"100\" height=\"30\" rx=\"6\" fill=\"" . ($ok ? '#D5F6EA' : '#FFE3EE') . "\" stroke=\"" . ($ok ? '#0B7F5C' : '#F03E82') . "\" stroke-width=\"2\"/><text x=\"300\" y=\"" . ($y + 21) . "\" text-anchor=\"middle\" fill=\"" . ($ok ? '#0B7F5C' : '#B8255F') . "\">" . ($ok ? 'True' : 'False') . "</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="am" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="106" y="44" text-anchor="middle" font-weight="700">importo</text>
<text x="300" y="44" text-anchor="middle" font-weight="700">&gt; 100</text>
<text x="530" y="44" text-anchor="middle" font-weight="700">vendite[{$t[0]}]</text>
$cells
<line x1="164" y1="130" x2="240" y2="130" stroke="#1E1C52" stroke-width="2" marker-end="url(#am)"/>
<line x1="360" y1="130" x2="436" y2="130" stroke="#1E1C52" stroke-width="2" marker-end="url(#am)"/>
<text x="434" y="108" fill="#5B5880">0</text>
<rect x="456" y="86" width="150" height="34" rx="6" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="596" y="109" text-anchor="end">120.0</text>
<text x="434" y="152" fill="#5B5880">2</text>
<rect x="456" y="130" width="150" height="34" rx="6" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="596" y="153" text-anchor="end">230.0</text>
<text x="320" y="224" text-anchor="middle" font-family="system-ui, sans-serif" fill="#5B5880">{$t[1]}</text>
</svg>
SVG;
    }
}

/** groupby as split, apply, combine. */
if (!function_exists('fig_pd_group')) {
    function fig_pd_group(array $t): string
    {
        $c = ['Nord' => ['#DCEBFF', '#2F8CF0'], 'Sud' => ['#FFE8D6', '#E8590C'], 'Centro' => ['#D5F6EA', '#0B7F5C']];
        $rows = '';
        foreach ([['Nord', 120], ['Sud', 80], ['Nord', 230], ['Centro', 45], ['Sud', 60]] as $i => [$r, $v]) {
            $y = 58 + $i * 32;
            $rows .= "<rect x=\"16\" y=\"$y\" width=\"180\" height=\"28\" rx=\"6\" fill=\"{$c[$r][0]}\" stroke=\"{$c[$r][1]}\" stroke-width=\"2\"/><text x=\"26\" y=\"" . ($y + 19) . "\">$r</text><text x=\"186\" y=\"" . ($y + 19) . "\" text-anchor=\"end\">$v</text>";
        }
        $groups = '';
        $sums = '';
        foreach ([['Nord', '120 230', 350], ['Sud', '80 60', 140], ['Centro', '45', 45]] as $i => [$r, $vs, $s]) {
            $y = 52 + $i * 56;
            $groups .= "<rect x=\"244\" y=\"$y\" width=\"170\" height=\"44\" rx=\"10\" fill=\"{$c[$r][0]}\" stroke=\"{$c[$r][1]}\" stroke-width=\"2\"/><text x=\"254\" y=\"" . ($y + 27) . "\" font-weight=\"700\">$r</text><text x=\"404\" y=\"" . ($y + 27) . "\" text-anchor=\"end\">$vs</text>";
            $sums .= "<rect x=\"476\" y=\"" . ($y + 8) . "\" width=\"148\" height=\"28\" rx=\"6\" fill=\"{$c[$r][0]}\" stroke=\"{$c[$r][1]}\" stroke-width=\"2\"/><text x=\"486\" y=\"" . ($y + 27) . "\">$r</text><text x=\"614\" y=\"" . ($y + 27) . "\" text-anchor=\"end\" font-weight=\"700\">$s</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="ag" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-family="system-ui, sans-serif" font-weight="700" text-anchor="middle" font-size="15">
<text x="106" y="34">vendite</text><text x="329" y="34">1. {$t[0]}</text><text x="550" y="34">3. {$t[2]}</text>
</g>
$rows
$groups
$sums
<line x1="200" y1="130" x2="236" y2="130" stroke="#1E1C52" stroke-width="2" marker-end="url(#ag)"/>
<line x1="418" y1="130" x2="468" y2="130" stroke="#1E1C52" stroke-width="2" marker-end="url(#ag)"/>
<text x="443" y="120" text-anchor="middle" font-size="13">sum</text>
<text x="443" y="150" text-anchor="middle" font-size="12" font-family="system-ui, sans-serif" fill="#5B5880">2. {$t[1]}</text>
<text x="320" y="238" text-anchor="middle" font-family="system-ui, sans-serif" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

/** Four weeks of daily sales as bars, with the 7-day rolling mean drawn over them. */
if (!function_exists('fig_pd_rolling')) {
    function fig_pd_rolling(): string
    {
        $v = [];
        for ($i = 0; $i < 28; $i++) $v[] = 40 + 18 * sin($i / 2.2) + (($i * 37) % 23) + $i * 1.4;
        $bars = '';
        $pts = [];
        foreach ($v as $i => $x) {
            $bx = 60 + $i * 19;
            $h = $x * 1.55;
            $bars .= '<rect x="' . $bx . '" y="' . round(210 - $h, 1) . '" width="14" height="' . round($h, 1) . '" rx="3" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="1"/>';
            if ($i >= 6) $pts[] = ($bx + 7) . ',' . round(210 - array_sum(array_slice($v, $i - 6, 7)) / 7 * 1.55, 1);
        }
        $line = implode(' ', $pts);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<line x1="50" y1="210" x2="600" y2="210" stroke="#8A87B0" stroke-width="2"/>
$bars
<polyline points="$line" fill="none" stroke="#E8590C" stroke-width="3" stroke-linejoin="round"/>
<rect x="60" y="20" width="16" height="12" rx="3" fill="#DCEBFF" stroke="#2F8CF0"/><text x="84" y="31">vendite</text>
<line x1="190" y1="26" x2="214" y2="26" stroke="#E8590C" stroke-width="3"/><text x="222" y="31">media7 = rolling(7).mean()</text>
<g font-size="12" fill="#5B5880"><text x="60" y="230">1</text><text x="181" y="230">7</text><text x="313" y="230">14</text><text x="446" y="230">21</text><text x="573" y="230">28</text></g>
</svg>
SVG;
    }
}

/** pivot_table turns a long table into a wide one; melt goes back. */
if (!function_exists('fig_pd_pivot')) {
    function fig_pd_pivot(): string
    {
        $long = '';
        foreach ([['Nord', 2025, 300], ['Nord', 2026, 350], ['Sud', 2025, 120], ['Sud', 2026, 140], ['Centro', 2025, 90], ['Centro', 2026, 100]] as $i => [$r, $a, $v]) {
            $y = 66 + $i * 26;
            $fill = $a === 2025 ? '#DCEBFF' : '#FFE8D6';
            $long .= "<rect x=\"16\" y=\"$y\" width=\"230\" height=\"26\" fill=\"#FFFFFF\"/><text x=\"26\" y=\"" . ($y + 18) . "\">$r</text><rect x=\"110\" y=\"" . ($y + 3) . "\" width=\"52\" height=\"20\" rx=\"5\" fill=\"$fill\"/><text x=\"136\" y=\"" . ($y + 18) . "\" text-anchor=\"middle\">$a</text><text x=\"236\" y=\"" . ($y + 18) . "\" text-anchor=\"end\">$v</text>";
        }
        $wide = '';
        foreach ([['Nord', 300, 350], ['Sud', 120, 140], ['Centro', 90, 100]] as $i => [$r, $a, $b]) {
            $y = 92 + $i * 30;
            $wide .= "<text x=\"404\" y=\"" . ($y + 20) . "\">$r</text><text x=\"538\" y=\"" . ($y + 20) . "\" text-anchor=\"end\">$a</text><text x=\"610\" y=\"" . ($y + 20) . "\" text-anchor=\"end\">$b</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="ap" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="40" width="230" height="26" fill="#ECE6FF"/>
<text x="26" y="58" font-weight="700">regione</text><text x="136" y="58" text-anchor="middle" font-weight="700">anno</text><text x="236" y="58" text-anchor="end" font-weight="700">importo</text>
$long
<rect x="16" y="40" width="230" height="182" rx="6" fill="none" stroke="#8A87B0" stroke-width="2"/>
<rect x="394" y="62" width="230" height="30" fill="#ECE6FF"/>
<text x="404" y="82" font-weight="700">regione</text>
<rect x="484" y="66" width="60" height="22" rx="5" fill="#DCEBFF"/><text x="538" y="82" text-anchor="end" font-weight="700">2025</text>
<rect x="556" y="66" width="60" height="22" rx="5" fill="#FFE8D6"/><text x="610" y="82" text-anchor="end" font-weight="700">2026</text>
$wide
<rect x="394" y="62" width="230" height="120" rx="6" fill="none" stroke="#8A87B0" stroke-width="2"/>
<line x1="258" y1="100" x2="384" y2="100" stroke="#1E1C52" stroke-width="2" marker-end="url(#ap)"/>
<text x="320" y="90" text-anchor="middle" font-size="13" fill="#5B33C9">pivot_table</text>
<line x1="384" y1="160" x2="258" y2="160" stroke="#1E1C52" stroke-width="2" marker-end="url(#ap)"/>
<text x="320" y="182" text-anchor="middle" font-size="13" fill="#0B7F5C">melt</text>
</svg>
SVG;
    }
}

return [
    'id' => 'python-pandas', 'family' => 'python', 'after' => 'python-base', 'categories' => ['data'],
    'title' => L('Analisi dati con pandas', 'Data Analysis with pandas'), 'short' => L('pandas', 'pandas'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Analizzare dati con Python: DataFrame, caricare ed esplorare, selezionare e filtrare, pulire, raggruppare e unire tabelle, grafici veloci, serie temporali, nuove colonne, pivot e dati grandi.', 'Analysing data with Python: DataFrames, loading and exploring, selecting and filtering, cleaning, grouping and joining tables, quick charts, time series, new columns, pivots and big data.'),
    'lessons' => [
        lesson('dataframe', L('DataFrame ed esplorazione', 'DataFrames and exploration'), [
            P('**pandas** è la libreria Python per le tabelle di dati. Un **DataFrame** è una tabella con righe e colonne con nome; ogni colonna è una **Series**. Si carica spesso da CSV, Excel o database.', '**pandas** is the Python library for data tables. A **DataFrame** is a table with named rows and columns; each column is a **Series**. It is often loaded from CSV, Excel or databases.'),
            Fig(L(fig_pd_df(['indice', 'nomi delle colonne', 'una riga']), fig_pd_df(['index', 'column names', 'a row'])),
                L('Una tabella di quattro righe e tre colonne: data, regione e importo. A sinistra la colonna grigia dell’indice con 0, 1, 2 e 3; in alto i nomi delle colonne; la colonna importo è contornata in blu ed etichettata Series; la riga 2 è tratteggiata in arancione. Sotto: shape = (4, 3).', 'A table of four rows and three columns: data, regione and importo. On the left the grey index column with 0, 1, 2 and 3; at the top the column names; the importo column is outlined in blue and labelled Series; row 2 is dashed in orange. Below: shape = (4, 3).'),
                L('L’indice dà un’etichetta a ogni riga; ogni colonna è una Series.', 'The index labels every row; every column is a Series.')),
            C('import pandas as pd

vendite = pd.read_csv("vendite.csv", parse_dates=["data"])
print(vendite.shape)        # (righe, colonne)
print(vendite.head())       # prime 5 righe
vendite.info()              # tipi e valori mancanti
print(vendite.describe())   # statistiche delle colonne numeriche', 'python', '<style>table{border-collapse:collapse;font:14px system-ui}th,td{border:1px solid #D9D7EA;padding:4px 10px;text-align:right}th{background:#F1F0F8}</style>
<pre>(4, 3)</pre>
<table><tr><th></th><th>data</th><th>regione</th><th>importo</th></tr>
<tr><th>0</th><td>2026-01-03</td><td>Nord</td><td>120.0</td></tr>
<tr><th>1</th><td>2026-01-05</td><td>Sud</td><td>80.5</td></tr>
<tr><th>2</th><td>2026-01-09</td><td>Nord</td><td>230.0</td></tr>
<tr><th>3</th><td>2026-01-12</td><td>Centro</td><td>45.0</td></tr></table>'),
            P('`head`, `info` e `describe` sono i primi tre comandi da lanciare su un dataset nuovo: ti dicono forma, tipi, valori mancanti e ordini di grandezza.', '`head`, `info` and `describe` are the first three commands to run on a new dataset: they tell you shape, types, missing values and orders of magnitude.'),
            P('L’**indice** è l’etichetta delle righe: se non dici altro vale 0, 1, 2… ma può essere anche una data o un codice cliente. Ogni colonna ha un solo tipo (`df.dtypes` li elenca): `int64`, `float64`, `datetime64`, `str` per il testo. Se una colonna di numeri risulta di tipo testo, quasi sempre c’è un valore sporco dentro. Nota che `shape` è un attributo e si scrive senza parentesi, mentre `head()` è un metodo.', 'The **index** is the label of the rows: unless you say otherwise it is 0, 1, 2… but it can also be a date or a customer code. Each column has a single type (`df.dtypes` lists them): `int64`, `float64`, `datetime64`, `str` for text. If a column of numbers turns out to be text, there is almost always a dirty value in it. Note that `shape` is an attribute and is written without brackets, while `head()` is a method.'),
        ], [
            Q('d1', L('Che cos’è un DataFrame?', 'What is a DataFrame?'), [L('Una tabella con righe e colonne con nome', 'A table with named rows and columns'), L('Un grafico', 'A chart'), L('Un database remoto', 'A remote database'), L('Un tipo di file', 'A file type')], 0, L('Ogni colonna è una Series.', 'Each column is a Series.')),
            Q('d2', L('Che cosa mostra `df.info()`?', 'What does `df.info()` show?'), [L('Tipi delle colonne e valori mancanti', 'Column types and missing values'), L('Il grafico dei dati', 'The data chart'), L('Solo la prima riga', 'Only the first row'), L('La media', 'The mean')], 0, L('`describe` dà le statistiche.', '`describe` gives the statistics.')),
            Q('d3', L('Che cosa restituisce `df.shape`?', 'What does `df.shape` return?'), [L('(righe, colonne)', '(rows, columns)'), L('Il nome del file', 'The file name'), L('Le colonne', 'The columns'), L('Il tipo di dati', 'The data type')], 0, L('Una tupla.', 'A tuple.')),
        ], task('python', L('Carica `clienti.csv` in `df` e stampa le prime 10 righe con `head(10)`.', 'Load `clienti.csv` into `df` and print the first 10 rows with `head(10)`.'), "import pandas as pd\n", [re('df\s*=\s*pd\.read_csv\s*\(\s*["\']clienti\.csv["\']', L('Carichi il file', 'You load the file')), re('print\s*\(\s*df\.head\s*\(\s*10\s*\)\s*\)', L('Stampi 10 righe', 'You print 10 rows'))], '', "df = pd.read_csv(\"clienti.csv\")\nprint(df.head(10))"), [
            dMatch(pMatch(), [['read_csv', L('Carica un CSV', 'Loads a CSV')], ['head', L('Prime righe', 'First rows')], ['info', L('Tipi e mancanti', 'Types and missing')], ['describe', L('Statistiche', 'Statistics')], ['shape', L('Dimensioni', 'Dimensions')]], L('Esplorare un dataset.', 'Exploring a dataset.')),
            dFlash(pFlash(), [[L('Ogni colonna di un DataFrame è una Series', 'Each DataFrame column is a Series'), true], [L('pandas legge solo file CSV', 'pandas only reads CSV files'), false], [L('describe riassume le colonne numeriche', 'describe summarises numeric columns'), true], [L('head mostra le ultime righe', 'head shows the last rows'), false]], L('Primo sguardo ai dati.', 'First look at data.')),
        ]),
        lesson('select', L('Selezionare e filtrare', 'Selecting and filtering'), [
            P('Una colonna si prende con `df["colonna"]`, più colonne con una lista `df[["a", "b"]]`. Per filtrare le righe si usa una **maschera booleana**; più condizioni si combinano con `&` (e) e `|` (o), ognuna tra parentesi.', 'A column is taken with `df["column"]`, several with a list `df[["a", "b"]]`. To filter rows you use a **boolean mask**; several conditions combine with `&` (and) and `|` (or), each in parentheses.'),
            Fig(L(fig_pd_mask(['maschera', 'Il confronto dà una colonna di True e False; restano solo le righe True.']), fig_pd_mask(['mask', 'The comparison gives a column of True and False; only the True rows stay.'])),
                L('A sinistra la colonna importo con 120.0, 80.5, 230.0 e 45.0. Al centro il risultato di > 100: True, False, True, False. A destra restano le righe 0 e 2, con 120.0 e 230.0.', 'On the left the importo column with 120.0, 80.5, 230.0 and 45.0. In the middle the result of > 100: True, False, True, False. On the right rows 0 and 2 remain, with 120.0 and 230.0.'),
                L('Una maschera booleana sceglie le righe da tenere.', 'A boolean mask picks the rows to keep.')),
            C('grandi = vendite[vendite["importo"] > 100]
nord = vendite[(vendite["regione"] == "Nord") & (vendite["importo"] > 100)]
alcune = vendite[vendite["citta"].isin(["Udine", "Trieste"])]

vendite.loc[vendite["importo"] > 100, ["cliente", "importo"]]   # righe e colonne per etichetta
vendite.sort_values("importo", ascending=False).head(5)', 'python'),
            P('`loc` seleziona per etichette (righe e colonne insieme), `iloc` per posizione. Con pandas non si usano `and`/`or` di Python sulle colonne: danno errore.', '`loc` selects by labels (rows and columns together), `iloc` by position. In pandas you do not use Python’s `and`/`or` on columns: they raise an error.'),
            P('`vendite["importo"] > 100` non è un solo `True` o `False`: è una Series lunga quanto la tabella, un valore per riga. Mettendola tra le quadre, pandas tiene le righe dove c’è `True`. Puoi anche salvarla in una variabile, `grandi = vendite["importo"] > 100`, e riusarla o negarla con `~grandi`. Le parentesi intorno a ogni condizione servono perché `&` e `|` hanno la precedenza sui confronti.', '`vendite["importo"] > 100` is not a single `True` or `False`: it is a Series as long as the table, one value per row. Putting it inside the square brackets, pandas keeps the rows where it finds `True`. You can also store it in a variable, `grandi = vendite["importo"] > 100`, and reuse it or negate it with `~grandi`. The brackets around each condition are needed because `&` and `|` take precedence over comparisons.'),
        ], [
            Q('s1', L('Come combini due condizioni con “e”?', 'How do you combine two conditions with “and”?'), [L('Con & e ogni condizione tra parentesi', 'With & and each condition in parentheses'), L('Con and', 'With and'), L('Con +', 'With +'), L('Con ,', 'With ,')], 0, L('`and` dà errore sulle Series.', '`and` errors on Series.')),
            Q('s2', L('Che cosa fa `isin([...])`?', 'What does `isin([...])` do?'), [L('Controlla se il valore è in un elenco', 'Checks whether the value is in a list'), L('Ordina', 'Sorts'), L('Conta', 'Counts'), L('Cancella', 'Deletes')], 0, L('Come IN in SQL.', 'Like IN in SQL.')),
            Q('s3', L('Che differenza c’è tra loc e iloc?', 'What is the difference between loc and iloc?'), [L('loc per etichette, iloc per posizione', 'loc by labels, iloc by position'), L('Nessuna', 'None'), L('iloc è più vecchio', 'iloc is older'), L('loc solo per colonne', 'loc only for columns')], 0, L('i = integer.', 'i = integer.')),
        ], task('python', L('Da `df` tieni in `giovani` le righe con `eta < 30` e `citta == "Roma"`.', 'From `df` keep in `giovani` the rows with `eta < 30` and `citta == "Roma"`.'), '', [re('giovani\s*=\s*df\s*\[', L('Filtri df', 'You filter df')), re('\(\s*df\s*\[\s*["\']eta["\']\s*\]\s*<\s*30\s*\)\s*&\s*\(\s*df\s*\[\s*["\']citta["\']\s*\]\s*==\s*["\']Roma["\']\s*\)|\(\s*df\s*\[\s*["\']citta["\']\s*\]\s*==\s*["\']Roma["\']\s*\)\s*&\s*\(\s*df\s*\[\s*["\']eta["\']\s*\]\s*<\s*30\s*\)', L('Due condizioni con &', 'Two conditions with &'))], '', "giovani = df[(df[\"eta\"] < 30) & (df[\"citta\"] == \"Roma\")]"), [
            dBug(pBug(), ['import pandas as pd', 'df = pd.read_csv("ordini.csv")', 'grandi = df[df["totale"] > 50 and df["pagato"] == True]'], 2, 'grandi = df[(df["totale"] > 50) & (df["pagato"] == True)]', L('Con pandas si usa & e le parentesi.', 'pandas uses & and parentheses.')),
            dMatch(pMatch(), [['df["a"]', L('Una colonna', 'One column')], ['df[["a", "b"]]', L('Più colonne', 'Several columns')], ['df[df["x"] > 0]', L('Filtro sulle righe', 'Row filter')], ['sort_values', L('Ordina', 'Sorts')]], L('Prendere i dati che servono.', 'Taking the data you need.')),
        ]),
        lesson('clean', L('Pulire i dati', 'Cleaning data'), [
            P('I dati veri sono sporchi: valori mancanti (`NaN`), doppioni, tipi sbagliati, testi scritti in modi diversi. pandas ha gli strumenti per sistemarli; la regola è **guardare prima di cancellare**.', 'Real data is dirty: missing values (`NaN`), duplicates, wrong types, text written in different ways. pandas has the tools to fix them; the rule is **look before you delete**.'),
            C('print(df.isna().sum())                          # mancanti per colonna
df["eta"] = df["eta"].fillna(df["eta"].median())
df = df.dropna(subset=["email"])                # senza email non serve
df = df.drop_duplicates(subset=["email"])
df["citta"] = df["citta"].str.strip().str.title()   # " udine " → "Udine"
df["importo"] = pd.to_numeric(df["importo"], errors="coerce")
df = df.rename(columns={"tel": "telefono"})', 'python'),
            P('`errors="coerce"` trasforma in `NaN` i valori che non sono numeri, così puoi trovarli. Le operazioni restituiscono un nuovo DataFrame: ricordati di riassegnarlo.', '`errors="coerce"` turns non-numeric values into `NaN`, so you can find them. Operations return a new DataFrame: remember to reassign it.'),
            P('Prima di correggere, guarda: `df["citta"].value_counts()` mostra subito se «Udine», « udine» e «UDINE» sono finite come tre città diverse. Riempire o cancellare dipende dal significato: un’età mancante si può stimare con la mediana (che, a differenza della media, non si lascia trascinare dai valori estremi), un’email mancante no. Dopo `to_numeric`, controlla quante righe sono diventate `NaN`: potrebbero essere molte più di quante pensi.', 'Before fixing, look: `df["citta"].value_counts()` shows straight away whether “Udine”, “ udine” and “UDINE” ended up as three different cities. Filling or dropping depends on the meaning: a missing age can be estimated with the median (which, unlike the mean, is not dragged around by extreme values), a missing email cannot. After `to_numeric`, check how many rows turned into `NaN`: there could be many more than you think.'),
        ], [
            Q('c1', L('Come conti i valori mancanti per colonna?', 'How do you count missing values per column?'), ['df.isna().sum()', 'df.count()', 'df.missing()', 'df.null()'], 0, L('True conta come 1.', 'True counts as 1.')),
            Q('c2', L('Che cosa fa `fillna(df["eta"].median())`?', 'What does `fillna(df["eta"].median())` do?'), [L('Riempie i mancanti con la mediana', 'Fills missing with the median'), L('Cancella le righe', 'Deletes rows'), L('Ordina per età', 'Sorts by age'), L('Calcola la media', 'Computes the mean')], 0, L('Un’imputazione semplice.', 'A simple imputation.')),
            Q('c3', L('Che cosa fa `str.strip().str.title()`?', 'What does `str.strip().str.title()` do?'), [L('Toglie gli spazi e mette le iniziali maiuscole', 'Trims spaces and capitalises initials'), L('Cancella il testo', 'Deletes the text'), L('Traduce', 'Translates'), L('Conta le lettere', 'Counts letters')], 0, L('Testi uniformi.', 'Uniform text.')),
        ], task('python', L('Togli da `df` le righe doppie in base alla colonna `codice` e riempi i mancanti di `prezzo` con `0`.', 'Remove from `df` the duplicate rows based on the `codice` column and fill missing `prezzo` values with `0`.'), '', [re('df\s*=\s*df\.drop_duplicates\s*\(\s*subset\s*=\s*\[\s*["\']codice["\']\s*\]\s*\)', L('Niente doppioni', 'No duplicates')), re('df\s*\[\s*["\']prezzo["\']\s*\]\s*=\s*df\s*\[\s*["\']prezzo["\']\s*\]\.fillna\s*\(\s*0\s*\)', L('Prezzi mancanti a 0', 'Missing prices to 0'))], '', "df = df.drop_duplicates(subset=[\"codice\"])\ndf[\"prezzo\"] = df[\"prezzo\"].fillna(0)"), [
            dMatch(pMatch(), [['isna', L('Trova i mancanti', 'Finds missing')], ['fillna', L('Riempie i mancanti', 'Fills missing')], ['dropna', L('Toglie righe con mancanti', 'Drops rows with missing')], ['drop_duplicates', L('Toglie doppioni', 'Removes duplicates')], ['to_numeric', L('Converte in numero', 'Converts to number')]], L('Gli attrezzi della pulizia.', 'Cleaning tools.')),
            dBug(pBug(), ['df = pd.read_csv("clienti.csv")', 'df.drop_duplicates(subset=["email"])', 'print(len(df))'], 1, 'df = df.drop_duplicates(subset=["email"])', L('Il risultato va riassegnato.', 'The result must be reassigned.')),
        ]),
        lesson('group', L('Raggruppare, unire, disegnare', 'Grouping, joining, plotting'), [
            P('**`groupby`** divide i dati in gruppi e calcola qualcosa per ciascuno, come `GROUP BY` in SQL. **`merge`** unisce due tabelle su una colonna comune, come una `JOIN`. Con `.plot()` ottieni un grafico veloce (con matplotlib installato).', '**`groupby`** splits data into groups and computes something for each, like `GROUP BY` in SQL. **`merge`** joins two tables on a common column, like a `JOIN`. With `.plot()` you get a quick chart (with matplotlib installed).'),
            Fig(L(fig_pd_group(['dividi', 'applica', 'combina', 'Una riga per gruppo nel risultato, con la somma di ciascuno.']), fig_pd_group(['split', 'apply', 'combine', 'One row per group in the result, with the sum of each.'])),
                L('A sinistra cinque righe di vendite: Nord 120, Sud 80, Nord 230, Centro 45, Sud 60. Al centro sono divise in tre gruppi: Nord con 120 e 230, Sud con 80 e 60, Centro con 45. A destra, dopo sum, il risultato: Nord 350, Sud 140, Centro 45.', 'On the left five sales rows: Nord 120, Sud 80, Nord 230, Centro 45, Sud 60. In the middle they are split into three groups: Nord with 120 and 230, Sud with 80 and 60, Centro with 45. On the right, after sum, the result: Nord 350, Sud 140, Centro 45.'),
                L('`groupby` divide, applica una funzione a ogni gruppo e ricompone.', '`groupby` splits, applies a function to each group and puts it back together.')),
            C('per_regione = vendite.groupby("regione")["importo"].agg(["sum", "mean", "count"])
print(per_regione.sort_values("sum", ascending=False))

completo = vendite.merge(clienti, on="cliente_id", how="left")

mensili = vendite.groupby(vendite["data"].dt.to_period("M"))["importo"].sum()
mensili.plot(kind="bar", title="Vendite mensili")', 'python', '<style>table{border-collapse:collapse;font:14px system-ui}th,td{border:1px solid #D9D7EA;padding:4px 10px;text-align:right}th{background:#F1F0F8}</style>
<table><tr><th></th><th>sum</th><th>mean</th><th>count</th></tr>
<tr><th>regione</th><td></td><td></td><td></td></tr>
<tr><th>Nord</th><td>350.0</td><td>175.0</td><td>2</td></tr>
<tr><th>Sud</th><td>140.0</td><td>70.0</td><td>2</td></tr>
<tr><th>Centro</th><td>45.0</td><td>45.0</td><td>1</td></tr></table>'),
            P('`how="left"` tiene tutte le righe della tabella di sinistra anche senza corrispondenza, come una `LEFT JOIN`. `.dt` dà accesso alle parti delle date (anno, mese, giorno della settimana).', '`how="left"` keeps all left-table rows even without a match, like a `LEFT JOIN`. `.dt` gives access to date parts (year, month, weekday).'),
            P('Il risultato di un `groupby` ha i gruppi come **indice**: se ti servono come colonna normale, per esportare o unire, aggiungi `.reset_index()` (oppure `as_index=False`). Con `merge` attenzione ai doppioni: se un `cliente_id` compare due volte nella tabella dei clienti, ogni vendita di quel cliente si sdoppia e i totali risultano gonfiati. `validate="many_to_one"` fa fallire subito l’unione in quel caso.', 'The result of a `groupby` has the groups as its **index**: if you need them as an ordinary column, to export or join, add `.reset_index()` (or `as_index=False`). With `merge` watch out for duplicates: if a `cliente_id` appears twice in the customer table, every sale of that customer is doubled and the totals come out inflated. `validate="many_to_one"` makes the join fail straight away in that case.'),
        ], [
            Q('g1', L('A che cosa corrisponde `groupby` in SQL?', 'What does `groupby` correspond to in SQL?'), ['GROUP BY', 'ORDER BY', 'WHERE', 'JOIN'], 0, L('Con funzioni di aggregazione.', 'With aggregate functions.')),
            Q('g2', L('Che cosa fa `merge(..., how="left")`?', 'What does `merge(..., how="left")` do?'), [L('Tiene tutte le righe di sinistra', 'Keeps all left rows'), L('Tiene solo le corrispondenze', 'Keeps only matches'), L('Cancella la tabella destra', 'Deletes the right table'), L('Ordina', 'Sorts')], 0, L('Come una LEFT JOIN.', 'Like a LEFT JOIN.')),
            Q('g3', L('A che cosa serve `.dt`?', 'What is `.dt` for?'), [L('Accedere alle parti delle date', 'Accessing date parts'), L('Cancellare le date', 'Deleting dates'), L('Disegnare grafici', 'Drawing charts'), L('Leggere un file', 'Reading a file')], 0, L('Anno, mese, giorno…', 'Year, month, day…')),
        ], task('python', L('Calcola in `totali` la somma di `importo` per `categoria` con `groupby`, poi ordina dal più alto.', 'Compute in `totali` the sum of `importo` per `categoria` with `groupby`, then sort from highest.'), '', [re('totali\s*=\s*df\.groupby\s*\(\s*["\']categoria["\']\s*\)\s*\[\s*["\']importo["\']\s*\]\.sum\s*\(\s*\)', L('Raggruppi e sommi', 'You group and sum')), re('sort_values\s*\(\s*(ascending\s*=\s*False)\s*\)', L('Ordini dal più alto', 'You sort from highest'))], '', "totali = df.groupby(\"categoria\")[\"importo\"].sum()\ntotali = totali.sort_values(ascending=False)"), [
            dOrder(pSort(), ['vendite = pd.read_csv("vendite.csv")', 'vendite = vendite.dropna(subset=["importo"])', 'per_regione = vendite.groupby("regione")["importo"].sum()', 'per_regione.sort_values(ascending=False).plot(kind="bar")'], L('Carica, pulisci, raggruppa, disegna.', 'Load, clean, group, plot.')),
            dMatch(pMatch(), [['groupby', 'GROUP BY'], ['merge', 'JOIN'], ['sort_values', 'ORDER BY'], ['df[df.x > 0]', 'WHERE'], ['agg(["sum", "mean"])', L('Più aggregazioni', 'Several aggregations')]], L('pandas e SQL a confronto.', 'pandas and SQL compared.')),
        ]),
        lesson('time', L('Date, serie temporali ed esportazione', 'Dates, time series and export'), [
            P('Le date lette da un CSV sono testo: `pd.to_datetime()` le converte. Con la colonna data come indice, `resample("ME")` raggruppa per mese (ME = fine mese; il vecchio alias `"M"` non è più accettato da pandas 3.0) e `rolling(7).mean()` calcola una media mobile su 7 righe.', 'Dates read from a CSV are text: `pd.to_datetime()` converts them. With the date column as index, `resample("ME")` groups by month (ME = month end; the old `"M"` alias is no longer accepted since pandas 3.0) and `rolling(7).mean()` computes a 7-row moving average.'),
            Fig(fig_pd_rolling(),
                L('Grafico a barre di 28 giorni di vendite, che salgono e scendono molto da un giorno all’altro. Sopra, una linea arancione media7 parte dal settimo giorno ed è molto più regolare.', 'Bar chart of 28 days of sales, going up and down a lot from one day to the next. Over it, an orange media7 line starts on the seventh day and is much smoother.'),
                L('La media mobile spiana gli sbalzi e mostra la tendenza.', 'The rolling mean smooths the jumps and shows the trend.')),
            C('df["data"] = pd.to_datetime(df["data"])
df = df.set_index("data")
mensile = df["vendite"].resample("ME").sum()
df["media7"] = df["vendite"].rolling(7).mean()
df["mese"] = df.index.month

mensile.to_csv("vendite_mensili.csv")
df.to_excel("report.xlsx", sheet_name="Dati")', 'python'),
            P('Alla fine si **esporta**: `to_csv`, `to_excel`, `to_json`. Per file grandi, `to_parquet` è più compatto e veloce. Aggiungi `index=False` se l’indice non serve nel file.', 'At the end you **export**: `to_csv`, `to_excel`, `to_json`. For large files, `to_parquet` is more compact and faster. Add `index=False` if the index is not needed in the file.'),
        ], [
            Q('ti1', L('Come converti una colonna di testo in date?', 'How do you convert a text column into dates?'), ['pd.to_datetime()', 'df.astype("date")', 'pd.date()', 'df.parse()'], 0, L('Poi puoi usare `.dt` e `resample`.', 'Then you can use `.dt` and `resample`.')),
            Q('ti2', L('Che cosa fa `resample("ME").sum()`?', 'What does `resample("ME").sum()` do?'), [L('Somma per mese', 'Sums by month'), L('Ordina per mese', 'Sorts by month'), L('Cancella i mesi vuoti', 'Deletes empty months'), L('Conta le righe', 'Counts rows')], 0, L('Serve un indice di date.', 'It needs a date index.')),
            Q('ti3', L('A che cosa serve `index=False` in `to_csv`?', 'What is `index=False` for in `to_csv`?'), [L('Non scrivere l’indice nel file', 'Not writing the index to the file'), L('Ordinare le righe', 'Sorting rows'), L('Comprimere il file', 'Compressing the file'), L('Saltare le intestazioni', 'Skipping headers')], 0, L('Spesso l’indice è solo un numero di riga.', 'Often the index is just a row number.')),
        ], task('python', L('Converti `df["giorno"]` in date con `pd.to_datetime` e salva `df` in `pulito.csv` senza indice.', 'Convert `df["giorno"]` to dates with `pd.to_datetime` and save `df` to `pulito.csv` without the index.'), '', [re('df\[\s*["\']giorno["\']\s*\]\s*=\s*pd\.to_datetime\(\s*df\[\s*["\']giorno["\']\s*\]\s*\)', L('Converti le date', 'You convert the dates')), re('df\.to_csv\(\s*["\']pulito\.csv["\']\s*,\s*index\s*=\s*False\s*\)', L('Esporti senza indice', 'You export without index'))], '', "df[\"giorno\"] = pd.to_datetime(df[\"giorno\"])\ndf.to_csv(\"pulito.csv\", index=False)"), [
            dOrder(pSort(), ['df = pd.read_csv("vendite.csv")', 'df["data"] = pd.to_datetime(df["data"])', 'df = df.set_index("data")', 'mensile = df["vendite"].resample("ME").sum()', 'mensile.to_csv("mensile.csv")'], L('Leggi, converti, indicizza, raggruppa, esporta.', 'Read, convert, index, group, export.')),
            dMatch(pMatch(), [['to_datetime', L('Testo in date', 'Text to dates')], ['resample', L('Raggruppa nel tempo', 'Groups over time')], ['rolling', L('Media mobile', 'Moving average')], ['to_excel', L('Esporta in Excel', 'Exports to Excel')], ['to_parquet', L('Formato compatto', 'Compact format')]], L('Tempo ed esportazione.', 'Time and export.')),
        ]),
        lesson('new-columns', L('Nuove colonne e operazioni vettoriali', 'New columns and vectorised operations'), [
            P('Una nuova colonna nasce da un’operazione tra colonne: `df["totale"] = df["prezzo"] * df["quantita"]`. Le operazioni sono **vettoriali**: lavorano su tutte le righe insieme, senza cicli, e sono molto più veloci di un `for` o di `apply`.', 'A new column comes from an operation between columns: `df["totale"] = df["prezzo"] * df["quantita"]`. Operations are **vectorised**: they work on every row at once, with no loops, and are much faster than a `for` or `apply`.'),
            C('import numpy as np

vendite["totale"] = vendite["prezzo"] * vendite["quantita"]
vendite["iva"] = (vendite["totale"] * 0.22).round(2)
vendite["fascia"] = np.where(vendite["totale"] > 100, "alta", "bassa")
vendite["mese"] = vendite["data"].dt.month
vendite["cliente"] = vendite["cliente"].str.upper()

vendite = vendite.assign(netto=lambda d: d["totale"] - d["sconto"])
vendite["livello"] = pd.cut(vendite["totale"], bins=[0, 50, 200, float("inf")],
                            labels=["piccolo", "medio", "grande"])', 'python'),
            P('`np.where` sceglie tra due valori secondo una condizione, `pd.cut` divide un numero in fasce, `assign` aggiunge colonne dentro una catena di metodi. `.str` e `.dt` applicano metodi di testo e di data a tutta la colonna.', '`np.where` picks between two values by a condition, `pd.cut` splits a number into bands, `assign` adds columns inside a method chain. `.str` and `.dt` apply text and date methods to the whole column.'),
        ], [
            Q('nc1', L('Come crei la colonna `totale` uguale a prezzo per quantità?', 'How do you create the `totale` column equal to price times quantity?'), ['df["totale"] = df["prezzo"] * df["quantita"]', 'df.totale(prezzo * quantita)', 'for r in df: r.totale = r.prezzo * r.quantita', 'df.add("totale", "prezzo*quantita")'], 0, L('Un’operazione tra colonne, riga per riga, senza ciclo.', 'An operation between columns, row by row, with no loop.')),
            Q('nc2', L('Che cosa fa `np.where(cond, "alta", "bassa")`?', 'What does `np.where(cond, "alta", "bassa")` do?'), [L('Cerca la posizione della prima riga vera', 'Finds the position of the first true row'), L('Filtra le righe che rispettano la condizione', 'Filters the rows meeting the condition'), L('Dà "alta" dove la condizione è vera, "bassa" altrove', 'Gives "alta" where the condition is true, "bassa" elsewhere'), L('Conta le righe vere', 'Counts the true rows')], 2, L('Una scelta per ogni riga.', 'One choice per row.')),
            Q('nc3', L('Perché evitare un ciclo for sulle righe?', 'Why avoid a for loop over the rows?'), [L('Dà sempre risultati sbagliati', 'It always gives wrong results'), L('Le operazioni vettoriali sono molto più veloci', 'Vectorised operations are much faster'), L('pandas non permette i cicli', 'pandas does not allow loops'), L('I cicli cancellano l’indice', 'Loops delete the index')], 1, L('Il lavoro avviene in codice compilato, non riga per riga in Python.', 'The work happens in compiled code, not row by row in Python.')),
        ], task('python', L('Aggiungi a `df` la colonna `prezzo_ivato` uguale a `prezzo` per `1.22`.', 'Add to `df` the column `prezzo_ivato` equal to `prezzo` times `1.22`.'), '', [re('df\s*\[\s*["\']prezzo_ivato["\']\s*\]\s*=\s*(df\s*\[\s*["\']prezzo["\']\s*\]\s*\*\s*1\.22|1\.22\s*\*\s*df\s*\[\s*["\']prezzo["\']\s*\])', L('La colonna è calcolata', 'The column is computed'))], '', 'df["prezzo_ivato"] = df["prezzo"] * 1.22'), [
            dBlanks(pFill(), 'df["totale"] = df["prezzo"] ___ df["pezzi"]
df["tipo"] = np.___(df["totale"] > 50, "grande", "piccolo")
df["anno"] = df["data"].___.year', ['*', 'where', 'dt'], ['x', 'if', 'date'], L('Moltiplica, scegli, estrai l’anno.', 'Multiply, choose, extract the year.')),
            dMatch(pMatch(), [['np.where', L('Scelta tra due valori', 'Choice between two values')], ['pd.cut', L('Fasce di valori', 'Value bands')], ['assign', L('Nuove colonne in catena', 'New columns in a chain')], ['.str', L('Metodi dei testi', 'Text methods')], ['.dt', L('Parti delle date', 'Date parts')]], L('Creare colonne senza cicli.', 'Creating columns without loops.')),
        ]),
        lesson('pivot', L('Tabelle pivot e forma dei dati', 'Pivot tables and data shape'), [
            P('Una **pivot** porta i valori di una colonna in intestazione: una riga per regione, una colonna per anno, le somme nelle celle, come in Excel. `melt` fa il contrario: da tabella larga a lunga, il formato preferito da molte librerie di grafici. `concat` impila tabelle con le stesse colonne.', 'A **pivot** moves a column’s values into the header: one row per region, one column per year, sums in the cells, just like Excel. `melt` does the opposite: from wide to long, the format many charting libraries prefer. `concat` stacks tables with the same columns.'),
            Fig(fig_pd_pivot(),
                L('A sinistra una tabella lunga con le colonne regione, anno e importo e sei righe, due per regione. Una freccia pivot_table porta a destra a una tabella larga con una riga per regione e le colonne 2025 e 2026; una freccia melt torna indietro.', 'On the left a long table with the columns regione, anno and importo and six rows, two per region. A pivot_table arrow leads right to a wide table with one row per region and the columns 2025 and 2026; a melt arrow goes back.'),
                L('Stessi dati, due forme: lunga per i grafici, larga per leggere.', 'Same data, two shapes: long for charts, wide for reading.')),
            C('pivot = vendite.pivot_table(
    index="regione", columns="anno", values="importo",
    aggfunc="sum", fill_value=0,
)

lungo = pivot.reset_index().melt(id_vars="regione", var_name="anno", value_name="importo")

pd.crosstab(vendite["regione"], vendite["canale"])      # conteggi incrociati
tutto = pd.concat([gennaio, febbraio], ignore_index=True)', 'python', '<style>table{border-collapse:collapse;font:14px system-ui}th,td{border:1px solid #D9D7EA;padding:4px 10px;text-align:right}th{background:#F1F0F8}</style>
<table><tr><th>anno</th><th>2025</th><th>2026</th></tr>
<tr><th>regione</th><td></td><td></td></tr>
<tr><th>Centro</th><td>90</td><td>100</td></tr>
<tr><th>Nord</th><td>300</td><td>350</td></tr>
<tr><th>Sud</th><td>120</td><td>140</td></tr></table>'),
            P('In `pivot_table` l’aggregazione predefinita è la **media**: per i totali scrivi `aggfunc="sum"`. `fill_value` riempie le combinazioni che non esistono.', 'In `pivot_table` the default aggregation is the **mean**: for totals write `aggfunc="sum"`. `fill_value` fills the combinations that do not exist.'),
        ], [
            Q('pv1', L('Che cosa produce `pivot_table(index="regione", columns="anno", values="importo", aggfunc="sum")`?', 'What does `pivot_table(index="regione", columns="anno", values="importo", aggfunc="sum")` produce?'), [L('Una riga per regione, una colonna per anno, somme nelle celle', 'One row per region, one column per year, sums in the cells'), L('Una riga per anno con la media delle regioni', 'One row per year with the regions’ mean'), L('Le vendite ordinate per regione e anno', 'Sales sorted by region and year'), L('La tabella senza le regioni vuote', 'The table without empty regions')], 0, L('Come una tabella pivot di Excel.', 'Like an Excel pivot table.')),
            Q('pv2', L('Che cosa fa `melt`?', 'What does `melt` do?'), [L('Unisce due tabelle su una chiave', 'Joins two tables on a key'), L('Trasforma una tabella larga in una lunga', 'Turns a wide table into a long one'), L('Toglie i valori mancanti', 'Removes missing values'), L('Arrotonda i numeri', 'Rounds numbers')], 1, L('Le colonne diventano righe.', 'Columns become rows.')),
            Q('pv3', L('Come impili due DataFrame con le stesse colonne?', 'How do you stack two DataFrames with the same columns?'), ['pd.merge(a, b)', 'a + b', 'pd.concat([a, b])', 'a.join(b)'], 2, L('`merge` e `join` uniscono invece su una chiave.', '`merge` and `join` combine on a key instead.')),
        ], task('python', L('Crea `tabella` con `df.pivot_table`: righe `negozio`, colonne `mese`, valori `incasso` sommati.', 'Create `tabella` with `df.pivot_table`: rows `negozio`, columns `mese`, values `incasso` summed.'), '', [re('tabella\s*=\s*df\.pivot_table\s*\(', L('Usi pivot_table', 'You use pivot_table')), re('index\s*=\s*["\']negozio["\']', L('Righe per negozio', 'Rows per shop')), re('columns\s*=\s*["\']mese["\']', L('Colonne per mese', 'Columns per month')), re('values\s*=\s*["\']incasso["\']', L('Valori dell’incasso', 'Takings values')), re('aggfunc\s*=\s*["\']sum["\']', L('Sommati', 'Summed'))], '', 'tabella = df.pivot_table(index="negozio", columns="mese", values="incasso", aggfunc="sum")'), [
            dMatch(pMatch(), [['pivot_table', L('Da lunga a larga, con aggregazione', 'Long to wide, with aggregation')], ['melt', L('Da larga a lunga', 'Wide to long')], ['concat', L('Impila tabelle', 'Stacks tables')], ['crosstab', L('Conta le combinazioni', 'Counts combinations')], ['fill_value', L('Valore per le celle vuote', 'Value for empty cells')]], L('Cambiare forma ai dati.', 'Reshaping data.')),
            dFlash(pFlash(), [[L('L’aggregazione predefinita di `pivot_table` è la media', 'The default aggregation of `pivot_table` is the mean'), true], [L('`melt` e `pivot_table` fanno la stessa cosa', '`melt` and `pivot_table` do the same thing'), false], [L('`concat` con `ignore_index=True` rinumera le righe', '`concat` with `ignore_index=True` renumbers the rows'), true], [L('Una pivot può avere più colonne di valori', 'A pivot can have several value columns'), true]], L('Larga per leggere, lunga per analizzare.', 'Wide to read, long to analyse.')),
        ]),
        lesson('big-data', L('Dati grandi: tipi, Parquet e memoria', 'Big data: types, Parquet and memory'), [
            P('Con milioni di righe la memoria conta. `usecols` carica solo le colonne necessarie, il tipo `category` fa occupare una frazione dello spazio ai testi ripetuti (regioni, categorie), `chunksize` legge un file troppo grande a pezzi. **Parquet** salva anche i tipi ed è molto più rapido del CSV.', 'With millions of rows memory matters. `usecols` loads only the columns you need, the `category` type makes repeated text (regions, categories) take a fraction of the space, `chunksize` reads a file that is too big in pieces. **Parquet** stores the types too and is much faster than CSV.'),
            C('vendite = pd.read_csv(
    "vendite.csv",
    usecols=["data", "regione", "importo"],
    dtype={"regione": "category"},
    parse_dates=["data"],
)
vendite.info(memory_usage="deep")

vendite.to_parquet("vendite.parquet")
veloce = pd.read_parquet("vendite.parquet", columns=["regione", "importo"])

for pezzo in pd.read_csv("enorme.csv", chunksize=100_000):
    print(pezzo["importo"].sum())', 'python'),
            Tip('Da pandas 3.0 il testo usa un tipo stringa dedicato (`str`) e il **Copy-on-Write** è sempre attivo: ogni selezione si comporta come una copia, quindi le assegnazioni a catena come `df[df["x"] > 0]["y"] = 1` non modificano più l’originale. Scrivi `df.loc[df["x"] > 0, "y"] = 1`.', 'Since pandas 3.0 text uses a dedicated string type (`str`) and **Copy-on-Write** is always on: every selection behaves like a copy, so chained assignments such as `df[df["x"] > 0]["y"] = 1` no longer change the original. Write `df.loc[df["x"] > 0, "y"] = 1`.'),
        ], [
            Q('pf1', L('Quando conviene il tipo `category`?', 'When is the `category` type worth it?'), [L('Per testi che si ripetono spesso, come le regioni', 'For text that repeats often, such as regions'), L('Per colonne numeriche con decimali', 'For numeric columns with decimals'), L('Per le date', 'For dates'), L('Per gli identificativi tutti diversi', 'For identifiers that are all different')], 0, L('Ogni valore distinto viene salvato una volta sola.', 'Each distinct value is stored only once.')),
            Q('pf2', L('Che cosa fa `chunksize=100_000` in `read_csv`?', 'What does `chunksize=100_000` do in `read_csv`?'), [L('Legge solo le prime 100.000 righe', 'Reads only the first 100,000 rows'), L('Comprime il file', 'Compresses the file'), L('Divide il file in più CSV', 'Splits the file into several CSVs'), L('Legge il file a pezzi da 100.000 righe', 'Reads the file in pieces of 100,000 rows')], 3, L('Restituisce un iteratore di DataFrame.', 'It returns an iterator of DataFrames.')),
            Q('pf3', L('Come modifichi in sicurezza alcune righe con pandas 3?', 'How do you safely change some rows with pandas 3?'), ['df[df.x > 0]["y"] = 1', 'df.loc[df.x > 0, "y"] = 1', 'df.y[df.x > 0] == 1', 'df.set(df.x > 0, y=1)'], 1, L('Un solo `loc` con righe e colonna insieme.', 'A single `loc` with rows and column together.')),
        ], task('python', L('Leggi in `df` il file `ordini.csv` caricando solo le colonne `cliente` e `totale` con `usecols`, poi salvalo in `ordini.parquet`.', 'Read `ordini.csv` into `df` loading only the `cliente` and `totale` columns with `usecols`, then save it to `ordini.parquet`.'), "import pandas as pd\n", [re('df\s*=\s*pd\.read_csv\s*\(\s*["\']ordini\.csv["\'][^)]*usecols\s*=\s*\[\s*["\'](cliente["\']\s*,\s*["\']totale|totale["\']\s*,\s*["\']cliente)["\']\s*\]', L('Carichi solo due colonne', 'You load only two columns')), re('df\.to_parquet\s*\(\s*["\']ordini\.parquet["\']', L('Salvi in Parquet', 'You save to Parquet'))], '', "df = pd.read_csv(\"ordini.csv\", usecols=[\"cliente\", \"totale\"])\ndf.to_parquet(\"ordini.parquet\")"), [
            dBug(pBug(), ['df = pd.read_csv("voti.csv")', 'df[df["voto"] < 6]["esito"] = "rimandato"', 'print(df.head())'], 1, 'df.loc[df["voto"] < 6, "esito"] = "rimandato"', L('L’assegnazione a catena lavora su una copia: con `loc` modifichi davvero il DataFrame.', 'Chained assignment works on a copy: with `loc` you really change the DataFrame.')),
            dMatch(pMatch(), [['category', L('Testi ripetuti', 'Repeated text')], ['usecols', L('Solo alcune colonne', 'Only some columns')], ['chunksize', L('Lettura a pezzi', 'Reading in chunks')], ['parquet', L('Formato colonnare veloce', 'Fast columnar format')], ['memory_usage', L('Memoria occupata', 'Memory used')]], L('Lavorare con dati grandi.', 'Working with big data.')),
        ]),
    ],
];
