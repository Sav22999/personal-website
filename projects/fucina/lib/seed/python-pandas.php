<?php
/** Data analysis with pandas: DataFrames, loading and exploring, selecting and filtering, cleaning, grouping and joining, charts, time series, new columns, pivots and big data. */

return [
    'id' => 'python-pandas', 'family' => 'python', 'after' => 'python-base', 'categories' => ['data'],
    'title' => L('Analisi dati con pandas', 'Data Analysis with pandas'), 'short' => L('pandas', 'pandas'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Analizzare dati con Python: DataFrame, caricare ed esplorare, selezionare e filtrare, pulire, raggruppare e unire tabelle, grafici veloci, serie temporali, nuove colonne, pivot e dati grandi.', 'Analysing data with Python: DataFrames, loading and exploring, selecting and filtering, cleaning, grouping and joining tables, quick charts, time series, new columns, pivots and big data.'),
    'lessons' => [
        lesson('dataframe', L('DataFrame ed esplorazione', 'DataFrames and exploration'), [
            P('**pandas** è la libreria Python per le tabelle di dati. Un **DataFrame** è una tabella con righe e colonne con nome; ogni colonna è una **Series**. Si carica spesso da CSV, Excel o database.', '**pandas** is the Python library for data tables. A **DataFrame** is a table with named rows and columns; each column is a **Series**. It is often loaded from CSV, Excel or databases.'),
            C('import pandas as pd

vendite = pd.read_csv("vendite.csv", parse_dates=["data"])
print(vendite.shape)        # (righe, colonne)
print(vendite.head())       # prime 5 righe
vendite.info()              # tipi e valori mancanti
print(vendite.describe())   # statistiche delle colonne numeriche', 'python'),
            P('`head`, `info` e `describe` sono i primi tre comandi da lanciare su un dataset nuovo: ti dicono forma, tipi, valori mancanti e ordini di grandezza.', '`head`, `info` and `describe` are the first three commands to run on a new dataset: they tell you shape, types, missing values and orders of magnitude.'),
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
            C('grandi = vendite[vendite["importo"] > 100]
nord = vendite[(vendite["regione"] == "Nord") & (vendite["importo"] > 100)]
alcune = vendite[vendite["citta"].isin(["Udine", "Trieste"])]

vendite.loc[vendite["importo"] > 100, ["cliente", "importo"]]   # righe e colonne per etichetta
vendite.sort_values("importo", ascending=False).head(5)', 'python'),
            P('`loc` seleziona per etichette (righe e colonne insieme), `iloc` per posizione. Con pandas non si usano `and`/`or` di Python sulle colonne: danno errore.', '`loc` selects by labels (rows and columns together), `iloc` by position. In pandas you do not use Python’s `and`/`or` on columns: they raise an error.'),
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
            C('per_regione = vendite.groupby("regione")["importo"].agg(["sum", "mean", "count"])
print(per_regione.sort_values("sum", ascending=False))

completo = vendite.merge(clienti, on="cliente_id", how="left")

mensili = vendite.groupby(vendite["data"].dt.to_period("M"))["importo"].sum()
mensili.plot(kind="bar", title="Vendite mensili")', 'python'),
            P('`how="left"` tiene tutte le righe della tabella di sinistra anche senza corrispondenza, come una `LEFT JOIN`. `.dt` dà accesso alle parti delle date (anno, mese, giorno della settimana).', '`how="left"` keeps all left-table rows even without a match, like a `LEFT JOIN`. `.dt` gives access to date parts (year, month, weekday).'),
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
            C('pivot = vendite.pivot_table(
    index="regione", columns="anno", values="importo",
    aggfunc="sum", fill_value=0,
)

lungo = pivot.reset_index().melt(id_vars="regione", var_name="anno", value_name="importo")

pd.crosstab(vendite["regione"], vendite["canale"])      # conteggi incrociati
tutto = pd.concat([gennaio, febbraio], ignore_index=True)', 'python'),
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
