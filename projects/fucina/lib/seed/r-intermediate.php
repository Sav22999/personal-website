<?php
/** R intermediate: the tidyverse with dplyr, pipes, ggplot2, reshaping data, simple models, stringr, lubridate and purrr. */

return [
    'id' => 'r-intermediate', 'family' => 'r', 'after' => 'r-base',
    'title' => L('R intermedio', 'Intermediate R'), 'short' => L('R intermedio', 'R Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il tidyverse: pipe, dplyr, raggruppamenti, ggplot2, dati lunghi e larghi, un primo modello lineare, testi con stringr, date con lubridate e iterazioni con purrr.', 'The tidyverse: pipes, dplyr, grouping, ggplot2, long and wide data, a first linear model, text with stringr, dates with lubridate and iteration with purrr.'),
    'lessons' => [
        lesson('pipe', L('Pipe e dplyr', 'Pipes and dplyr'), [
            P('Il **pipe** `|>` passa il risultato a sinistra come primo argomento della funzione a destra: le operazioni si leggono dall’alto in basso. **dplyr** offre verbi chiari: `filter`, `select`, `mutate`, `arrange`.', 'The **pipe** `|>` passes the left result as the first argument of the function on the right: operations read top to bottom. **dplyr** offers clear verbs: `filter`, `select`, `mutate`, `arrange`.'),
            C('library(dplyr)

vendite |>
  filter(regione == "Nord") |>
  mutate(totale = prezzo * pezzi) |>
  select(prodotto, totale) |>
  arrange(desc(totale))', 'r'),
            P('Dentro i verbi di dplyr le colonne si scrivono senza `$` né virgolette. Il pipe nativo `|>` esiste da R 4.1; nel codice più vecchio trovi `%>%` del pacchetto magrittr, che funziona quasi allo stesso modo.', 'Inside dplyr verbs, columns are written without `$` or quotes. The native pipe `|>` exists since R 4.1; in older code you find `%>%` from the magrittr package, which works almost the same way.'),
        ], [
            Q('p1', L('Che cosa fa `mutate`?', 'What does `mutate` do?'), [L('Crea o cambia una colonna', 'Creates or changes a column'), L('Filtra le righe', 'Filters rows'), L('Ordina', 'Sorts'), L('Cancella il data frame', 'Deletes the data frame')], 0, L('`filter` tiene le righe.', '`filter` keeps rows.')),
            Q('p2', L('Che cosa fa `|>`?', 'What does `|>` do?'), [L('Passa il risultato alla funzione dopo', 'Passes the result to the next function'), L('Confronta', 'Compares'), L('Commenta', 'Comments'), L('Assegna', 'Assigns')], 0, L('Si legge “e poi”.', 'Read it as “and then”.')),
            Q('p3', L('Come ordini in modo decrescente?', 'How do you sort in descending order?'), ['arrange(desc(x))', 'arrange(-desc)', 'sort(x, TRUE)', 'order_by(x)'], 0, L('`desc()` dentro `arrange`.', '`desc()` inside `arrange`.')),
        ], task('r', L('Da `clienti` tieni con `filter(eta >= 30)` e scegli `select(nome, citta)`, usando `|>`.', 'From `clienti` keep with `filter(eta >= 30)` and pick `select(nome, citta)`, using `|>`.'), '', [re('clienti\s*\|>\s*filter\s*\(\s*eta\s*>=\s*30\s*\)', L('Filtri con il pipe', 'You filter with the pipe')), re('\|>\s*select\s*\(\s*nome\s*,\s*citta\s*\)', L('Scegli le colonne', 'You pick the columns'))], '', 'clienti |>
  filter(eta >= 30) |>
  select(nome, citta)'), [
            dOrder(pSort(), ['studenti |>', '  filter(anno == 2026) |>', '  mutate(media = (voto1 + voto2) / 2) |>', '  arrange(desc(media))'], L('Dati, filtro, nuova colonna, ordine.', 'Data, filter, new column, order.')),
            dMatch(pMatch(), [['filter', L('Tiene righe', 'Keeps rows')], ['select', L('Sceglie colonne', 'Picks columns')], ['mutate', L('Nuove colonne', 'New columns')], ['arrange', L('Ordina', 'Sorts')], ['rename', L('Rinomina colonne', 'Renames columns')]], L('I verbi di dplyr.', 'The dplyr verbs.')),
        ]),
        lesson('summarise', L('Raggruppare e riassumere', 'Grouping and summarising'), [
            P('`group_by` divide i dati in gruppi; `summarise` calcola un valore per ciascuno. `n()` conta le righe del gruppo.', '`group_by` splits data into groups; `summarise` computes one value for each. `n()` counts the group’s rows.'),
            C('vendite |>
  group_by(regione) |>
  summarise(
    ordini = n(),
    incasso = sum(totale),
    medio = mean(totale)
  ) |>
  arrange(desc(incasso))', 'r'),
            P('È l’equivalente del `GROUP BY` di SQL, scritto con verbi.', 'It is the equivalent of SQL’s `GROUP BY`, written with verbs.'),
        ], [
            Q('s1', L('Che cosa conta `n()`?', 'What does `n()` count?'), [L('Le righe del gruppo', 'The group’s rows'), L('Le colonne', 'The columns'), L('I gruppi', 'The groups'), L('I valori mancanti', 'Missing values')], 0, L('Dentro `summarise`.', 'Inside `summarise`.')),
            Q('s2', L('Dopo `group_by` + `summarise` quante righe ci sono?', 'After `group_by` + `summarise`, how many rows are there?'), [L('Una per gruppo', 'One per group'), L('Le stesse di prima', 'The same as before'), L('Una sola', 'Just one'), L('Zero', 'Zero')], 0, L('Ogni gruppo diventa una riga.', 'Each group becomes a row.')),
            Q('s3', L('A che cosa corrisponde in SQL?', 'What does it match in SQL?'), ['GROUP BY', 'JOIN', 'WHERE', 'LIMIT'], 0, L('Stessa idea, altra sintassi.', 'Same idea, different syntax.')),
        ], task('r', L('Raggruppa `voti` per `corso` e calcola `media = mean(voto)`.', 'Group `voti` by `corso` and compute `media = mean(voto)`.'), 'voti |>', [re('group_by\s*\(\s*corso\s*\)', L('Raggruppi per corso', 'You group by course')), re('summarise\s*\(\s*media\s*=\s*mean\s*\(\s*voto\s*\)\s*\)', L('Calcoli la media', 'You compute the mean'))], '', 'voti |>
  group_by(corso) |>
  summarise(media = mean(voto))'), [
            dBlanks(pFill(), "ordini |>\n  ___(cliente) |>\n  ___(numero = ___(), speso = sum(totale))", ['group_by', 'summarise', 'n'], ['split', 'count', 'length'], L('Gruppi, riassunto, conteggio.', 'Groups, summary, count.')),
            dFlash(pFlash(), [[L('`summarise` restituisce una riga per gruppo', '`summarise` returns one row per group'), true], [L('`group_by` da solo cambia i numeri', '`group_by` alone changes the numbers'), false], [L('Si possono calcolare più valori in un `summarise`', 'You can compute several values in one `summarise`'), true], [L('`n()` conta le righe di ogni gruppo', '`n()` counts the rows of each group'), true]], L('`group_by` prepara, `summarise` calcola.', '`group_by` prepares, `summarise` computes.')),
        ]),
        lesson('ggplot', L('Grafici con ggplot2', 'Plots with ggplot2'), [
            P('**ggplot2** costruisce i grafici a strati: i dati, l’**estetica** (`aes`: quale colonna va su x, y, colore) e una **geometria** (`geom_point`, `geom_col`, `geom_line`), unite dal `+`.', '**ggplot2** builds plots in layers: the data, the **aesthetics** (`aes`: which column goes to x, y, colour) and a **geometry** (`geom_point`, `geom_col`, `geom_line`), joined with `+`.'),
            C('library(ggplot2)

ggplot(studenti, aes(x = ore, y = voto, colour = corso)) +
  geom_point(size = 3) +
  geom_smooth(method = "lm") +
  labs(title = "Studio e voti", x = "Ore", y = "Voto") +
  theme_minimal()', 'r'),
            P('Ogni `+` aggiunge un pezzo: punti, una linea di tendenza, titoli, un tema.', 'Each `+` adds a piece: points, a trend line, titles, a theme.'),
        ], [
            Q('g1', L('Che cosa va dentro `aes()`?', 'What goes inside `aes()`?'), [L('Quali colonne vanno su x, y, colore', 'Which columns go to x, y, colour'), L('Il titolo', 'The title'), L('Il tema', 'The theme'), L('Il file', 'The file')], 0, L('La mappatura tra dati e grafico.', 'The mapping between data and plot.')),
            Q('g2', L('Con che cosa si uniscono gli strati?', 'How are layers joined?'), ['+', '|>', '%>%', ','], 0, L('In ggplot2 si usa il più.', 'ggplot2 uses the plus.')),
            Q('g3', L('Quale geometria disegna punti?', 'Which geometry draws points?'), ['geom_point', 'geom_col', 'geom_line', 'geom_text'], 0, L('Un grafico a dispersione.', 'A scatter plot.')),
        ], task('r', L('Disegna `ggplot(auto, aes(x = km, y = prezzo)) + geom_point()`.', 'Draw `ggplot(auto, aes(x = km, y = prezzo)) + geom_point()`.'), 'library(ggplot2)
', [re('ggplot\s*\(\s*auto\s*,\s*aes\s*\(\s*x\s*=\s*km\s*,\s*y\s*=\s*prezzo\s*\)\s*\)', L('Dati ed estetica', 'Data and aesthetics')), re('\+\s*geom_point\s*\(\s*\)', L('Aggiungi i punti', 'You add the points'))], '', 'ggplot(auto, aes(x = km, y = prezzo)) + geom_point()'), [
            dOrder(pSort(), ['ggplot(vendite, aes(x = mese, y = incasso)) +', '  geom_line() +', '  labs(title = "Incassi") +', '  theme_minimal()'], L('Dati, geometria, titoli, tema.', 'Data, geometry, titles, theme.')),
            dMatch(pMatch(), [['geom_point', L('Punti', 'Points')], ['geom_line', L('Linea', 'Line')], ['geom_col', L('Barre con altezze date', 'Bars with given heights')], ['geom_histogram', L('Istogramma', 'Histogram')], ['labs', L('Titoli ed etichette', 'Titles and labels')]], L('I pezzi di un ggplot.', 'The pieces of a ggplot.')),
        ]),
        lesson('tidy', L('Dati ordinati', 'Tidy data'), [
            P('Nei **dati ordinati** ogni colonna è una variabile e ogni riga un’osservazione. `pivot_longer` trasforma tante colonne (un anno per colonna) in righe; `pivot_wider` fa il contrario. `left_join` unisce due tabelle.', 'In **tidy data** each column is a variable and each row an observation. `pivot_longer` turns many columns (one year per column) into rows; `pivot_wider` does the opposite. `left_join` joins two tables.'),
            C('library(tidyr)

larghi <- data.frame(paese = c("IT", "FR"), `2024` = c(3, 4), `2025` = c(5, 6), check.names = FALSE)

lunghi <- larghi |>
  pivot_longer(cols = -paese, names_to = "anno", values_to = "valore")

completi <- lunghi |> left_join(nomi_paesi, by = "paese")', 'r'),
            P('I dati lunghi sono quelli che ggplot2 e dplyr preferiscono: una riga per ogni misura.', 'Long data is what ggplot2 and dplyr prefer: one row per measurement.'),
        ], [
            Q('t1', L('Che cosa fa `pivot_longer`?', 'What does `pivot_longer` do?'), [L('Trasforma colonne in righe', 'Turns columns into rows'), L('Allunga i testi', 'Lengthens text'), L('Ordina', 'Sorts'), L('Cancella righe', 'Deletes rows')], 0, L('Da largo a lungo.', 'From wide to long.')),
            Q('t2', L('Nei dati ordinati che cos’è una riga?', 'In tidy data what is a row?'), [L('Un’osservazione', 'An observation'), L('Una variabile', 'A variable'), L('Un file', 'A file'), L('Un grafico', 'A plot')], 0, L('Le colonne sono le variabili.', 'Columns are the variables.')),
            Q('t3', L('Quale funzione unisce due tabelle tenendo tutte le righe della prima?', 'Which function joins two tables keeping every row of the first?'), ['left_join', 'inner_join', 'bind_rows', 'merge_all'], 0, L('Come la LEFT JOIN di SQL.', 'Like SQL’s LEFT JOIN.')),
        ], task('r', L('Trasforma `meteo` con `pivot_longer(cols = -citta, names_to = "mese", values_to = "gradi")`.', 'Reshape `meteo` with `pivot_longer(cols = -citta, names_to = "mese", values_to = "gradi")`.'), 'library(tidyr)
', [re('pivot_longer\s*\(', L('Usi pivot_longer', 'You use pivot_longer')), re('names_to\s*=\s*"mese"', L('I nomi vanno in “mese”', 'Names go to “mese”')), re('values_to\s*=\s*"gradi"', L('I valori in “gradi”', 'Values go to “gradi”'))], '', 'meteo |>
  pivot_longer(cols = -citta, names_to = "mese", values_to = "gradi")'), [
            dMatch(pMatch(), [['pivot_longer', L('Da largo a lungo', 'Wide to long')], ['pivot_wider', L('Da lungo a largo', 'Long to wide')], ['left_join', L('Unisce tenendo la prima tabella', 'Joins keeping the first table')], ['bind_rows', L('Mette le righe una sotto l’altra', 'Stacks rows')]], L('Rimodellare i dati.', 'Reshaping data.')),
            dFlash(pFlash(), [[L('Nei dati ordinati ogni colonna è una variabile', 'In tidy data each column is a variable'), true], [L('ggplot2 preferisce i dati larghi', 'ggplot2 prefers wide data'), false], [L('`pivot_wider` è il contrario di `pivot_longer`', '`pivot_wider` is the opposite of `pivot_longer`'), true], [L('`pivot_longer` trasforma colonne in righe', '`pivot_longer` turns columns into rows'), true]], L('Una riga per ogni misura.', 'One row per measurement.')),
        ]),
        lesson('models', L('Un modello lineare', 'A linear model'), [
            P('`lm(y ~ x, data = …)` cerca la retta che meglio spiega `y` a partire da `x`. `summary` mostra coefficienti, **p-value** e **R²**; `predict` stima nuovi valori.', '`lm(y ~ x, data = …)` finds the line that best explains `y` from `x`. `summary` shows coefficients, **p-values** and **R²**; `predict` estimates new values.'),
            C('modello <- lm(voto ~ ore, data = studenti)
summary(modello)
coef(modello)

nuovi <- data.frame(ore = c(2, 6))
predict(modello, nuovi)', 'r'),
            P('La **tilde** `~` si legge “in funzione di”. R² vicino a 1 indica che il modello spiega bene i dati; ma correlazione non è causalità.', 'The **tilde** `~` reads “as a function of”. R² close to 1 means the model fits the data well; but correlation is not causation.'),
        ], [
            Q('m1', L('Come si legge `voto ~ ore`?', 'How do you read `voto ~ ore`?'), [L('Voto in funzione delle ore', 'Grade as a function of hours'), L('Voto uguale ore', 'Grade equals hours'), L('Voto meno ore', 'Grade minus hours'), L('Voto o ore', 'Grade or hours')], 0, L('La tilde separa risposta e predittori.', 'The tilde separates response and predictors.')),
            Q('m2', L('Quale funzione stima valori nuovi?', 'Which function estimates new values?'), ['predict', 'forecast', 'estimate', 'guess'], 0, L('Riceve il modello e i nuovi dati.', 'It takes the model and new data.')),
            Q('m3', L('Un R² alto prova una causa?', 'Does a high R² prove a cause?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo sopra 0,9', 'Only above 0.9'), L('Solo con molti dati', 'Only with lots of data')], 0, L('Correlazione non è causalità.', 'Correlation is not causation.')),
        ], task('r', L('Crea `modello <- lm(prezzo ~ metri, data = case)` e mostra `summary(modello)`.', 'Create `modello <- lm(prezzo ~ metri, data = case)` and show `summary(modello)`.'), '', [re('modello\s*<-\s*lm\s*\(\s*prezzo\s*~\s*metri\s*,\s*data\s*=\s*case\s*\)', L('Il modello è giusto', 'The model is right')), re('summary\s*\(\s*modello\s*\)', L('Ne mostri il riassunto', 'You show its summary'))], '', 'modello <- lm(prezzo ~ metri, data = case)
summary(modello)'), [
            dBlanks(pFill(), "m <- ___(consumo ___ peso, data = auto)\n___(m, data.frame(peso = 1200))", ['lm', '~', 'predict'], ['model', '=', 'fit'], L('Modello, formula, previsione.', 'Model, formula, prediction.')),
            dFlash(pFlash(), [[L('`summary(lm(...))` mostra i coefficienti', '`summary(lm(...))` shows the coefficients'), true], [L('R² vicino a 0 indica un buon modello', 'R² near 0 means a good model'), false], [L('Correlazione non è causalità', 'Correlation is not causation'), true], [L('Nella formula `y ~ x`, `y` è la variabile da spiegare', 'In the formula `y ~ x`, `y` is the variable to explain'), true]], L('R² vicino a 1 è un buon adattamento.', 'R² near 1 is a good fit.')),
        ]),
        lesson('stringr', L('Testi con stringr', 'Text with stringr'), [
            P('**stringr** (parte del tidyverse) raccoglie le funzioni sui testi con nomi coerenti che iniziano tutti con `str_`: `str_detect` dice se un modello c’è, `str_replace_all` sostituisce, `str_to_upper` mette in maiuscolo, `str_sub` taglia, `str_length` misura. Il testo è sempre il primo argomento, quindi funzionano bene con il pipe.', '**stringr** (part of the tidyverse) gathers text functions with consistent names all starting with `str_`: `str_detect` says whether a pattern is there, `str_replace_all` replaces, `str_to_upper` capitalises, `str_sub` cuts, `str_length` measures. The text is always the first argument, so they work well with the pipe.'),
            C('library(stringr)
library(dplyr)

email <- c("ada@scuola.it", "BO@Ditta.com", "cy@scuola.it")
str_detect(email, "@scuola\\\\.it$")        # TRUE FALSE TRUE
str_to_lower(email)
str_replace_all("12-03-2026", "-", "/")   # "12/03/2026"
str_sub("Trieste", 1, 3)                  # "Tri"

clienti |> filter(str_detect(citta, "^San "))', 'r'),
            P('I modelli sono **espressioni regolari**: `^` inizio, `$` fine, `\d` una cifra, `.` qualsiasi carattere. Nelle stringhe di R la barra rovesciata va raddoppiata (`"\\\\."` per un punto vero), oppure usi le stringhe grezze `r"(\.)"`. Per un testo letterale senza regex c’è `fixed()`.', 'Patterns are **regular expressions**: `^` start, `$` end, `\d` a digit, `.` any character. In R strings the backslash must be doubled (`"\\\\."` for a real dot), or you use raw strings `r"(\.)"`. For literal text without regex there is `fixed()`.'),
        ], [
            Q('s1', L('Che cosa restituisce `str_detect(x, "^A")`?', 'What does `str_detect(x, "^A")` return?'), [L('Il numero di A nel testo', 'The number of A’s in the text'), L('TRUE o FALSE per ogni testo, se inizia con A', 'TRUE or FALSE for each text, whether it starts with A'), L('Il testo senza le A', 'The text without the A’s'), L('La posizione della prima A', 'The position of the first A')], 1, L('`^` indica l’inizio del testo.', '`^` marks the start of the text.')),
            Q('s2', L('Perché in R si scrive `"\\\\."` per cercare un punto?', 'Why do you write `"\\\\."` in R to search for a dot?'), [L('Perché il punto è vietato nei testi', 'Because the dot is forbidden in texts'), L('Perché R conta i punti due volte', 'Because R counts dots twice'), L('Perché nella stringa la barra va raddoppiata, e `\.` è un punto vero nella regex', 'Because in the string the backslash is doubled, and `\.` is a real dot in the regex'), L('Per cercare due punti di fila', 'To search for two dots in a row')], 2, L('Da solo `.` vale qualsiasi carattere.', 'On its own `.` means any character.')),
            Q('s3', L('Quale funzione sostituisce tutte le occorrenze?', 'Which function replaces every occurrence?'), ['str_replace_all', 'str_replace', 'str_sub', 'str_detect'], 0, L('`str_replace` cambia solo la prima.', '`str_replace` changes only the first.')),
        ], task('r', L('Da `prodotti` tieni con `filter` le righe in cui `codice` inizia con `"IT-"`, usando `str_detect` e il pipe.', 'From `prodotti` keep with `filter` the rows where `codice` starts with `"IT-"`, using `str_detect` and the pipe.'), 'library(stringr)
library(dplyr)
', [re('prodotti\s*(\|>|%>%)\s*filter\s*\(\s*str_detect\s*\(\s*codice\s*,\s*"\^IT-"\s*\)\s*\)', L('Filtri i codici italiani', 'You filter the Italian codes'))], '', 'prodotti |> filter(str_detect(codice, "^IT-"))'), [
            dMatch(pMatch(), [['str_detect', L('C’è il modello?', 'Is the pattern there?')], ['str_replace_all', L('Sostituisce tutto', 'Replaces everything')], ['str_sub', L('Taglia un pezzo', 'Cuts a piece')], ['str_length', L('Quanti caratteri', 'How many characters')], ['^', L('Inizio del testo', 'Start of the text')]], L('Le funzioni di stringr.', 'The stringr functions.')),
            dBlanks(pFill(), "nomi <- c(\" ada \", \"BEA\")\nnomi |>\n  ___() |>\n  ___()\n# \"ada\" \"bea\"", ['str_trim', 'str_to_lower'], ['str_cut', 'tolower_all', 'str_lower'], L('Togli gli spazi, poi metti in minuscolo.', 'Remove the spaces, then lowercase.')),
        ]),
        lesson('dates', L('Date con lubridate', 'Dates with lubridate'), [
            P('**lubridate** (nel tidyverse dalla versione 2.0) rende facili le date. Le funzioni hanno il nome dell’ordine in cui leggono la data: `ymd("2026-03-15")`, `dmy("15/03/2026")`, `dmy_hm("15/03/2026 18:30")`. Poi `year()`, `month()`, `wday()` estraggono le parti.', '**lubridate** (in the tidyverse since version 2.0) makes dates easy. The functions are named after the order in which they read the date: `ymd("2026-03-15")`, `dmy("15/03/2026")`, `dmy_hm("15/03/2026 18:30")`. Then `year()`, `month()`, `wday()` extract the parts.'),
            C('library(lubridate)

d <- dmy("15/03/2026")
year(d)                       # 2026
month(d, label = TRUE)        # mar
d + days(30)                  # "2026-04-14"
floor_date(d, "month")        # "2026-03-01"
as.numeric(ymd("2026-12-25") - d)   # giorni che mancano

vendite |>
  mutate(mese = floor_date(data, "month")) |>
  group_by(mese) |>
  summarise(incasso = sum(totale))', 'r'),
            P('`days()`, `weeks()`, `months()` aggiungono durate in modo naturale; `floor_date` arrotonda all’inizio del mese o della settimana, perfetto per raggruppare. Togliere due date dà la differenza in giorni. Con `ymd` qualsiasi separatore va bene: `"2026/03/15"` e `"20260315"` funzionano.', '`days()`, `weeks()`, `months()` add durations naturally; `floor_date` rounds down to the start of the month or week, perfect for grouping. Subtracting two dates gives the difference in days. With `ymd` any separator is fine: `"2026/03/15"` and `"20260315"` both work.'),
        ], [
            Q('d1', L('Quale funzione legge `"25/12/2026"`?', 'Which function reads `"25/12/2026"`?'), ['ymd', 'mdy', 'dmy', 'ydm'], 2, L('Giorno, mese, anno.', 'Day, month, year.')),
            Q('d2', L('Che cosa fa `floor_date(d, "month")`?', 'What does `floor_date(d, "month")` do?'), [L('Porta la data al primo giorno del suo mese', 'Moves the date to the first day of its month'), L('Toglie un mese alla data', 'Takes a month off the date'), L('Restituisce il nome del mese', 'Returns the month’s name'), L('Conta i giorni del mese', 'Counts the days in the month')], 0, L('Utile per raggruppare per mese.', 'Handy for grouping by month.')),
            Q('d3', L('Come aggiungi 2 settimane a una data `d`?', 'How do you add 2 weeks to a date `d`?'), ['d + 2', 'd + "2 weeks"', 'add(d, 14w)', 'd + weeks(2)'], 3, L('`d + 2` aggiungerebbe 2 giorni.', '`d + 2` would add 2 days.')),
        ], task('r', L('In `ordini` crea con `mutate` la colonna `mese = floor_date(data, "month")`, poi conta gli ordini per mese con `group_by` e `summarise(n = n())`.', 'In `ordini` create with `mutate` the column `mese = floor_date(data, "month")`, then count orders per month with `group_by` and `summarise(n = n())`.'), 'library(lubridate)
library(dplyr)
', [re('mutate\s*\(\s*mese\s*=\s*floor_date\s*\(\s*data\s*,\s*"month"\s*\)\s*\)', L('Crei il mese', 'You create the month')), re('group_by\s*\(\s*mese\s*\)', L('Raggruppi per mese', 'You group by month')), re('summarise\s*\(\s*n\s*=\s*n\s*\(\s*\)\s*\)', L('Conti gli ordini', 'You count the orders'))], '', 'ordini |>
  mutate(mese = floor_date(data, "month")) |>
  group_by(mese) |>
  summarise(n = n())'), [
            dMatch(pMatch(), [['ymd', L('Anno-mese-giorno', 'Year-month-day')], ['dmy', L('Giorno/mese/anno', 'Day/month/year')], ['wday', L('Giorno della settimana', 'Day of the week')], ['days(7)', L('Una durata di 7 giorni', 'A 7-day duration')], ['floor_date', L('Inizio del periodo', 'Start of the period')]], L('Gli strumenti di lubridate.', 'lubridate’s tools.')),
            dFlash(pFlash(), [[L('`dmy("01/02/2026")` è il primo febbraio', '`dmy("01/02/2026")` is the first of February'), true], [L('`ymd` accetta solo il trattino come separatore', '`ymd` only accepts the dash as separator'), false], [L('Togliere due date dà una differenza in giorni', 'Subtracting two dates gives a difference in days'), true], [L('`month(d, label = TRUE)` dà il nome del mese', '`month(d, label = TRUE)` gives the month’s name'), true]], L('Date senza mal di testa.', 'Dates without headaches.')),
        ]),
        lesson('purrr', L('Iterare con purrr', 'Iterating with purrr'), [
            P('**purrr** applica una funzione a ogni elemento di un vettore o di una lista, senza cicli `for`. `map()` restituisce sempre una lista; le varianti `map_dbl()`, `map_chr()`, `map_lgl()` restituiscono un vettore del tipo indicato, e danno errore se un risultato non è di quel tipo.', '**purrr** applies a function to each element of a vector or a list, without `for` loops. `map()` always returns a list; the variants `map_dbl()`, `map_chr()`, `map_lgl()` return a vector of the given type, and error out if a result is not of that type.'),
            C('library(purrr)

voti <- list(ada = c(8, 9, 7), bea = c(6, 7), cy = c(9, 10))
map_dbl(voti, mean)                     # medie per studente
map_chr(voti, \(v) if (mean(v) >= 7) "ok" else "recupero")

file <- c("gennaio.csv", "febbraio.csv")
tutti <- file |>
  map(read.csv) |>
  list_rbind()', 'r'),
            P('`\(x) …` è la scorciatoia di R 4.1 per scrivere una funzione al volo (come `function(x) …`). Un uso classico: leggere molti file con `map(read.csv)` e unirli in un solo data frame con `list_rbind()`. `map2()` scorre due vettori insieme.', '`\(x) …` is R 4.1’s shortcut for writing a function on the fly (like `function(x) …`). A classic use: reading many files with `map(read.csv)` and stacking them into one data frame with `list_rbind()`. `map2()` walks two vectors together.'),
        ], [
            Q('p1', L('Che cosa restituisce sempre `map()`?', 'What does `map()` always return?'), [L('Un vettore numerico', 'A numeric vector'), L('Un data frame', 'A data frame'), L('Una lista', 'A list'), L('Un fattore', 'A factor')], 2, L('Le varianti `map_*` danno vettori.', 'The `map_*` variants give vectors.')),
            Q('p2', L('Che cosa significa `\(x) x * 2`?', 'What does `\(x) x * 2` mean?'), [L('Una funzione che raddoppia x', 'A function that doubles x'), L('Un commento', 'A comment'), L('Un errore di battitura', 'A typo'), L('Un percorso di file', 'A file path')], 0, L('È come `function(x) x * 2`.', 'It is like `function(x) x * 2`.')),
            Q('p3', L('Come unisci in un data frame una lista di data frame?', 'How do you stack a list of data frames into one?'), ['map_chr()', 'str_c()', 'unlist_all()', 'list_rbind()'], 3, L('Righe una sotto l’altra.', 'Rows one below the other.')),
        ], task('r', L('Calcola con `map_dbl` la mediana di ogni elemento della lista `tempi`, salvando il risultato in `mediane`.', 'Compute with `map_dbl` the median of each element of the list `tempi`, saving the result in `mediane`.'), 'library(purrr)
tempi <- list(a = c(12, 15, 11), b = c(9, 10))
', [re('mediane\s*(<-|=)\s*(map_dbl\s*\(\s*tempi\s*,\s*(median|\\\\\(\s*\w+\s*\)\s*median\s*\(\s*\w+\s*\)|function\s*\(\s*\w+\s*\)\s*median\s*\(\s*\w+\s*\))\s*\)|tempi\s*(\|>|%>%)\s*map_dbl\s*\(\s*median\s*\))', L('Le mediane con map_dbl', 'The medians with map_dbl'))], '', 'mediane <- map_dbl(tempi, median)'), [
            dMatch(pMatch(), [['map', L('Restituisce una lista', 'Returns a list')], ['map_dbl', L('Vettore di numeri', 'Vector of numbers')], ['map_chr', L('Vettore di testi', 'Vector of texts')], ['map2', L('Due vettori insieme', 'Two vectors together')], ['list_rbind', L('Unisce data frame', 'Stacks data frames')]], L('La famiglia map.', 'The map family.')),
            dOrder(pSort(), ['library(purrr)', 'file <- list.files("dati", pattern = "\\\\.csv$", full.names = TRUE)', 'tabelle <- map(file, read.csv)', 'tutto <- list_rbind(tabelle)'], L('Carica, elenca i file, leggili, uniscili.', 'Load, list the files, read them, stack them.')),
        ]),
    ],
];
