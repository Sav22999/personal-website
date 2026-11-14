<?php
/** Maths for AI, the intuition: vectors and similarity, matrices, probability, derivatives and gradient descent, with NumPy. */

return [
    'id' => 'ai-math', 'family' => 'ai', 'after' => 'ai-base',
    'title' => L('Matematica per l’AI', 'Maths for AI'), 'short' => L('Matematica AI', 'AI Maths'), 'level' => L('Base', 'Basics'),
    'desc' => L('La matematica che serve davvero, spiegata con l’intuizione e NumPy: vettori e somiglianza, matrici, probabilità, derivate e discesa del gradiente.', 'The maths you really need, explained through intuition and NumPy: vectors and similarity, matrices, probability, derivatives and gradient descent.'),
    'lessons' => [
        lesson('vectors', L('Vettori', 'Vectors'), [
            P('Per un modello tutto diventa numeri. Un **vettore** è un elenco di numeri che descrive qualcosa: una casa come `[metri, stanze, anni]`, una parola come centinaia di numeri (un **embedding**). In Python si usano gli array di **NumPy**.', 'To a model everything becomes numbers. A **vector** is a list of numbers describing something: a house as `[metres, rooms, years]`, a word as hundreds of numbers (an **embedding**). In Python you use **NumPy** arrays.'),
            C('import numpy as np

casa = np.array([85, 3, 20])     # metri, stanze, anni
altra = np.array([120, 4, 5])
print(casa + altra)               # [205   7  25]
print(casa * 2)                   # [170   6  40]
print(np.dot(casa, altra))        # prodotto scalare: 85*120 + 3*4 + 20*5', 'python'),
            P('Il **prodotto scalare** (`np.dot`) moltiplica elemento per elemento e somma: è il mattone di quasi tutti i calcoli nelle reti neurali.', 'The **dot product** (`np.dot`) multiplies element by element and sums: it is the building block of nearly every neural network computation.'),
        ], [
            Q('v1', L('Che cos’è un vettore in ML?', 'What is a vector in ML?'), [L('Un elenco di numeri che descrive qualcosa', 'A list of numbers describing something'), L('Una freccia sullo schermo', 'An arrow on screen'), L('Un file di testo', 'A text file'), L('Un tipo di grafico', 'A type of chart')], 0, L('Le caratteristiche diventano numeri.', 'Features become numbers.')),
            Q('v2', L('Che cosa calcola `np.dot(a, b)`?', 'What does `np.dot(a, b)` compute?'), [L('La somma dei prodotti elemento per elemento', 'The sum of element-wise products'), L('La media', 'The mean'), L('Il massimo', 'The maximum'), L('La lunghezza dell’array', 'The array length')], 0, L('Il prodotto scalare.', 'The dot product.')),
            Q('v3', L('Che cos’è un embedding?', 'What is an embedding?'), [L('Un vettore che rappresenta una parola o un testo', 'A vector representing a word or text'), L('Un video incorporato', 'An embedded video'), L('Un file compresso', 'A compressed file'), L('Un tipo di font', 'A kind of font')], 0, L('Significati simili, vettori vicini.', 'Similar meanings, nearby vectors.')),
        ], task('python', L('Crea con NumPy i vettori `a = [1, 2, 3]` e `b = [4, 5, 6]` e stampa il loro prodotto scalare con `np.dot`.', 'With NumPy create the vectors `a = [1, 2, 3]` and `b = [4, 5, 6]` and print their dot product with `np.dot`.'), 'import numpy as np', [re('a\s*=\s*np\.array\(\s*\[\s*1\s*,\s*2\s*,\s*3\s*\]\s*\)', L('Il vettore a', 'Vector a')), re('b\s*=\s*np\.array\(\s*\[\s*4\s*,\s*5\s*,\s*6\s*\]\s*\)', L('Il vettore b', 'Vector b')), re('print\s*\(\s*np\.dot\s*\(\s*a\s*,\s*b\s*\)\s*\)', L('Stampi il prodotto scalare', 'You print the dot product'))], '', "a = np.array([1, 2, 3])\nb = np.array([4, 5, 6])\nprint(np.dot(a, b))"), [
            dBlanks(pFill(), "import numpy as ___\nv = np.___([2, 4, 6])\nprint(np.___(v, v))", ['np', 'array', 'dot'], ['numpy', 'list', 'sum'], L('Importa, crea, moltiplica.', 'Import, create, multiply.')),
            dFlash(pFlash(), [[L('Il prodotto scalare di [1,2] e [3,4] è 11', 'The dot product of [1,2] and [3,4] is 11'), true], [L('Un embedding è un’immagine', 'An embedding is an image'), false], [L('`casa * 2` raddoppia ogni elemento', '`casa * 2` doubles every element'), true], [L('NumPy serve solo per i grafici', 'NumPy is only for charts'), false]], L('1·3 + 2·4 = 11.', '1·3 + 2·4 = 11.')),
        ]),
        lesson('similarity', L('Distanza e somiglianza', 'Distance and similarity'), [
            P('Se due cose sono vettori, possiamo misurare quanto sono **simili**. La **distanza euclidea** è la distanza “in linea d’aria”. La **similarità del coseno** guarda l’angolo tra i vettori: 1 vuol dire stessa direzione, 0 nessuna relazione.', 'If two things are vectors, we can measure how **similar** they are. **Euclidean distance** is the straight-line distance. **Cosine similarity** looks at the angle between vectors: 1 means same direction, 0 no relation.'),
            C('import numpy as np

def coseno(a, b):
    return np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b))

gatto = np.array([0.9, 0.1, 0.8])
micio = np.array([0.8, 0.2, 0.9])
auto = np.array([0.1, 0.9, 0.0])
print(coseno(gatto, micio))   # ≈ 0.99, molto simili
print(coseno(gatto, auto))    # ≈ 0.16, poco simili', 'python'),
            P('È così che funzionano la ricerca semantica e i sistemi RAG: si trasforma la domanda in embedding e si cercano i documenti con il coseno più alto.', 'This is how semantic search and RAG systems work: turn the question into an embedding and find the documents with the highest cosine.'),
        ], [
            Q('s1', L('Una similarità del coseno vicina a 1 significa…', 'A cosine similarity close to 1 means…'), [L('Vettori molto simili', 'Very similar vectors'), L('Vettori opposti', 'Opposite vectors'), L('Errore', 'Error'), L('Vettori vuoti', 'Empty vectors')], 0, L('Stessa direzione.', 'Same direction.')),
            Q('s2', L('Che cosa calcola `np.linalg.norm(v)`?', 'What does `np.linalg.norm(v)` compute?'), [L('La lunghezza del vettore', 'The vector’s length'), L('La somma', 'The sum'), L('Il numero di elementi', 'The number of elements'), L('Il valore minimo', 'The minimum value')], 0, L('Serve a normalizzare.', 'It is used to normalise.')),
            Q('s3', L('A che cosa serve la similarità nella ricerca semantica?', 'What is similarity for in semantic search?'), [L('Trovare i testi più vicini alla domanda', 'Finding the texts closest to the question'), L('Ordinare per data', 'Sorting by date'), L('Comprimere i file', 'Compressing files'), L('Contare le parole', 'Counting words')], 0, L('Vicino nel significato, non nelle parole.', 'Close in meaning, not in words.')),
        ], task('python', L('Completa la funzione `coseno(a, b)` che restituisce il prodotto scalare diviso il prodotto delle norme.', 'Complete the function `coseno(a, b)` returning the dot product divided by the product of the norms.'), "import numpy as np\n\ndef coseno(a, b):\n    return ", [re('def\s+coseno\s*\(\s*a\s*,\s*b\s*\)', L('La funzione esiste', 'The function exists')), re('return\s+np\.dot\s*\(\s*a\s*,\s*b\s*\)\s*/\s*\(\s*np\.linalg\.norm\s*\(\s*a\s*\)\s*\*\s*np\.linalg\.norm\s*\(\s*b\s*\)\s*\)', L('Prodotto scalare diviso le norme', 'Dot product over the norms'))], '', "def coseno(a, b):\n    return np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b))"), [
            dMatch(pMatch(), [[L('Coseno ≈ 1', 'Cosine ≈ 1'), L('Molto simili', 'Very similar')], [L('Coseno ≈ 0', 'Cosine ≈ 0'), L('Nessuna relazione', 'No relation')], [L('Distanza euclidea', 'Euclidean distance'), L('In linea d’aria', 'Straight line')], [L('Norma', 'Norm'), L('Lunghezza del vettore', 'Vector length')]], L('Misurare la somiglianza.', 'Measuring similarity.')),
            dOrder(pSort(), [L('Trasforma la domanda in embedding', 'Turn the question into an embedding'), L('Calcola il coseno con ogni documento', 'Compute the cosine with each document'), L('Ordina dal più alto', 'Sort from highest'), L('Prendi i primi risultati', 'Take the top results')], L('La ricerca semantica in quattro passi.', 'Semantic search in four steps.')),
        ]),
        lesson('matrices', L('Matrici', 'Matrices'), [
            P('Una **matrice** è una tabella di numeri: righe e colonne. Un dataset è una matrice (una riga per esempio, una colonna per caratteristica); anche i pesi di uno strato di rete neurale sono una matrice. La sua forma si legge con `.shape`.', 'A **matrix** is a table of numbers: rows and columns. A dataset is a matrix (one row per example, one column per feature); the weights of a neural network layer are a matrix too. Its shape is read with `.shape`.'),
            C('import numpy as np

X = np.array([[85, 3], [120, 4], [60, 2]])   # 3 case, 2 caratteristiche
w = np.array([1500, 10000])                  # peso di ogni caratteristica
print(X.shape)        # (3, 2)
print(X @ w)          # prezzo stimato di ogni casa', 'python'),
            P('Il **prodotto matrice-vettore** (`@`) calcola in un colpo il prodotto scalare di ogni riga con i pesi: è quello che fa uno strato di rete neurale, milioni di volte, sulle GPU.', 'The **matrix-vector product** (`@`) computes in one go each row’s dot product with the weights: it is what a neural network layer does, millions of times, on GPUs.'),
        ], [
            Q('m1', L('Che cosa restituisce `X.shape` per 3 righe e 2 colonne?', 'What does `X.shape` return for 3 rows and 2 columns?'), ['(3, 2)', '(2, 3)', '6', '[3, 2, 1]'], 0, L('Prima le righe, poi le colonne.', 'Rows first, then columns.')),
            Q('m2', L('Che cosa fa l’operatore `@` in NumPy?', 'What does the `@` operator do in NumPy?'), [L('Il prodotto tra matrici', 'Matrix multiplication'), L('Un commento', 'A comment'), L('Una email', 'An email'), L('La divisione', 'Division')], 0, L('Righe per colonne.', 'Rows times columns.')),
            Q('m3', L('In un dataset, che cosa è di solito una riga?', 'In a dataset, what is a row usually?'), [L('Un esempio', 'One example'), L('Una caratteristica', 'One feature'), L('Un modello', 'A model'), L('Un errore', 'An error')], 0, L('Le colonne sono le caratteristiche.', 'Columns are the features.')),
        ], task('python', L('Data la matrice `X` e i pesi `w`, calcola le previsioni con `@` e salvale in `y`, poi stampa `X.shape`.', 'Given matrix `X` and weights `w`, compute the predictions with `@` into `y`, then print `X.shape`.'), "import numpy as np\nX = np.array([[1, 2], [3, 4]])\nw = np.array([10, 1])", [re('y\s*=\s*X\s*@\s*w', L('Calcoli le previsioni', 'You compute the predictions')), re('print\s*\(\s*X\.shape\s*\)', L('Stampi la forma', 'You print the shape'))], '', "y = X @ w\nprint(X.shape)"), [
            dMatch(pMatch(), [[L('Riga', 'Row'), L('Un esempio', 'An example')], [L('Colonna', 'Column'), L('Una caratteristica', 'A feature')], ['.shape', L('Forma della matrice', 'Matrix shape')], ['@', L('Prodotto matrice-vettore', 'Matrix-vector product')]], L('Leggere una matrice.', 'Reading a matrix.')),
            dFlash(pFlash(), [[L('I pesi di uno strato neurale formano una matrice', 'A neural layer’s weights form a matrix'), true], [L('`.shape` di una matrice 4×2 è (2, 4)', '`.shape` of a 4×2 matrix is (2, 4)'), false], [L('Le GPU sono veloci nei prodotti tra matrici', 'GPUs are fast at matrix products'), true], [L('`@` in NumPy indica un decoratore', '`@` in NumPy means a decorator'), false]], L('Righe, colonne, prodotti.', 'Rows, columns, products.')),
        ]),
        lesson('probability', L('Probabilità', 'Probability'), [
            P('I modelli non dicono “sì” o “no”: danno **probabilità**. Un classificatore dice “spam al 93%”; un LLM assegna una probabilità a ogni token possibile. Le probabilità di tutte le opzioni sommano a 1.', 'Models do not say “yes” or “no”: they give **probabilities**. A classifier says “93% spam”; an LLM assigns a probability to every possible token. The probabilities of all options add up to 1.'),
            C('import numpy as np

def softmax(punteggi):
    e = np.exp(punteggi - np.max(punteggi))
    return e / e.sum()

print(softmax(np.array([2.0, 1.0, 0.1])))   # [0.66 0.24 0.10], somma 1', 'python'),
            P('La funzione **softmax** trasforma punteggi qualsiasi in probabilità. La **temperatura** di un LLM divide i punteggi prima della softmax: bassa rende la scelta più prevedibile, alta più varia e creativa.', 'The **softmax** function turns any scores into probabilities. An LLM’s **temperature** divides the scores before the softmax: low makes the choice more predictable, high more varied and creative.'),
        ], [
            Q('p1', L('Quanto fanno le probabilità di tutte le opzioni?', 'What do the probabilities of all options add up to?'), ['1', '100', '0', L('Dipende', 'It depends')], 0, L('O 100%.', 'Or 100%.')),
            Q('p2', L('Che cosa fa la softmax?', 'What does softmax do?'), [L('Trasforma punteggi in probabilità', 'Turns scores into probabilities'), L('Ordina un elenco', 'Sorts a list'), L('Cancella i negativi', 'Deletes negatives'), L('Arrotonda i numeri', 'Rounds numbers')], 0, L('Positivi e con somma 1.', 'Positive and summing to 1.')),
            Q('p3', L('Una temperatura bassa rende un LLM…', 'A low temperature makes an LLM…'), [L('Più prevedibile', 'More predictable'), L('Più creativo', 'More creative'), L('Più lento', 'Slower'), L('Multilingue', 'Multilingual')], 0, L('Sceglie quasi sempre il token più probabile.', 'It almost always picks the most likely token.')),
        ], task('python', L('Completa `softmax`: calcola `e = np.exp(punteggi - np.max(punteggi))` e restituisci `e / e.sum()`.', 'Complete `softmax`: compute `e = np.exp(punteggi - np.max(punteggi))` and return `e / e.sum()`.'), "import numpy as np\n\ndef softmax(punteggi):\n    ", [re('e\s*=\s*np\.exp\s*\(\s*punteggi\s*-\s*np\.max\s*\(\s*punteggi\s*\)\s*\)', L('Calcoli gli esponenziali', 'You compute the exponentials')), re('return\s+e\s*/\s*e\.sum\s*\(\s*\)', L('Normalizzi', 'You normalise'))], '', "def softmax(punteggi):\n    e = np.exp(punteggi - np.max(punteggi))\n    return e / e.sum()"), [
            dMatch(pMatch(), [[L('Probabilità', 'Probability'), L('Tra 0 e 1', 'Between 0 and 1')], ['Softmax', L('Punteggi → probabilità', 'Scores → probabilities')], [L('Temperatura bassa', 'Low temperature'), L('Risposte prevedibili', 'Predictable answers')], [L('Temperatura alta', 'High temperature'), L('Risposte varie', 'Varied answers')]], L('Il caso, misurato.', 'Chance, measured.')),
            dFlash(pFlash(), [[L('Un LLM assegna una probabilità a ogni token', 'An LLM assigns a probability to each token'), true], [L('Le probabilità possono essere negative', 'Probabilities can be negative'), false], [L('La softmax produce valori che sommano a 1', 'Softmax outputs values summing to 1'), true], [L('Temperatura alta vuol dire risposte identiche', 'High temperature means identical answers'), false]], L('Probabilità ovunque.', 'Probability everywhere.')),
        ]),
        lesson('gradient', L('Derivate e discesa del gradiente', 'Derivatives and gradient descent'), [
            P('Addestrare un modello vuol dire ridurre un **errore** (la *loss*). La **derivata** dice in che direzione e quanto cambia l’errore se muovi un parametro. La **discesa del gradiente** fa un piccolo passo nella direzione che fa scendere l’errore, e ripete.', 'Training a model means reducing an **error** (the *loss*). The **derivative** tells you in which direction and how much the error changes if you move a parameter. **Gradient descent** takes a small step in the direction that lowers the error, and repeats.'),
            C('# errore(w) = (w - 3)²  → minimo in w = 3; derivata = 2·(w - 3)
w = 0.0
passo = 0.1          # learning rate
for _ in range(50):
    gradiente = 2 * (w - 3)
    w = w - passo * gradiente
print(round(w, 3))   # ≈ 3.0', 'python'),
            P('Il **learning rate** è la lunghezza del passo: troppo grande e si salta oltre il minimo, troppo piccolo e l’addestramento non finisce più. Le reti neurali fanno questo su milioni di parametri insieme, con la **backpropagation** che calcola tutte le derivate.', 'The **learning rate** is the step length: too big and you overshoot the minimum, too small and training takes forever. Neural networks do this on millions of parameters at once, with **backpropagation** computing all the derivatives.'),
        ], [
            Q('g1', L('Che cosa vuole ridurre l’addestramento?', 'What does training try to reduce?'), [L('L’errore (loss)', 'The error (loss)'), L('Il numero di dati', 'The amount of data'), L('La velocità', 'The speed'), L('I parametri a zero', 'Parameters to zero')], 0, L('Meno errore, modello migliore.', 'Less error, better model.')),
            Q('g2', L('Che cosa succede con un learning rate troppo grande?', 'What happens with a learning rate that is too big?'), [L('Si salta oltre il minimo', 'You overshoot the minimum'), L('L’addestramento è perfetto', 'Training is perfect'), L('Il modello si spegne', 'The model shuts down'), L('Niente', 'Nothing')], 0, L('Passi troppo lunghi.', 'Steps too long.')),
            Q('g3', L('Che cosa calcola la backpropagation?', 'What does backpropagation compute?'), [L('Le derivate di tutti i parametri', 'The derivatives of all parameters'), L('Le probabilità finali', 'The final probabilities'), L('Il numero di strati', 'The number of layers'), L('La dimensione del dataset', 'The dataset size')], 0, L('Così si sa come aggiornare ogni peso.', 'So you know how to update each weight.')),
        ], task('python', L('Completa il passo della discesa del gradiente: aggiorna `w` sottraendo `passo * gradiente`.', 'Complete the gradient descent step: update `w` by subtracting `passo * gradiente`.'), "w = 10.0\npasso = 0.1\nfor _ in range(100):\n    gradiente = 2 * (w - 3)\n    ", [re('w\s*=\s*w\s*-\s*passo\s*\*\s*gradiente|w\s*-=\s*passo\s*\*\s*gradiente', L('Fai il passo in discesa', 'You take the downhill step'))], '', 'w = w - passo * gradiente'), [
            dOrder(pSort(), [L('Calcola l’errore', 'Compute the error'), L('Calcola il gradiente', 'Compute the gradient'), L('Aggiorna i parametri', 'Update the parameters'), L('Ripeti', 'Repeat')], L('Il ciclo dell’addestramento.', 'The training loop.')),
            dBug(pBug(), ['w = 0.0', 'passo = 0.1', 'for _ in range(50):', '    gradiente = 2 * (w - 3)', '    w = w + passo * gradiente'], 4, '    w = w - passo * gradiente', L('Per scendere si sottrae il gradiente.', 'To go down you subtract the gradient.')),
        ]),
    ],
];
