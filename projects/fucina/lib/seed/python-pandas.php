<?php
/** Data analysis with pandas: DataFrames, loading and exploring, selecting and filtering, cleaning, grouping and joining, charts. */

return [
    'id' => 'python-pandas', 'family' => 'python', 'after' => 'python-base', 'categories' => ['data'],
    'title' => L('Analisi dati con pandas', 'Data Analysis with pandas'), 'short' => L('pandas', 'pandas'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Analizzare dati con Python: DataFrame, caricare ed esplorare, selezionare e filtrare, pulire, raggruppare e unire tabelle, grafici veloci.', 'Analysing data with Python: DataFrames, loading and exploring, selecting and filtering, cleaning, grouping and joining tables, quick charts.'),
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
            P('Le date lette da un CSV sono testo: `pd.to_datetime()` le converte. Con la colonna data come indice, `resample("M")` raggruppa per mese e `rolling(7).mean()` calcola una media mobile su 7 righe.', 'Dates read from a CSV are text: `pd.to_datetime()` converts them. With the date column as index, `resample("M")` groups by month and `rolling(7).mean()` computes a 7-row moving average.'),
            C('df["data"] = pd.to_datetime(df["data"])
df = df.set_index("data")
mensile = df["vendite"].resample("M").sum()
df["media7"] = df["vendite"].rolling(7).mean()
df["mese"] = df.index.month

mensile.to_csv("vendite_mensili.csv")
df.to_excel("report.xlsx", sheet_name="Dati")', 'python'),
            P('Alla fine si **esporta**: `to_csv`, `to_excel`, `to_json`. Per file grandi, `to_parquet` è più compatto e veloce. Aggiungi `index=False` se l’indice non serve nel file.', 'At the end you **export**: `to_csv`, `to_excel`, `to_json`. For large files, `to_parquet` is more compact and faster. Add `index=False` if the index is not needed in the file.'),
        ], [
            Q('ti1', L('Come converti una colonna di testo in date?', 'How do you convert a text column into dates?'), ['pd.to_datetime()', 'df.astype("date")', 'pd.date()', 'df.parse()'], 0, L('Poi puoi usare `.dt` e `resample`.', 'Then you can use `.dt` and `resample`.')),
            Q('ti2', L('Che cosa fa `resample("M").sum()`?', 'What does `resample("M").sum()` do?'), [L('Somma per mese', 'Sums by month'), L('Ordina per mese', 'Sorts by month'), L('Cancella i mesi vuoti', 'Deletes empty months'), L('Conta le righe', 'Counts rows')], 0, L('Serve un indice di date.', 'It needs a date index.')),
            Q('ti3', L('A che cosa serve `index=False` in `to_csv`?', 'What is `index=False` for in `to_csv`?'), [L('Non scrivere l’indice nel file', 'Not writing the index to the file'), L('Ordinare le righe', 'Sorting rows'), L('Comprimere il file', 'Compressing the file'), L('Saltare le intestazioni', 'Skipping headers')], 0, L('Spesso l’indice è solo un numero di riga.', 'Often the index is just a row number.')),
        ], task('python', L('Converti `df["giorno"]` in date con `pd.to_datetime` e salva `df` in `pulito.csv` senza indice.', 'Convert `df["giorno"]` to dates with `pd.to_datetime` and save `df` to `pulito.csv` without the index.'), '', [re('df\[\s*["\']giorno["\']\s*\]\s*=\s*pd\.to_datetime\(\s*df\[\s*["\']giorno["\']\s*\]\s*\)', L('Converti le date', 'You convert the dates')), re('df\.to_csv\(\s*["\']pulito\.csv["\']\s*,\s*index\s*=\s*False\s*\)', L('Esporti senza indice', 'You export without index'))], '', "df[\"giorno\"] = pd.to_datetime(df[\"giorno\"])\ndf.to_csv(\"pulito.csv\", index=False)"), [
            dOrder(pSort(), ['df = pd.read_csv("vendite.csv")', 'df["data"] = pd.to_datetime(df["data"])', 'df = df.set_index("data")', 'mensile = df["vendite"].resample("M").sum()', 'mensile.to_csv("mensile.csv")'], L('Leggi, converti, indicizza, raggruppa, esporta.', 'Read, convert, index, group, export.')),
            dMatch(pMatch(), [['to_datetime', L('Testo in date', 'Text to dates')], ['resample', L('Raggruppa nel tempo', 'Groups over time')], ['rolling', L('Media mobile', 'Moving average')], ['to_excel', L('Esporta in Excel', 'Exports to Excel')], ['to_parquet', L('Formato compatto', 'Compact format')]], L('Tempo ed esportazione.', 'Time and export.')),
        ]),
    ],
];
