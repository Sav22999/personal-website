<?php
/** Intermediate ML: feature preparation, pipelines, ensembles, cross-validation and tuning, clustering. */

return [
    'id' => 'ml-intermediate', 'family' => 'ml', 'after' => 'ml-base',
    'title' => L('Machine learning intermedio', 'Intermediate Machine Learning'), 'short' => L('ML intermedio', 'ML Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Modelli più robusti: preparare le caratteristiche, pipeline, random forest e gradient boosting, cross-validation, ricerca degli iperparametri e clustering.', 'Sturdier models: preparing features, pipelines, random forests and gradient boosting, cross-validation, hyperparameter search and clustering.'),
    'lessons' => [
        lesson('features', L('Preparare le caratteristiche', 'Preparing features'), [
            P('I modelli vogliono numeri puliti. Tre lavori tipici: riempire i **valori mancanti** (`SimpleImputer`), portare i numeri sulla stessa scala (`StandardScaler`) e trasformare le categorie in colonne 0/1 (**one-hot**, `OneHotEncoder`).', 'Models want clean numbers. Three typical jobs: filling **missing values** (`SimpleImputer`), bringing numbers to the same scale (`StandardScaler`) and turning categories into 0/1 columns (**one-hot**, `OneHotEncoder`).'),
            C('from sklearn.impute import SimpleImputer
from sklearn.preprocessing import StandardScaler, OneHotEncoder

SimpleImputer(strategy="median")        # NaN → mediana della colonna
StandardScaler()                        # media 0, deviazione 1
OneHotEncoder(handle_unknown="ignore")  # "Roma" → [1, 0, 0]', 'python'),
            P('Regola d’oro: queste trasformazioni si **imparano solo sul train** (la mediana, la media) e poi si applicano al test. Se calcoli la media su tutti i dati, informazioni del test “trapelano” nell’addestramento (**data leakage**).', 'Golden rule: these transformations are **learned on train only** (the median, the mean) and then applied to test. If you compute the mean over all data, test information “leaks” into training (**data leakage**).'),
        ], [
            Q('f1', L('Che cosa fa il one-hot encoding?', 'What does one-hot encoding do?'), [L('Trasforma categorie in colonne 0/1', 'Turns categories into 0/1 columns'), L('Cancella le categorie', 'Deletes categories'), L('Ordina i dati', 'Sorts the data'), L('Riempie i valori mancanti', 'Fills missing values')], 0, L('Una colonna per categoria.', 'One column per category.')),
            Q('f2', L('Che cos’è il data leakage?', 'What is data leakage?'), [L('Informazioni del test che finiscono nell’addestramento', 'Test information leaking into training'), L('Un furto di dati', 'A data theft'), L('Un file corrotto', 'A corrupted file'), L('Un errore di rete', 'A network error')], 0, L('Rende il voto troppo ottimista.', 'It makes the score too optimistic.')),
            Q('f3', L('A che cosa serve `StandardScaler`?', 'What is `StandardScaler` for?'), [L('Mettere i numeri sulla stessa scala', 'Putting numbers on the same scale'), L('Contare le righe', 'Counting rows'), L('Codificare testi', 'Encoding text'), L('Disegnare grafici', 'Drawing charts')], 0, L('Media 0, deviazione 1.', 'Mean 0, deviation 1.')),
        ], task('python', L('Crea un `SimpleImputer` con strategia `"median"` e uno `StandardScaler`, salvandoli in `imputer` e `scaler`.', 'Create a `SimpleImputer` with strategy `"median"` and a `StandardScaler`, storing them in `imputer` and `scaler`.'), "from sklearn.impute import SimpleImputer\nfrom sklearn.preprocessing import StandardScaler", [re('imputer\s*=\s*SimpleImputer\s*\(\s*strategy\s*=\s*["\']median["\']\s*\)', L('Imputer con la mediana', 'Median imputer')), re('scaler\s*=\s*StandardScaler\s*\(\s*\)', L('Lo scaler', 'The scaler'))], '', "imputer = SimpleImputer(strategy=\"median\")\nscaler = StandardScaler()"), [
            dMatch(pMatch(), [['SimpleImputer', L('Riempie i NaN', 'Fills NaNs')], ['StandardScaler', L('Stessa scala', 'Same scale')], ['OneHotEncoder', L('Categorie in colonne 0/1', 'Categories into 0/1 columns')], ['Data leakage', L('Il test trapela nel train', 'Test leaks into train')]], L('Preparare i dati.', 'Preparing data.')),
            dFlash(pFlash(), [[L('La media per lo scaler va calcolata sul train', 'The scaler’s mean must be computed on train'), true], [L('Si può calcolare la mediana su train e test insieme', 'You can compute the median on train and test together'), false], [L('Il one-hot crea una colonna per categoria', 'One-hot creates a column per category'), true], [L('I modelli accettano testo grezzo come “Roma”', 'Models accept raw text like “Roma”'), false]], L('Pulito e senza fughe.', 'Clean and leak-free.')),
        ]),
        lesson('pipeline', L('Pipeline', 'Pipelines'), [
            P('Una **pipeline** mette in fila preparazione e modello in un unico oggetto: `fit` impara tutto sul train, `predict` applica tutto nello stesso ordine. Così eviti il data leakage per costruzione e il codice resta corto.', 'A **pipeline** chains preparation and model in a single object: `fit` learns everything on train, `predict` applies everything in the same order. That way you avoid data leakage by design and the code stays short.'),
            C('from sklearn.pipeline import make_pipeline
from sklearn.compose import make_column_transformer
from sklearn.linear_model import LogisticRegression

prep = make_column_transformer(
    (StandardScaler(), ["eta", "reddito"]),
    (OneHotEncoder(handle_unknown="ignore"), ["citta"]),
)
pipe = make_pipeline(prep, LogisticRegression(max_iter=1000))
pipe.fit(X_train, y_train)
print(pipe.score(X_test, y_test))', 'python'),
            P('`make_column_transformer` applica trasformazioni diverse a colonne diverse. La pipeline intera si salva con `joblib.dump(pipe, "modello.joblib")` e si ricarica in produzione.', '`make_column_transformer` applies different transformations to different columns. The whole pipeline is saved with `joblib.dump(pipe, "model.joblib")` and reloaded in production.'),
        ], [
            Q('p1', L('Perché usare una pipeline?', 'Why use a pipeline?'), [L('Preparazione e modello insieme, senza leakage', 'Preparation and model together, without leakage'), L('Per rendere il modello più grande', 'To make the model bigger'), L('Per disegnare grafici', 'To draw charts'), L('Non serve mai', 'It is never useful')], 0, L('Impara tutto solo sul train.', 'It learns everything on train only.')),
            Q('p2', L('A che cosa serve `make_column_transformer`?', 'What is `make_column_transformer` for?'), [L('Trasformazioni diverse per colonne diverse', 'Different transformations for different columns'), L('Unire due tabelle', 'Joining two tables'), L('Rinominare colonne', 'Renaming columns'), L('Cancellare colonne', 'Deleting columns')], 0, L('Numeri scalati, categorie one-hot.', 'Numbers scaled, categories one-hot.')),
            Q('p3', L('Come salvi una pipeline addestrata?', 'How do you save a trained pipeline?'), ['joblib.dump', 'print', 'pipe.close()', 'export()'], 0, L('E `joblib.load` per ricaricarla.', 'And `joblib.load` to reload it.')),
        ], task('python', L('Crea `pipe` con `make_pipeline(StandardScaler(), LogisticRegression())` e addestrala su `X_train`, `y_train`.', 'Create `pipe` with `make_pipeline(StandardScaler(), LogisticRegression())` and train it on `X_train`, `y_train`.'), "from sklearn.pipeline import make_pipeline\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.linear_model import LogisticRegression", [re('pipe\s*=\s*make_pipeline\s*\(\s*StandardScaler\s*\(\s*\)\s*,\s*LogisticRegression\s*\([^)]*\)\s*\)', L('La pipeline esiste', 'The pipeline exists')), re('pipe\.fit\s*\(\s*X_train\s*,\s*y_train\s*\)', L('La addestri', 'You train it'))], '', "pipe = make_pipeline(StandardScaler(), LogisticRegression())\npipe.fit(X_train, y_train)"), [
            dOrder(pSort(), ['prep = make_column_transformer((StandardScaler(), ["eta"]))', 'pipe = make_pipeline(prep, LogisticRegression())', 'pipe.fit(X_train, y_train)', 'print(pipe.score(X_test, y_test))', 'joblib.dump(pipe, "modello.joblib")'], L('Prepara, componi, addestra, valuta, salva.', 'Prepare, compose, train, evaluate, save.')),
            dBug(pBug(), ['scaler = StandardScaler().fit(X)', 'X_train_s = scaler.transform(X_train)', 'X_test_s = scaler.transform(X_test)'], 0, 'scaler = StandardScaler().fit(X_train)', L('Lo scaler va adattato solo sul train.', 'The scaler must be fitted on train only.')),
        ]),
        lesson('ensembles', L('Random forest e boosting', 'Random forests and boosting'), [
            P('Un **ensemble** combina tanti modelli deboli in uno forte. La **random forest** addestra molti alberi su parti diverse dei dati e fa votare. Il **gradient boosting** costruisce alberi uno dopo l’altro, ognuno corregge gli errori dei precedenti.', 'An **ensemble** combines many weak models into a strong one. A **random forest** trains many trees on different parts of the data and lets them vote. **Gradient boosting** builds trees one after another, each fixing the previous ones’ mistakes.'),
            C('from sklearn.ensemble import RandomForestClassifier, HistGradientBoostingClassifier

foresta = RandomForestClassifier(n_estimators=300, random_state=0)
foresta.fit(X_train, y_train)
print(foresta.feature_importances_)   # quanto conta ogni caratteristica

boost = HistGradientBoostingClassifier(learning_rate=0.1)
boost.fit(X_train, y_train)', 'python'),
            P('Sui dati tabellari (fogli di calcolo, database) il gradient boosting è spesso il modello più forte, più delle reti neurali. `feature_importances_` aiuta a capire quali colonne pesano di più.', 'On tabular data (spreadsheets, databases) gradient boosting is often the strongest model, beating neural networks. `feature_importances_` helps you see which columns matter most.'),
        ], [
            Q('e1', L('Come decide una random forest?', 'How does a random forest decide?'), [L('Fa votare molti alberi', 'Many trees vote'), L('Usa un solo albero profondo', 'It uses one deep tree'), L('A caso', 'At random'), L('Con una rete neurale', 'With a neural network')], 0, L('La saggezza della folla.', 'Wisdom of the crowd.')),
            Q('e2', L('Che cosa fa ogni nuovo albero nel boosting?', 'What does each new tree do in boosting?'), [L('Corregge gli errori dei precedenti', 'Fixes the previous ones’ errors'), L('Cancella gli altri', 'Deletes the others'), L('Ripete lo stesso albero', 'Repeats the same tree'), L('Niente', 'Nothing')], 0, L('Un passo alla volta.', 'One step at a time.')),
            Q('e3', L('Su dati tabellari, quale modello è spesso il più forte?', 'On tabular data, which model is often strongest?'), [L('Gradient boosting', 'Gradient boosting'), L('Regressione lineare sempre', 'Linear regression always'), L('Un modello linguistico', 'A language model'), L('Nessun modello', 'No model')], 0, L('Lo dicono molte competizioni.', 'Many competitions show it.')),
        ], task('python', L('Crea una `RandomForestClassifier` con `n_estimators=200`, addestrala e stampa `feature_importances_`.', 'Create a `RandomForestClassifier` with `n_estimators=200`, train it and print `feature_importances_`.'), "from sklearn.ensemble import RandomForestClassifier", [re('(\w+)\s*=\s*RandomForestClassifier\s*\([^)]*n_estimators\s*=\s*200', L('Foresta di 200 alberi', '200-tree forest')), re('\.fit\s*\(\s*X_train\s*,\s*y_train\s*\)', L('La addestri', 'You train it')), re('print\s*\(\s*\w+\.feature_importances_\s*\)', L('Stampi l’importanza', 'You print importance'))], '', "foresta = RandomForestClassifier(n_estimators=200)\nforesta.fit(X_train, y_train)\nprint(foresta.feature_importances_)"), [
            dMatch(pMatch(), [['Random forest', L('Molti alberi che votano', 'Many trees voting')], ['Gradient boosting', L('Alberi che correggono errori', 'Trees fixing errors')], ['n_estimators', L('Numero di alberi', 'Number of trees')], ['feature_importances_', L('Quanto conta ogni colonna', 'How much each column matters')]], L('Unire le forze.', 'Joining forces.')),
            dFlash(pFlash(), [[L('Una random forest usa molti alberi', 'A random forest uses many trees'), true], [L('Nel boosting gli alberi sono indipendenti', 'In boosting trees are independent'), false], [L('Il boosting è forte sui dati tabellari', 'Boosting is strong on tabular data'), true], [L('Un ensemble è sempre un solo modello', 'An ensemble is always a single model'), false]], L('Insieme si sbaglia meno.', 'Together you err less.')),
        ]),
        lesson('tuning', L('Cross-validation e iperparametri', 'Cross-validation and hyperparameters'), [
            P('Gli **iperparametri** sono le scelte che fai tu prima dell’addestramento (`max_depth`, `n_estimators`, `learning_rate`). Per sceglierli senza toccare il test si usa la **cross-validation**: il train si divide in *k* parti, si addestra su *k−1* e si valuta sulla rimanente, a turno.', '**Hyperparameters** are the choices you make before training (`max_depth`, `n_estimators`, `learning_rate`). To pick them without touching the test set you use **cross-validation**: the train set is split into *k* parts, you train on *k−1* and evaluate on the remaining one, in turn.'),
            C('from sklearn.model_selection import cross_val_score, GridSearchCV

print(cross_val_score(foresta, X_train, y_train, cv=5).mean())

griglia = GridSearchCV(
    RandomForestClassifier(random_state=0),
    {"n_estimators": [100, 300], "max_depth": [5, 10, None]},
    cv=5,
)
griglia.fit(X_train, y_train)
print(griglia.best_params_)', 'python'),
            P('`GridSearchCV` prova tutte le combinazioni con la cross-validation e tiene la migliore. Solo alla fine si valuta **una volta** sul test.', '`GridSearchCV` tries every combination with cross-validation and keeps the best. Only at the end do you evaluate **once** on the test set.'),
        ], [
            Q('k1', L('Che cos’è un iperparametro?', 'What is a hyperparameter?'), [L('Una scelta fatta prima dell’addestramento', 'A choice made before training'), L('Un peso imparato', 'A learned weight'), L('Un dato di test', 'A test datum'), L('Un errore', 'An error')], 0, L('Come `max_depth`.', 'Like `max_depth`.')),
            Q('k2', L('Con `cv=5` quante volte si addestra il modello?', 'With `cv=5` how many times is the model trained?'), ['5', '1', '10', '25'], 0, L('Una per ogni parte usata come validazione.', 'Once per part used as validation.')),
            Q('k3', L('Quando si usa il test set?', 'When do you use the test set?'), [L('Una volta, alla fine', 'Once, at the end'), L('A ogni tentativo', 'On every attempt'), L('Per scegliere gli iperparametri', 'To pick hyperparameters'), L('Mai', 'Never')], 0, L('Altrimenti lo “consumi”.', 'Otherwise you “use it up”.')),
        ], task('python', L('Calcola la media di `cross_val_score(modello, X_train, y_train, cv=5)` e stampala.', 'Compute the mean of `cross_val_score(modello, X_train, y_train, cv=5)` and print it.'), "from sklearn.model_selection import cross_val_score", [re('cross_val_score\s*\(\s*modello\s*,\s*X_train\s*,\s*y_train\s*,\s*cv\s*=\s*5\s*\)', L('Cross-validation a 5 parti', '5-fold cross-validation')), re('\.mean\s*\(\s*\)', L('Fai la media', 'You take the mean')), re('print\s*\(', L('La stampi', 'You print it'))], '', "print(cross_val_score(modello, X_train, y_train, cv=5).mean())"), [
            dBlanks(pFill(), "griglia = ___(\n    RandomForestClassifier(),\n    {\"max_depth\": [5, 10]},\n    ___=5,\n)\ngriglia.fit(X_train, y_train)\nprint(griglia.___)", ['GridSearchCV', 'cv', 'best_params_'], ['GridSearch', 'folds', 'params'], L('Cercare la combinazione migliore.', 'Searching for the best combination.')),
            dOrder(pSort(), [L('Dividi train e test', 'Split train and test'), L('Scegli gli iperparametri con la cross-validation sul train', 'Pick hyperparameters with cross-validation on train'), L('Riaddestra con i migliori', 'Retrain with the best'), L('Valuta una volta sul test', 'Evaluate once on test')], L('Il test si tocca per ultimo.', 'The test set comes last.')),
        ]),
        lesson('clustering', L('Clustering', 'Clustering'), [
            P('Senza etichette si possono trovare **gruppi** di esempi simili. **K-means** sceglie *k* centri e assegna ogni punto al più vicino, spostando i centri finché si stabilizzano. Serve a segmentare clienti, raggruppare documenti, scoprire anomalie.', 'Without labels you can find **groups** of similar examples. **K-means** picks *k* centres and assigns each point to the nearest, moving the centres until they settle. It is used to segment customers, group documents, spot anomalies.'),
            C('from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score

km = KMeans(n_clusters=4, n_init="auto", random_state=0)
gruppi = km.fit_predict(X_scalato)
print(km.cluster_centers_)
print(silhouette_score(X_scalato, gruppi))   # da -1 a 1: più alto, gruppi più netti', 'python'),
            P('Il numero di gruppi *k* lo scegli tu: si provano più valori e si confronta il **silhouette score**. Scala sempre i dati prima, perché k-means usa le distanze.', 'You choose the number of groups *k*: try several values and compare the **silhouette score**. Always scale the data first, because k-means uses distances.'),
        ], [
            Q('g1', L('Che cosa fa k-means?', 'What does k-means do?'), [L('Raggruppa i punti attorno a k centri', 'Groups points around k centres'), L('Prevede un prezzo', 'Predicts a price'), L('Classifica con le etichette', 'Classifies with labels'), L('Ordina alfabeticamente', 'Sorts alphabetically')], 0, L('Non servono etichette.', 'No labels needed.')),
            Q('g2', L('Perché scalare i dati prima di k-means?', 'Why scale data before k-means?'), [L('Usa le distanze', 'It uses distances'), L('Per renderlo più colorato', 'To make it more colourful'), L('Non serve', 'It is not needed'), L('Per avere meno righe', 'To have fewer rows')], 0, L('Una colonna in migliaia dominerebbe.', 'A column in thousands would dominate.')),
            Q('g3', L('Come scegli k?', 'How do you choose k?'), [L('Provando valori e confrontando il silhouette score', 'Trying values and comparing the silhouette score'), L('Sempre 2', 'Always 2'), L('Lo sceglie il test set', 'The test set picks it'), L('A caso', 'At random')], 0, L('Più alto è meglio.', 'Higher is better.')),
        ], task('python', L('Crea `KMeans` con `n_clusters=3` e `random_state=0`, e salva in `gruppi` il risultato di `fit_predict(X_scalato)`.', 'Create `KMeans` with `n_clusters=3` and `random_state=0`, and store in `gruppi` the result of `fit_predict(X_scalato)`.'), "from sklearn.cluster import KMeans", [re('(\w+)\s*=\s*KMeans\s*\([^)]*n_clusters\s*=\s*3', L('Tre gruppi', 'Three groups')), re('KMeans\s*\([^)]*random_state\s*=\s*0', L('Risultato ripetibile', 'Repeatable result')), re('gruppi\s*=\s*\w+\.fit_predict\s*\(\s*X_scalato\s*\)', L('Assegni i gruppi', 'You assign the groups'))], '', "km = KMeans(n_clusters=3, random_state=0)\ngruppi = km.fit_predict(X_scalato)"), [
            dMatch(pMatch(), [['KMeans', L('Gruppi attorno a centri', 'Groups around centres')], ['n_clusters', L('Quanti gruppi', 'How many groups')], ['cluster_centers_', L('Posizione dei centri', 'Centre positions')], ['silhouette_score', L('Quanto sono netti i gruppi', 'How clear the groups are')]], L('Gli strumenti del clustering.', 'Clustering tools.')),
            dFlash(pFlash(), [[L('K-means ha bisogno di etichette', 'K-means needs labels'), false], [L('Il numero di gruppi lo sceglie chi programma', 'The programmer chooses the number of groups'), true], [L('Scalare i dati aiuta k-means', 'Scaling data helps k-means'), true], [L('Un silhouette score di -1 indica gruppi perfetti', 'A silhouette score of -1 means perfect groups'), false]], L('Trovare strutture nascoste.', 'Finding hidden structure.')),
        ]),
    ],
];
