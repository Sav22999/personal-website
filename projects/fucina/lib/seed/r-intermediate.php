<?php
/** R intermediate: the tidyverse with dplyr, pipes, ggplot2, reshaping data and simple models. */

return [
    'id' => 'r-intermediate', 'family' => 'r', 'after' => 'r-base',
    'title' => L('R intermedio', 'Intermediate R'), 'short' => L('R intermedio', 'R Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il tidyverse: pipe, dplyr, raggruppamenti, ggplot2, dati lunghi e larghi e un primo modello lineare.', 'The tidyverse: pipes, dplyr, grouping, ggplot2, long and wide data and a first linear model.'),
    'lessons' => [
        lesson('pipe', L('Pipe e dplyr', 'Pipes and dplyr'), [
            P('Il **pipe** `|>` passa il risultato a sinistra come primo argomento della funzione a destra: le operazioni si leggono dall’alto in basso. **dplyr** offre verbi chiari: `filter`, `select`, `mutate`, `arrange`.', 'The **pipe** `|>` passes the left result as the first argument of the function on the right: operations read top to bottom. **dplyr** offers clear verbs: `filter`, `select`, `mutate`, `arrange`.'),
            C('library(dplyr)

vendite |>
  filter(regione == "Nord") |>
  mutate(totale = prezzo * pezzi) |>
  select(prodotto, totale) |>
  arrange(desc(totale))', 'r'),
            P('Dentro i verbi di dplyr le colonne si scrivono senza `$` né virgolette.', 'Inside dplyr verbs, columns are written without `$` or quotes.'),
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
            dFlash(pFlash(), [[L('`summarise` restituisce una riga per gruppo', '`summarise` returns one row per group'), true], [L('`group_by` da solo cambia i numeri', '`group_by` alone changes the numbers'), false], [L('Si possono calcolare più valori in un `summarise`', 'You can compute several values in one `summarise`'), true]], L('`group_by` prepara, `summarise` calcola.', '`group_by` prepares, `summarise` computes.')),
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
            dFlash(pFlash(), [[L('Nei dati ordinati ogni colonna è una variabile', 'In tidy data each column is a variable'), true], [L('ggplot2 preferisce i dati larghi', 'ggplot2 prefers wide data'), false], [L('`pivot_wider` è il contrario di `pivot_longer`', '`pivot_wider` is the opposite of `pivot_longer`'), true]], L('Una riga per ogni misura.', 'One row per measurement.')),
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
            dFlash(pFlash(), [[L('`summary(lm(...))` mostra i coefficienti', '`summary(lm(...))` shows the coefficients'), true], [L('R² vicino a 0 indica un buon modello', 'R² near 0 means a good model'), false], [L('Correlazione non è causalità', 'Correlation is not causation'), true]], L('R² vicino a 1 è un buon adattamento.', 'R² near 1 is a good fit.')),
        ]),
    ],
];
