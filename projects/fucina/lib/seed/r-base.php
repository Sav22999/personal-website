<?php
/** R basics: vectors, data frames, functions, simple statistics and plots, types and factors, lists, packages. */

/** What a command prints, as a small monospace page. */
if (!function_exists('fucina_data_pre')) {
    function fucina_data_pre(string $text): string
    {
        return '<pre style="margin:8px;font:13px/1.45 ui-monospace,monospace;color:#1E1C52;white-space:pre-wrap">' . htmlspecialchars($text) . '</pre>';
    }
}

/** A vectorised operation: each element of the vector is multiplied, all at once; positions start from 1. */
if (!function_exists('fig_rbase_vector')) {
    function fig_rbase_vector(string $index): string
    {
        $g = '';
        foreach ([7, 8, 6, 9] as $i => $v) {
            $x = 200 + $i * 90;
            $g .= '<text x="' . ($x + 35) . '" y="44" text-anchor="middle" font-size="13" fill="#5A5880">[' . ($i + 1) . ']</text>'
                . '<rect x="' . $x . '" y="54" width="70" height="44" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="' . ($x + 35) . '" y="82" text-anchor="middle" font-weight="700">' . $v . '</text>'
                . '<path d="M' . ($x + 35) . ' 100 V140" stroke="#E8590C" stroke-width="2"/><path d="M' . ($x + 29) . ' 138 L' . ($x + 35) . ' 148 L' . ($x + 41) . ' 138 Z" fill="#E8590C"/>'
                . '<text x="' . ($x + 42) . '" y="126" font-family="ui-monospace, monospace" font-size="13" fill="#B84508">× 10</text>'
                . '<rect x="' . $x . '" y="150" width="70" height="44" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="' . ($x + 35) . '" y="178" text-anchor="middle" font-weight="700">' . ($v * 10) . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="16" fill="#1E1C52">
<text x="186" y="44" text-anchor="end" font-size="13" fill="#5A5880">{$index}</text>
<text x="186" y="82" text-anchor="end" font-family="ui-monospace, monospace" font-weight="700">voti</text>
<text x="186" y="178" text-anchor="end" font-family="ui-monospace, monospace" font-weight="700">voti * 10</text>
{$g}
</svg>
SVG;
    }
}

/** Filtering a data frame: the condition makes a TRUE/FALSE vector, and the TRUE rows are kept. */
if (!function_exists('fig_rbase_filter')) {
    function fig_rbase_filter(): string
    {
        $g = '';
        foreach ([['Ada', 8, true], ['Bea', 6, false], ['Cleo', 9, true]] as $i => [$n, $v, $ok]) {
            $y = 92 + $i * 36;
            $g .= '<rect x="20" y="' . ($y - 22) . '" width="180" height="30" rx="7" fill="' . ($ok ? '#D5F6EA' : '#FFFFFF') . '" stroke="' . ($ok ? '#0B7F5C' : '#C9C7DE') . '" stroke-width="2"/><text x="34" y="' . $y . '">' . $n . '</text><text x="184" y="' . $y . '" text-anchor="end">' . $v . '</text>'
                . '<rect x="234" y="' . ($y - 22) . '" width="90" height="30" rx="7" fill="' . ($ok ? '#D5F6EA' : '#F1F0F8') . '" stroke="' . ($ok ? '#0B7F5C' : '#8A87B0') . '" stroke-width="2"/><text x="279" y="' . $y . '" text-anchor="middle" font-family="ui-monospace, monospace" font-weight="700" fill="' . ($ok ? '#0B7F5C' : '#5A5880') . '">' . ($ok ? 'TRUE' : 'FALSE') . '</text>';
        }
        foreach ([['Ada', 8], ['Cleo', 9]] as $i => [$n, $v]) {
            $y = 110 + $i * 36;
            $g .= '<rect x="440" y="' . ($y - 22) . '" width="180" height="30" rx="7" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="454" y="' . $y . '">' . $n . '</text><text x="604" y="' . $y . '" text-anchor="end">' . $v . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-family="ui-monospace, monospace" font-size="13" font-weight="700"><text x="20" y="30">studenti</text><text x="234" y="30">voto &gt;= 8</text><text x="440" y="48">studenti[studenti\$voto &gt;= 8, ]</text></g>
<g font-weight="700" font-size="13" fill="#5A5880"><text x="34" y="58">nome</text><text x="184" y="58" text-anchor="end">voto</text><text x="454" y="76">nome</text><text x="604" y="76" text-anchor="end">voto</text></g>
{$g}
<path d="M340 128 H422" stroke="#0B7F5C" stroke-width="2"/><path d="M420 122 L430 128 L420 134 Z" fill="#0B7F5C"/>
</svg>
SVG;
    }
}

/** A box plot read through summary(): minimum, quartiles, median, mean and maximum of six ages. */
if (!function_exists('fig_rbase_box')) {
    function fig_rbase_box(): string
    {
        $x = fn(float $v) => (int)round(60 + ($v - 20) * 20);
        $dots = '';
        foreach ([23, 27, 31, 38, 45] as $v) $dots .= '<circle cx="' . $x($v) . '" cy="200" r="6" fill="#2F8CF0"/>';
        $dots .= '<circle cx="' . $x(31) . '" cy="186" r="6" fill="#2F8CF0"/>';
        $lab = '';
        foreach ([['Min.', 23, 52], ['1st Qu.', 28, 74], ['Median', 31, 52], ['3rd Qu.', 36.25, 74], ['Max.', 45, 52]] as [$t, $v, $y]) {
            $lab .= '<text x="' . $x($v) . '" y="' . $y . '" text-anchor="middle" font-size="13"><tspan font-weight="700">' . $t . '</tspan> ' . $v . '</text><line x1="' . $x($v) . '" y1="' . ($y + 6) . '" x2="' . $x($v) . '" y2="98" stroke="#C9C7DE" stroke-width="1.5" stroke-dasharray="3 3"/>';
        }
        $m = $x(32.5);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
{$lab}
<line x1="{$x(23)}" y1="130" x2="{$x(28)}" y2="130" stroke="#1E1C52" stroke-width="2"/><line x1="{$x(36.25)}" y1="130" x2="{$x(45)}" y2="130" stroke="#1E1C52" stroke-width="2"/>
<line x1="{$x(23)}" y1="114" x2="{$x(23)}" y2="146" stroke="#1E1C52" stroke-width="2"/><line x1="{$x(45)}" y1="114" x2="{$x(45)}" y2="146" stroke="#1E1C52" stroke-width="2"/>
<rect x="{$x(28)}" y="102" width="165" height="56" rx="6" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<line x1="{$x(31)}" y1="102" x2="{$x(31)}" y2="158" stroke="#B84508" stroke-width="3"/>
<path d="M{$m} 122 l8 8 l-8 8 l-8 -8 Z" fill="#7048E8"/>
<text x="{$m}" y="176" text-anchor="middle" font-size="13" fill="#4B2BBF"><tspan font-weight="700">Mean</tspan> 32.5</text>
{$dots}
<text x="20" y="224" font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">eta: 23 27 31 31 38 45</text>
</svg>
SVG;
    }
}

/** A list as a train of carriages: [ ] gives a shorter train, [[ ]] or $ gives what is inside a carriage. */
if (!function_exists('fig_rbase_list')) {
    function fig_rbase_list(string $list, string $vector): string
    {
        $car = fn(int $x, int $y, int $w, string $name, string $val, string $s = '#7048E8', string $f = '#ECE6FF') => '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="56" rx="10" fill="' . $f . '" stroke="' . $s . '" stroke-width="2"/><text x="' . ($x + 10) . '" y="' . ($y + 20) . '" font-family="ui-monospace, monospace" font-size="12" font-weight="700" fill="#4B2BBF">' . $name . '</text><text x="' . ($x + $w / 2) . '" y="' . ($y + 44) . '" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">' . $val . '</text>'
            . '<circle cx="' . ($x + 18) . '" cy="' . ($y + 62) . '" r="6" fill="#FFFFFF" stroke="' . $s . '" stroke-width="2"/><circle cx="' . ($x + $w - 18) . '" cy="' . ($y + 62) . '" r="6" fill="#FFFFFF" stroke="' . $s . '" stroke-width="2"/>';
        $g = $car(150, 30, 130, 'nome', '"Ada"') . $car(290, 30, 130, 'voti', '8 9 7', '#E8590C', '#FFE8D6') . $car(430, 30, 130, 'iscritta', 'TRUE');
        $g .= '<path d="M280 58 H290 M420 58 H430" stroke="#7048E8" stroke-width="3"/>';
        $g .= $car(290, 130, 130, 'voti', '8 9 7', '#E8590C', '#FFE8D6');
        $g .= '<rect x="290" y="230" width="130" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="355" y="256" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">8 9 7</text>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-family="ui-monospace, monospace" font-size="13" font-weight="700" text-anchor="end"><text x="136" y="64">studente</text><text x="276" y="164">studente["voti"]</text><text x="276" y="256">studente[["voti"]]</text></g>
{$g}
<g font-size="13" font-weight="700"><text x="572" y="64" fill="#4B2BBF">{$list}</text><text x="434" y="164" fill="#4B2BBF">{$list}</text><text x="434" y="256" fill="#0B7F5C">{$vector}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'r-base', 'family' => 'r', 'after' => null,
    'title' => L('R base', 'R Basics'), 'short' => L('R base', 'R Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio della statistica: vettori, data frame, funzioni, medie e primi grafici, tipi e fattori, liste, pacchetti e progetti.', 'The language of statistics: vectors, data frames, functions, averages and first plots, types and factors, lists, packages and projects.'),
    'lessons' => [
        lesson('vectors', L('Valori e vettori', 'Values and vectors'), [
            P('In R si assegna con la freccia `<-`. Il mattone di base è il **vettore**, creato con `c()`: una serie di valori dello stesso tipo. Le operazioni lavorano su tutto il vettore insieme.', 'In R you assign with the arrow `<-`. The basic brick is the **vector**, made with `c()`: a series of values of the same type. Operations work on the whole vector at once.'),
            C('voti <- c(7, 8, 6, 9)
voti * 10        # 70 80 60 90
length(voti)     # 4
voti[1]          # 7: gli indici partono da 1
nomi <- c("Ada", "Bea")
print(paste("Ciao", nomi))', 'r', fucina_data_pre('[1] 70 80 60 90
[1] 4
[1] 7
[1] "Ciao Ada" "Ciao Bea"')),
            Fig(L(fig_rbase_vector('posizione'), fig_rbase_vector('position')),
                L('Il vettore voti con i valori 7, 8, 6, 9 nelle posizioni da 1 a 4. Da ogni casella scende una freccia «× 10» verso il vettore risultato: 70, 80, 60, 90.', 'The voti vector with the values 7, 8, 6, 9 in positions 1 to 4. From each cell a “× 10” arrow goes down to the result vector: 70, 80, 60, 90.'),
                L('Un’operazione sola, applicata a ogni elemento: niente ciclo da scrivere.', 'A single operation, applied to every element: no loop to write.')),
            P('Attenzione: in R gli indici partono da **1**, non da 0. `paste` unisce testi.', 'Careful: in R indexes start at **1**, not 0. `paste` joins texts.'),
            P('Anche un numero da solo, come `5`, è un vettore lungo 1: per questo in R quasi tutto lavora su vettori. Tra due vettori le operazioni vanno elemento per elemento: `c(1, 2, 3) + c(10, 20, 30)` dà `11 22 33`. Se uno è più corto, R lo **ricicla** ripetendolo: comodo con un numero solo, ma con lunghezze che non combaciano ti avvisa con un warning, e spesso è un errore.', 'Even a single number, such as `5`, is a vector of length 1: that is why almost everything in R works on vectors. Between two vectors operations go element by element: `c(1, 2, 3) + c(10, 20, 30)` gives `11 22 33`. If one is shorter, R **recycles** it by repeating it: handy with a single number, but with lengths that do not fit it warns you, and it is often a mistake.'),
        ], [
            Q('v1', L('Come si assegna un valore in R?', 'How do you assign a value in R?'), ['x <- 5', 'x := 5', 'let x = 5', 'x == 5'], 0, L('Funziona anche `=`, ma si preferisce la freccia.', '`=` works too, but the arrow is preferred.')),
            Q('v2', L('Da quale numero partono gli indici?', 'Which number do indexes start from?'), ['1', '0', '-1', L('Dipende', 'It depends')], 0, L('`voti[1]` è il primo.', '`voti[1]` is the first.')),
            Q('v3', L('Che cosa dà `c(1, 2) * 2`?', 'What does `c(1, 2) * 2` give?'), ['2 4', '1 2 1 2', '4', L('Un errore', 'An error')], 0, L('L’operazione vale per ogni elemento.', 'The operation applies to each item.')),
        ], task('r', L('Crea `altezze <- c(170, 165, 180)` e stampa `mean(altezze)`.', 'Create `altezze <- c(170, 165, 180)` and print `mean(altezze)`.'), '', [re('altezze\s*<-\s*c\s*\(\s*170\s*,\s*165\s*,\s*180\s*\)', L('Il vettore esiste', 'The vector exists')), re('mean\s*\(\s*altezze\s*\)', L('Calcoli la media', 'You compute the mean'))], '', 'altezze <- c(170, 165, 180)
print(mean(altezze))'), [
            dBlanks(pFill(), "prezzi ___ c(10, 20, 30)\nscontati <- prezzi * 0.9\nprimo <- prezzi[___]\nquanti <- ___(prezzi)", ['<-', '1', 'length'], ['=>', '0', 'size'], L('Freccia, primo indice, lunghezza.', 'Arrow, first index, length.')),
            dFlash(pFlash(), [[L('In R `voti[0]` è il primo elemento', 'In R `voti[0]` is the first item'), false], [L('`c()` crea un vettore', '`c()` creates a vector'), true], [L('Le operazioni valgono per tutto il vettore', 'Operations apply to the whole vector'), true], [L('`length(v)` dice quanti elementi ha il vettore', '`length(v)` tells how many elements the vector has'), true]], L('Gli indici partono da 1.', 'Indexes start at 1.')),
        ]),
        lesson('dataframes', L('Data frame', 'Data frames'), [
            P('Un **data frame** è una tabella: ogni colonna è un vettore, ogni riga un’osservazione. Si leggono le colonne con `$` e si filtrano le righe con le parentesi quadre.', 'A **data frame** is a table: each column is a vector, each row an observation. You read columns with `$` and filter rows with square brackets.'),
            C('studenti <- data.frame(
  nome = c("Ada", "Bea", "Cleo"),
  voto = c(8, 6, 9)
)
studenti$voto
studenti[studenti$voto >= 8, ]
nrow(studenti)
dati <- read.csv("vendite.csv")', 'r', fucina_data_pre('> studenti[studenti$voto >= 8, ]
  nome voto
1  Ada    8
3 Cleo    9')),
            P('In `tabella[righe, colonne]` lasciare vuota una parte significa “tutte”. `read.csv` carica un file CSV in un data frame.', 'In `table[rows, columns]` leaving a part empty means “all”. `read.csv` loads a CSV file into a data frame.'),
            P('Come funziona il filtro? `studenti$voto >= 8` confronta ogni voto e restituisce un vettore di `TRUE` e `FALSE`, uno per riga; messo prima della virgola, tiene solo le righe `TRUE`. L’errore più comune è dimenticare la virgola: `studenti[studenti$voto >= 8]` cerca di scegliere **colonne** e dà un errore o un risultato strano.', 'How does the filter work? `studenti$voto >= 8` compares every mark and returns a vector of `TRUE` and `FALSE`, one per row; placed before the comma, it keeps only the `TRUE` rows. The most common mistake is forgetting the comma: `studenti[studenti$voto >= 8]` tries to pick **columns** and gives an error or an odd result.'),
            Fig(fig_rbase_filter(),
                L('A sinistra il data frame studenti con Ada 8, Bea 6, Cleo 9. Accanto, la condizione voto >= 8 produce TRUE, FALSE, TRUE. A destra il risultato del filtro contiene solo Ada e Cleo.', 'On the left the studenti data frame with Ada 8, Bea 6, Cleo 9. Next to it, the condition voto >= 8 gives TRUE, FALSE, TRUE. On the right the filtered result holds only Ada and Cleo.'),
                L('Il vettore di `TRUE` e `FALSE` fa da setaccio per le righe.', 'The vector of `TRUE` and `FALSE` works as a sieve for the rows.')),
        ], [
            Q('d1', L('Come leggi la colonna `voto`?', 'How do you read the `voto` column?'), ['studenti$voto', 'studenti.voto', 'studenti->voto', 'studenti[voto]'], 0, L('Il dollaro sceglie la colonna.', 'The dollar picks the column.')),
            Q('d2', L('Che cosa fa `df[df$eta > 18, ]`?', 'What does `df[df$eta > 18, ]` do?'), [L('Tiene le righe con età sopra 18', 'Keeps rows with age over 18'), L('Cancella la colonna età', 'Deletes the age column'), L('Conta le righe', 'Counts rows'), L('Dà un errore', 'Gives an error')], 0, L('Dopo la virgola, tutte le colonne.', 'After the comma, all columns.')),
            Q('d3', L('Quale funzione carica un CSV?', 'Which function loads a CSV?'), ['read.csv', 'load.csv', 'open.csv', 'csv()'], 0, L('Restituisce un data frame.', 'It returns a data frame.')),
        ], task('r', L('Dal data frame `auto` tieni le righe con `prezzo < 20000`.', 'From the data frame `auto` keep the rows with `prezzo < 20000`.'), '', [re('auto\s*\[\s*auto\$prezzo\s*<\s*20000\s*,\s*\]', L('Il filtro è giusto', 'The filter is right'))], '', 'economiche <- auto[auto$prezzo < 20000, ]'), [
            dMatch(pMatch(), [['$', L('Sceglie una colonna', 'Picks a column')], ['nrow()', L('Numero di righe', 'Number of rows')], ['ncol()', L('Numero di colonne', 'Number of columns')], ['head()', L('Le prime righe', 'The first rows')], ['summary()', L('Riassunto statistico', 'Statistical summary')]], L('Esplorare un data frame.', 'Exploring a data frame.')),
            dOrder(pSort(), ['dati <- read.csv("voti.csv")', 'head(dati)', 'promossi <- dati[dati$voto >= 6, ]', 'nrow(promossi)'], L('Carica, guarda, filtra, conta.', 'Load, look, filter, count.')),
        ]),
        lesson('functions', L('Funzioni e controllo', 'Functions and control'), [
            P('Una funzione si crea con `function` e si assegna a un nome. `if`/`else` sceglie, `for` ripete; spesso però in R si evitano i cicli usando le operazioni sui vettori o `sapply`.', 'A function is made with `function` and assigned to a name. `if`/`else` chooses, `for` repeats; in R you often avoid loops by using vector operations or `sapply`.'),
            C('giudizio <- function(voto) {
  if (voto >= 6) "promosso" else "bocciato"
}
giudizio(7)

quadrati <- sapply(1:5, function(x) x^2)
for (n in c("Ada", "Bea")) print(n)', 'r', fucina_data_pre('[1] "promosso"
[1] "Ada"
[1] "Bea"')),
            P('L’ultima espressione di una funzione è il suo risultato: non serve scrivere `return`. `1:5` crea la sequenza da 1 a 5.', 'A function’s last expression is its result: no need to write `return`. `1:5` creates the sequence from 1 to 5.'),
            P('Gli argomenti possono avere un valore predefinito, `function(voto, soglia = 6)`, e si possono passare per nome in qualsiasi ordine: `giudizio(soglia = 5, voto = 7)`. Un limite da conoscere: `if` guarda un valore solo, quindi `giudizio(c(7, 4))` dà errore. Per un vettore intero c’è la versione vettoriale, `ifelse(voti >= 6, "promosso", "bocciato")`, che risponde per ogni elemento.', 'Arguments can have a default value, `function(voto, soglia = 6)`, and can be passed by name in any order: `giudizio(soglia = 5, voto = 7)`. A limit worth knowing: `if` looks at a single value, so `giudizio(c(7, 4))` is an error. For a whole vector there is the vectorised version, `ifelse(voti >= 6, "promosso", "bocciato")`, which answers for each element.'),
        ], [
            Q('f1', L('Che cosa restituisce una funzione senza `return`?', 'What does a function return without `return`?'), [L('L’ultima espressione', 'The last expression'), 'NULL', L('Un errore', 'An error'), '0'], 0, L('È lo stile tipico di R.', 'It is typical R style.')),
            Q('f2', L('Che cosa crea `1:4`?', 'What does `1:4` create?'), ['1 2 3 4', '1 4', '4', '0 1 2 3'], 0, L('Una sequenza di interi.', 'A sequence of integers.')),
            Q('f3', L('Che cosa fa `sapply(v, f)`?', 'What does `sapply(v, f)` do?'), [L('Applica f a ogni elemento', 'Applies f to each item'), L('Ordina v', 'Sorts v'), L('Salva su file', 'Saves to file'), L('Cancella v', 'Deletes v')], 0, L('Un modo per evitare il ciclo.', 'A way to avoid the loop.')),
        ], task('r', L('Scrivi `doppio <- function(x) x * 2`.', 'Write `doppio <- function(x) x * 2`.'), '', [re('doppio\s*<-\s*function\s*\(\s*x\s*\)\s*\{?\s*x\s*\*\s*2', L('La funzione è giusta', 'The function is right'))], '', 'doppio <- function(x) x * 2'), [
            dBug(pBug(), ['media_voti <- function(v) {', '  somma <- sum(v)', '  somma / length(v)', '}', 'media_voti(c(6, 8)'], 4, 'media_voti(c(6, 8))', L('Manca una parentesi di chiusura.', 'A closing bracket is missing.')),
            dBlanks(pFill(), "pari <- ___(n) {\n  n %% 2 == 0\n}\nrisultati <- ___(1:6, pari)", ['function', 'sapply'], ['def', 'map', 'apply_all'], L('Definisci e applica.', 'Define and apply.')),
        ]),
        lesson('stats', L('Statistiche di base', 'Basic statistics'), [
            P('R nasce per la statistica: `mean`, `median`, `sd` (deviazione standard), `min`, `max`, `quantile` e `summary` riassumono un vettore in un attimo. `table` conta le occorrenze.', 'R was born for statistics: `mean`, `median`, `sd` (standard deviation), `min`, `max`, `quantile` and `summary` sum up a vector in a moment. `table` counts occurrences.'),
            C('eta <- c(23, 31, 27, 45, 31, 38)
mean(eta)
median(eta)
sd(eta)
summary(eta)
table(c("rosso", "blu", "rosso"))
mean(c(5, NA, 7), na.rm = TRUE)', 'r', fucina_data_pre('> summary(eta)
   Min. 1st Qu.  Median    Mean 3rd Qu.    Max.
  23.00   28.00   31.00   32.50   36.25   45.00')),
            P('`NA` è un valore mancante: molte funzioni restituiscono `NA` se ne trovano uno, a meno di `na.rm = TRUE`.', '`NA` is a missing value: many functions return `NA` if they find one, unless you pass `na.rm = TRUE`.'),
            P('Media e mediana raccontano cose diverse: la **media** risente dei valori estremi, la **mediana** (il valore centrale) no. Qui la media è 32,5 e la mediana 31, perché il 45 tira la media verso l’alto; con stipendi o prezzi la differenza può essere enorme. La deviazione standard dice quanto i valori sono sparsi attorno alla media: più è grande, più sono lontani tra loro.', 'Mean and median tell different stories: the **mean** is pulled by extreme values, the **median** (the middle value) is not. Here the mean is 32.5 and the median 31, because the 45 drags the mean up; with salaries or prices the gap can be huge. The standard deviation says how spread out the values are around the mean: the bigger it is, the further apart they are.'),
            Fig(fig_rbase_box(),
                L('Un box plot orizzontale delle età 23, 27, 31, 31, 38, 45. I baffi vanno dal minimo 23 al massimo 45; la scatola va dal primo quartile 28 al terzo 36,25, con la mediana 31 come linea spessa; un rombo segna la media 32,5. Sotto, i sei valori come punti.', 'A horizontal box plot of the ages 23, 27, 31, 31, 38, 45. The whiskers run from the minimum 23 to the maximum 45; the box runs from the first quartile 28 to the third 36.25, with the median 31 as a thick line; a diamond marks the mean 32.5. Below, the six values as dots.'),
                L('`summary()` e `boxplot()` mostrano gli stessi cinque numeri, più la media.', '`summary()` and `boxplot()` show the same five numbers, plus the mean.')),
        ], [
            Q('s1', L('Quale funzione dà il valore centrale?', 'Which function gives the middle value?'), ['median', 'mean', 'mid', 'center'], 0, L('`mean` è la media.', '`mean` is the average.')),
            Q('s2', L('Che cosa fa `na.rm = TRUE`?', 'What does `na.rm = TRUE` do?'), [L('Ignora i valori mancanti', 'Ignores missing values'), L('Cancella il vettore', 'Deletes the vector'), L('Arrotonda', 'Rounds'), L('Ordina', 'Sorts')], 0, L('Altrimenti il risultato è `NA`.', 'Otherwise the result is `NA`.')),
            Q('s3', L('Che cosa misura `sd`?', 'What does `sd` measure?'), [L('Quanto i valori sono dispersi', 'How spread out values are'), L('La somma', 'The sum'), L('Il massimo', 'The maximum'), L('Il numero di valori', 'The number of values')], 0, L('Deviazione standard.', 'Standard deviation.')),
        ], task('r', L('Calcola `median(tempi, na.rm = TRUE)`.', 'Compute `median(tempi, na.rm = TRUE)`.'), 'tempi <- c(12, NA, 9, 15)
', [re('median\s*\(\s*tempi\s*,\s*na\.rm\s*=\s*TRUE\s*\)', L('La mediana ignora i mancanti', 'The median ignores missing values'))], '', 'median(tempi, na.rm = TRUE)'), [
            dMatch(pMatch(), [['mean', L('Media', 'Average')], ['median', L('Valore centrale', 'Middle value')], ['sd', L('Dispersione', 'Spread')], ['table', L('Conta le occorrenze', 'Counts occurrences')], ['NA', L('Valore mancante', 'Missing value')]], L('Statistica di base.', 'Basic statistics.')),
            dFlash(pFlash(), [[L('`mean(c(1, NA))` restituisce 1', '`mean(c(1, NA))` returns 1'), false], [L('`summary` mostra minimo, quartili e massimo', '`summary` shows min, quartiles and max'), true], [L('`table` conta quante volte compare ogni valore', '`table` counts how often each value appears'), true], [L('`median` è il valore centrale dei dati ordinati', '`median` is the middle value of the sorted data'), true]], L('Con un `NA` serve `na.rm = TRUE`.', 'With an `NA` you need `na.rm = TRUE`.')),
        ]),
        lesson('plots', L('Primi grafici', 'First plots'), [
            P('Con R base si disegna subito: `plot` per i punti, `hist` per gli istogrammi, `barplot` per le barre, `boxplot` per la distribuzione. Gli argomenti `main`, `xlab`, `col` danno titolo, etichette e colori.', 'Base R draws right away: `plot` for points, `hist` for histograms, `barplot` for bars, `boxplot` for distribution. The arguments `main`, `xlab`, `col` give title, labels and colours.'),
            C('ore <- c(1, 2, 3, 4, 5)
voti <- c(5, 6, 6.5, 8, 9)
plot(ore, voti, main = "Studio e voti",
     xlab = "Ore di studio", ylab = "Voto", col = "tomato", pch = 19)

hist(rnorm(200), col = "skyblue", main = "Distribuzione")', 'r', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 250" width="360" style="font-family:system-ui,sans-serif;display:block;margin:8px"><text x="190" y="22" text-anchor="middle" font-size="14" font-weight="700">Studio e voti</text><rect x="50" y="34" width="290" height="170" fill="none" stroke="#333"/><g fill="#FF6347"><circle cx="70" cy="184" r="5"/><circle cx="132" cy="150" r="5"/><circle cx="195" cy="133" r="5"/><circle cx="257" cy="82" r="5"/><circle cx="320" cy="48" r="5"/></g><g font-size="11" text-anchor="middle"><text x="70" y="218">1</text><text x="132" y="218">2</text><text x="195" y="218">3</text><text x="257" y="218">4</text><text x="320" y="218">5</text></g><g font-size="11" text-anchor="end"><text x="44" y="188">5</text><text x="44" y="120">7</text><text x="44" y="52">9</text></g><text x="195" y="240" text-anchor="middle" font-size="12">Ore di studio</text><text x="16" y="120" text-anchor="middle" font-size="12" transform="rotate(-90 16 120)">Voto</text></svg>'),
            P('`rnorm(200)` genera 200 numeri casuali con distribuzione normale: utile per provare.', '`rnorm(200)` generates 200 random normally distributed numbers: handy for trying things out.'),
            P('In R base un grafico si costruisce a strati: `plot` disegna la base, poi funzioni come `abline`, `lines`, `points`, `text` e `legend` aggiungono elementi sopra il grafico già aperto. Per esempio `abline(lm(voti ~ ore))` traccia la retta che meglio segue i punti. Per salvare l’immagine su file apri prima un dispositivo, `png("grafico.png")`, disegna, e chiudilo con `dev.off()`.', 'In base R a plot is built in layers: `plot` draws the base, then functions such as `abline`, `lines`, `points`, `text` and `legend` add elements on top of the plot already open. For example `abline(lm(voti ~ ore))` draws the line that best follows the points. To save the image to a file first open a device, `png("plot.png")`, draw, and close it with `dev.off()`.'),
        ], [
            Q('p1', L('Quale funzione fa un istogramma?', 'Which function makes a histogram?'), ['hist', 'plot', 'barplot', 'histogram'], 0, L('`barplot` fa barre di valori già contati.', '`barplot` draws bars of counted values.')),
            Q('p2', L('Quale argomento dà il titolo?', 'Which argument sets the title?'), ['main', 'title', 'label', 'head'], 0, L('`xlab` e `ylab` per gli assi.', '`xlab` and `ylab` for the axes.')),
            Q('p3', L('Che cosa genera `rnorm(100)`?', 'What does `rnorm(100)` generate?'), [L('100 numeri casuali normali', '100 random normal numbers'), L('I numeri da 1 a 100', 'The numbers 1 to 100'), L('Un grafico', 'A plot'), L('Un errore', 'An error')], 0, L('Media 0, deviazione 1.', 'Mean 0, deviation 1.')),
        ], task('r', L('Disegna `hist(pesi, main = "Pesi", col = "orange")`.', 'Draw `hist(pesi, main = "Pesi", col = "orange")`.'), 'pesi <- c(60, 72, 65, 80, 68)
', [re('hist\s*\(\s*pesi\s*,', L('È un istogramma dei pesi', 'It is a histogram of the weights')), re('main\s*=\s*"Pesi"', L('Ha il titolo', 'It has the title')), re('col\s*=\s*"orange"', L('Ha il colore', 'It has the colour'))], '', 'hist(pesi, main = "Pesi", col = "orange")'), [
            dMatch(pMatch(), [['plot', L('Punti o linee', 'Points or lines')], ['hist', L('Istogramma', 'Histogram')], ['barplot', L('Barre', 'Bars')], ['boxplot', L('Scatola e baffi', 'Box and whiskers')]], L('I grafici di base.', 'The basic plots.')),
            dBlanks(pFill(), "plot(x, y, ___ = \"Titolo\", ___ = \"Asse X\", ___ = \"red\")", ['main', 'xlab', 'col'], ['title', 'xaxis', 'colour'], L('Titolo, etichetta, colore.', 'Title, label, colour.')),
        ]),
        lesson('types', L('Tipi e fattori', 'Types and factors'), [
            P('Ogni vettore ha un **tipo**: `numeric` (numeri), `character` (testi tra virgolette), `logical` (`TRUE`/`FALSE`). `class()` lo rivela. Se mescoli tipi in `c()`, R converte tutto nel tipo più «largo»: `c(1, "a")` diventa un vettore di testi.', 'Every vector has a **type**: `numeric` (numbers), `character` (texts in quotes), `logical` (`TRUE`/`FALSE`). `class()` reveals it. If you mix types in `c()`, R converts everything to the “widest” type: `c(1, "a")` becomes a vector of texts.'),
            C('class(3.5)              # "numeric"
class("ciao")           # "character"
class(TRUE)             # "logical"
c(1, "a")               # "1" "a"
as.numeric("42") + 1    # 43
sum(c(TRUE, FALSE, TRUE))  # 2

taglie <- factor(c("M", "S", "M", "L"), levels = c("S", "M", "L"))
levels(taglie)          # "S" "M" "L"
table(taglie)', 'r'),
            P('Le funzioni `as.numeric`, `as.character` e `as.logical` convertono a mano. Un **fattore** (`factor`) è un vettore di categorie con un elenco fisso di **livelli**: utile per taglie, regioni, risposte di un sondaggio, e rispettato da `table` e dai grafici nell’ordine che decidi tu.', 'The functions `as.numeric`, `as.character` and `as.logical` convert by hand. A **factor** is a vector of categories with a fixed list of **levels**: useful for sizes, regions, survey answers, and respected by `table` and by plots in the order you choose.'),
        ], [
            Q('y1', L('Che cosa dà `c(2, "tre")`?', 'What does `c(2, "tre")` give?'), [L('Un errore', 'An error'), L('Un vettore numerico', 'A numeric vector'), L('Un vettore di testi: "2" "tre"', 'A vector of texts: "2" "tre"'), L('Una lista di due elementi', 'A list of two elements')], 2, L('Il tipo più largo vince.', 'The widest type wins.')),
            Q('y2', L('Quanto fa `sum(c(TRUE, TRUE, FALSE))`?', 'What is `sum(c(TRUE, TRUE, FALSE))`?'), ['2', '3', 'TRUE', L('Un errore', 'An error')], 0, L('`TRUE` vale 1 e `FALSE` vale 0.', '`TRUE` counts as 1 and `FALSE` as 0.')),
            Q('y3', L('A che cosa serve un fattore?', 'What is a factor for?'), [L('A moltiplicare i valori di un vettore', 'Multiplying a vector’s values'), L('A salvare numeri con molti decimali', 'Storing numbers with many decimals'), L('A collegare due data frame', 'Linking two data frames'), L('A rappresentare categorie con livelli fissi', 'Representing categories with fixed levels')], 3, L('Per esempio S, M, L.', 'For example S, M, L.')),
        ], task('r', L('Crea il fattore `livello <- factor(c("base", "avanzato", "base"), levels = c("base", "avanzato"))` e mostra `table(livello)`.', 'Create the factor `livello <- factor(c("base", "avanzato", "base"), levels = c("base", "avanzato"))` and show `table(livello)`.'), '', [re('livello\s*(<-|=)\s*factor\s*\(\s*c\s*\(\s*"base"\s*,\s*"avanzato"\s*,\s*"base"\s*\)\s*,\s*levels\s*=\s*c\s*\(\s*"base"\s*,\s*"avanzato"\s*\)\s*\)', L('Il fattore con i livelli', 'The factor with its levels')), re('table\s*\(\s*livello\s*\)', L('Conti le categorie', 'You count the categories'))], '', 'livello <- factor(c("base", "avanzato", "base"), levels = c("base", "avanzato"))
table(livello)'), [
            dMatch(pMatch(), [['numeric', L('Numeri', 'Numbers')], ['character', L('Testi', 'Texts')], ['logical', 'TRUE / FALSE'], ['factor', L('Categorie', 'Categories')], ['class()', L('Dice il tipo', 'Tells the type')]], L('I tipi di R.', 'R’s types.')),
            dFlash(pFlash(), [[L('`as.numeric("5")` restituisce il numero 5', '`as.numeric("5")` returns the number 5'), true], [L('Un vettore può contenere numeri e testi senza conversioni', 'A vector can hold numbers and texts without conversion'), false], [L('`TRUE` conta come 1 in una somma', '`TRUE` counts as 1 in a sum'), true], [L('I livelli di un fattore decidono l’ordine delle categorie', 'A factor’s levels decide the order of the categories'), true]], L('Un tipo per vettore.', 'One type per vector.')),
        ]),
        lesson('lists', L('Liste', 'Lists'), [
            P('Un vettore tiene un solo tipo; una **lista** invece può contenere di tutto: numeri, testi, vettori, data frame, perfino altre liste. Si crea con `list()` dando un nome a ogni elemento, e si esplora con `str()`, che ne mostra la struttura.', 'A vector holds one type only; a **list** instead can hold anything: numbers, texts, vectors, data frames, even other lists. You create it with `list()` naming each element, and explore it with `str()`, which shows its structure.'),
            C('studente <- list(
  nome = "Ada",
  voti = c(8, 9, 7),
  iscritta = TRUE
)
studente$nome          # "Ada"
studente[["voti"]]     # 8 9 7
studente["voti"]       # una lista con un solo elemento
str(studente)

risultati <- lapply(list(a = 1:3, b = 4:6), mean)', 'r'),
            Fig(L(fig_rbase_list('lista', 'vettore'), fig_rbase_list('list', 'vector')),
                L('La lista studente disegnata come un treno di tre vagoni: nome con "Ada", voti con 8 9 7, iscritta con TRUE. Sotto, studente["voti"] è un treno più corto con il solo vagone voti, ancora una lista; studente[["voti"]] è il contenuto del vagone, il vettore 8 9 7.', 'The studente list drawn as a train of three carriages: nome with "Ada", voti with 8 9 7, iscritta with TRUE. Below, studente["voti"] is a shorter train with only the voti carriage, still a list; studente[["voti"]] is what the carriage holds, the vector 8 9 7.'),
                L('`[ ]` stacca dei vagoni, `[[ ]]` e `$` aprono un vagone.', '`[ ]` uncouples carriages, `[[ ]]` and `$` open one carriage.')),
            P('Per prendere un **elemento** si usa `$` o le doppie parentesi `[[ ]]`; le parentesi singole `[ ]` restituiscono invece una **lista più piccola**. Molte funzioni (per esempio `lm` o la lettura di un JSON) restituiscono liste. `lapply` applica una funzione a ogni elemento e restituisce una lista.', 'To take an **element** you use `$` or double brackets `[[ ]]`; single brackets `[ ]` instead return a **smaller list**. Many functions (for example `lm` or reading a JSON file) return lists. `lapply` applies a function to each element and returns a list.'),
        ], [
            Q('l1', L('Che differenza c’è tra un vettore e una lista?', 'What is the difference between a vector and a list?'), [L('La lista può contenere elementi di tipi diversi', 'A list can hold elements of different types'), L('Il vettore può contenere altre liste', 'A vector can hold other lists'), L('La lista contiene solo testi', 'A list holds only texts'), L('Nessuna, sono sinonimi', 'None, they are synonyms')], 0, L('Il vettore ha un solo tipo.', 'A vector has one type only.')),
            Q('l2', L('Che cosa restituisce `studente[["voti"]]`?', 'What does `studente[["voti"]]` return?'), [L('Una lista che contiene i voti', 'A list containing the grades'), L('Il nome dell’elemento', 'The element’s name'), L('Il vettore dei voti', 'The vector of grades'), L('Il numero di elementi', 'The number of elements')], 2, L('Le parentesi singole darebbero una lista.', 'Single brackets would give a list.')),
            Q('l3', L('Quale funzione mostra la struttura di una lista?', 'Which function shows the structure of a list?'), ['print_all()', 'shape()', 'dim()', 'str()'], 3, L('Da «structure».', 'From “structure”.')),
        ], task('r', L('Crea la lista `libro` con `titolo = "Dune"` e `anno = 1965`, poi stampa `libro$titolo`.', 'Create the list `libro` with `titolo = "Dune"` and `anno = 1965`, then print `libro$titolo`.'), '', [re('libro\s*(<-|=)\s*list\s*\(', L('La lista esiste', 'The list exists')), re('titolo\s*=\s*"Dune"', L('C’è il titolo', 'There is the title')), re('anno\s*=\s*1965', L('C’è l’anno', 'There is the year')), re('libro\$titolo|libro\[\[\s*"titolo"\s*\]\]', L('Leggi il titolo', 'You read the title'))], '', 'libro <- list(titolo = "Dune", anno = 1965)
print(libro$titolo)'), [
            dBlanks(pFill(), "cliente <- ___(nome = \"Bea\", ordini = c(3, 5))\ncliente___nome\ncliente[[___]]", ['list', '$', '"ordini"'], ['c', '@', '2:3'], L('Crea la lista, prendi per nome, prendi con le doppie parentesi.', 'Create the list, take by name, take with double brackets.')),
            dMatch(pMatch(), [['list()', L('Crea una lista', 'Creates a list')], ['$', L('Elemento per nome', 'Element by name')], ['[[ ]]', L('Un elemento', 'One element')], ['[ ]', L('Una lista più piccola', 'A smaller list')], ['lapply', L('Applica a ogni elemento', 'Applies to each element')]], L('Muoversi nelle liste.', 'Moving around lists.')),
        ]),
        lesson('packages', L('Pacchetti e progetti', 'Packages and projects'), [
            P('La forza di R sono i **pacchetti**: raccolte di funzioni pubblicate su **CRAN** (oltre ventimila). Si installano una volta con `install.packages("nome")` e si caricano in ogni sessione con `library(nome)`. L’ambiente più usato per lavorare è **RStudio**; Posit, che lo sviluppa, offre anche il nuovo editor Positron.', 'R’s strength is its **packages**: collections of functions published on **CRAN** (over twenty thousand). You install them once with `install.packages("name")` and load them in every session with `library(name)`. The most used environment is **RStudio**; Posit, which develops it, also offers the newer Positron editor.'),
            C('install.packages("dplyr")    # una volta sola
library(dplyr)               # in ogni script

?mean                        # aiuto su una funzione
getwd()                      # cartella di lavoro attuale
dati <- read.csv("dati/vendite.csv")', 'r'),
            P('`?funzione` apre la documentazione, sempre con esempi in fondo. Lavora dentro un **progetto** (un file `.Rproj` di RStudio): la cartella di lavoro è quella del progetto e i percorsi relativi come `"dati/vendite.csv"` funzionano su ogni computer. Evita `setwd("C:/Utenti/…")` negli script da condividere.', '`?function` opens the documentation, always with examples at the bottom. Work inside a **project** (an RStudio `.Rproj` file): the working directory is the project’s and relative paths such as `"dati/vendite.csv"` work on every computer. Avoid `setwd("C:/Users/…")` in scripts you share.'),
        ], [
            Q('k1', L('Quante volte si installa un pacchetto?', 'How many times do you install a package?'), [L('A ogni riga di codice', 'On every line of code'), L('Una volta; poi lo si carica con `library`', 'Once; then you load it with `library`'), L('A ogni avvio del computer', 'On every computer start-up'), L('Mai, sono tutti già installati', 'Never, they are all pre-installed')], 1, L('`library` va in ogni script.', '`library` goes in every script.')),
            Q('k2', L('Dove si pubblicano i pacchetti ufficiali di R?', 'Where are official R packages published?'), ['CRAN', 'npm', 'PyPI', 'Maven'], 0, L('Comprehensive R Archive Network.', 'Comprehensive R Archive Network.')),
            Q('k3', L('Perché evitare `setwd()` con un percorso assoluto negli script condivisi?', 'Why avoid `setwd()` with an absolute path in shared scripts?'), [L('Perché rallenta R', 'Because it slows R down'), L('Perché cancella i file', 'Because it deletes files'), L('Perché è vietato da CRAN', 'Because CRAN forbids it'), L('Perché quel percorso non esiste sugli altri computer', 'Because that path does not exist on other computers')], 3, L('Meglio un progetto e percorsi relativi.', 'Better a project and relative paths.')),
        ], task('r', L('Installa il pacchetto `ggplot2` e poi caricalo.', 'Install the `ggplot2` package and then load it.'), '', [re('install\.packages\s*\(\s*["\']ggplot2["\']\s*\)', L('Lo installi', 'You install it')), re('library\s*\(\s*["\']?ggplot2["\']?\s*\)', L('Lo carichi', 'You load it'))], '', 'install.packages("ggplot2")
library(ggplot2)'), [
            dOrder(pSort(), ['install.packages("readr")', 'library(readr)', 'vendite <- read_csv("dati/vendite.csv")', 'head(vendite)'], L('Installa, carica, usa, guarda.', 'Install, load, use, look.')),
            dBug(pBug(), ['install.packages(dplyr)', 'library(dplyr)'], 0, 'install.packages("dplyr")', L('In `install.packages` il nome va tra virgolette.', 'In `install.packages` the name goes in quotes.')),
        ]),
    ],
];
