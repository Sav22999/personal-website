<?php
/** Deep learning basics with PyTorch: neurons and layers, tensors, the training loop, CNNs, overfitting tricks, Datasets and DataLoaders, transfer learning and saving, RNNs and LSTMs. */

return [
    'id' => 'dl-base', 'family' => 'dl', 'after' => 'ml-base',
    'title' => L('Deep learning', 'Deep Learning'), 'short' => L('DL base', 'DL Basics'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Le reti neurali con PyTorch: neuroni e strati, tensori, il ciclo di addestramento, reti convoluzionali, dropout e GPU, Dataset e DataLoader, transfer learning e salvataggio, RNN e LSTM.', 'Neural networks with PyTorch: neurons and layers, tensors, the training loop, convolutional networks, dropout and GPUs, Datasets and DataLoaders, transfer learning and saving, RNNs and LSTMs.'),
    'lessons' => [
        lesson('neurons', L('Neuroni e strati', 'Neurons and layers'), [
            P('Un **neurone** artificiale fa una somma pesata dei suoi input, aggiunge un **bias** e passa il risultato in una **funzione di attivazione** non lineare, come **ReLU** (che azzera i negativi). Tanti neuroni affiancati formano uno **strato**; più strati in fila formano una rete **profonda**.', 'An artificial **neuron** takes a weighted sum of its inputs, adds a **bias** and passes the result through a non-linear **activation function**, such as **ReLU** (which zeroes negatives). Many neurons side by side form a **layer**; several layers in a row form a **deep** network.'),
            C('import torch
from torch import nn

rete = nn.Sequential(
    nn.Linear(4, 16),   # 4 input → 16 neuroni
    nn.ReLU(),
    nn.Linear(16, 3),   # 16 → 3 classi
)
print(rete)', 'python'),
            P('Senza attivazioni non lineari, tanti strati equivarrebbero a uno solo: è la non linearità che permette di imparare relazioni complesse.', 'Without non-linear activations, many layers would be equivalent to a single one: non-linearity is what allows learning complex relations.'),
        ], [
            Q('n1', L('Che cosa fa ReLU?', 'What does ReLU do?'), [L('Azzera i valori negativi', 'Zeroes negative values'), L('Raddoppia i valori', 'Doubles values'), L('Ordina i valori', 'Sorts values'), L('Calcola la media', 'Computes the mean')], 0, L('max(0, x).', 'max(0, x).')),
            Q('n2', L('Perché servono le attivazioni non lineari?', 'Why are non-linear activations needed?'), [L('Altrimenti più strati equivalgono a uno', 'Otherwise many layers equal one'), L('Per velocizzare', 'To speed up'), L('Per usare meno memoria', 'To use less memory'), L('Non servono', 'They are not needed')], 0, L('La non linearità dà potenza.', 'Non-linearity gives power.')),
            Q('n3', L('`nn.Linear(4, 16)` collega…', '`nn.Linear(4, 16)` connects…'), [L('4 input a 16 neuroni', '4 inputs to 16 neurons'), L('16 input a 4 neuroni', '16 inputs to 4 neurons'), L('4 strati', '4 layers'), L('16 immagini', '16 images')], 0, L('Prima gli input, poi le uscite.', 'Inputs first, then outputs.')),
        ], task('python', L('Crea `rete` con `nn.Sequential`: `nn.Linear(10, 32)`, `nn.ReLU()`, `nn.Linear(32, 1)`.', 'Create `rete` with `nn.Sequential`: `nn.Linear(10, 32)`, `nn.ReLU()`, `nn.Linear(32, 1)`.'), "import torch\nfrom torch import nn", [re('rete\s*=\s*nn\.Sequential\s*\(', L('Rete sequenziale', 'Sequential network')), re('nn\.Linear\s*\(\s*10\s*,\s*32\s*\)\s*,\s*nn\.ReLU\s*\(\s*\)\s*,\s*nn\.Linear\s*\(\s*32\s*,\s*1\s*\)', L('Strati nell’ordine giusto', 'Layers in the right order'))], '', "rete = nn.Sequential(\n    nn.Linear(10, 32),\n    nn.ReLU(),\n    nn.Linear(32, 1),\n)"), [
            dOrder(pSort(), [L('Somma pesata degli input', 'Weighted sum of inputs'), L('Aggiungi il bias', 'Add the bias'), L('Applica l’attivazione', 'Apply the activation'), L('Passa al prossimo strato', 'Pass to the next layer')], L('Il viaggio di un dato in un neurone.', 'A datum’s trip through a neuron.')),
            dBug(pBug(), ['rete = nn.Sequential(', '    nn.Linear(8, 16),', '    nn.ReLU(),', '    nn.Linear(32, 2),', ')'], 3, '    nn.Linear(16, 2),', L('Gli input di uno strato sono le uscite del precedente.', 'A layer’s inputs are the previous layer’s outputs.')),
        ]),
        lesson('tensors', L('Tensori', 'Tensors'), [
            P('In PyTorch i dati sono **tensori**: array a più dimensioni, come quelli di NumPy, che possono stare sulla **GPU** e ricordano le operazioni per calcolare le derivate in automatico (**autograd**).', 'In PyTorch data are **tensors**: multi-dimensional arrays, like NumPy’s, that can live on the **GPU** and remember operations to compute derivatives automatically (**autograd**).'),
            C('import torch

x = torch.tensor([[1.0, 2.0], [3.0, 4.0]])
print(x.shape)          # torch.Size([2, 2])

w = torch.tensor(3.0, requires_grad=True)
y = w ** 2
y.backward()            # calcola dy/dw
print(w.grad)           # tensor(6.)

device = "cuda" if torch.cuda.is_available() else "cpu"
x = x.to(device)', 'python'),
            P('Un batch di 32 immagini a colori 64×64 è un tensore di forma `[32, 3, 64, 64]`: esempi, canali, altezza, larghezza.', 'A batch of 32 colour 64×64 images is a tensor of shape `[32, 3, 64, 64]`: examples, channels, height, width.'),
        ], [
            Q('t1', L('Che cosa fa `y.backward()`?', 'What does `y.backward()` do?'), [L('Calcola le derivate', 'Computes the derivatives'), L('Torna indietro di un passo', 'Goes back one step'), L('Cancella y', 'Deletes y'), L('Inverte il tensore', 'Reverses the tensor')], 0, L('Autograd.', 'Autograd.')),
            Q('t2', L('Che cosa indica `requires_grad=True`?', 'What does `requires_grad=True` mean?'), [L('Che servono le derivate di quel tensore', 'That tensor’s derivatives are needed'), L('Che è sulla GPU', 'That it is on the GPU'), L('Che è un intero', 'That it is an integer'), L('Che è costante', 'That it is constant')], 0, L('Tipico dei pesi.', 'Typical of weights.')),
            Q('t3', L('Come sposti un tensore sulla GPU?', 'How do you move a tensor to the GPU?'), [L('Con `.to("cuda")`', 'With `.to("cuda")`'), L('Con `.gpu = True`', 'With `.gpu = True`'), L('Non si può', 'You cannot'), L('Con `print`', 'With `print`')], 0, L('E il modello va spostato insieme.', 'And the model must move too.')),
        ], task('python', L('Crea `w = torch.tensor(2.0, requires_grad=True)`, calcola `y = w ** 3`, chiama `y.backward()` e stampa `w.grad`.', 'Create `w = torch.tensor(2.0, requires_grad=True)`, compute `y = w ** 3`, call `y.backward()` and print `w.grad`.'), "import torch", [re('w\s*=\s*torch\.tensor\s*\(\s*2\.0\s*,\s*requires_grad\s*=\s*True\s*\)', L('w con gradiente', 'w with gradient')), re('y\s*=\s*w\s*\*\*\s*3', L('y = w³', 'y = w³')), re('y\.backward\s*\(\s*\)', L('Calcoli la derivata', 'You compute the derivative')), re('print\s*\(\s*w\.grad\s*\)', L('La stampi', 'You print it'))], '', "w = torch.tensor(2.0, requires_grad=True)\ny = w ** 3\ny.backward()\nprint(w.grad)"), [
            dMatch(pMatch(), [[L('Tensore', 'Tensor'), L('Array a più dimensioni', 'Multi-dimensional array')], ['autograd', L('Derivate automatiche', 'Automatic derivatives')], ['.backward()', L('Calcola i gradienti', 'Computes gradients')], ['.to("cuda")', L('Sposta sulla GPU', 'Moves to the GPU')], ['[32, 3, 64, 64]', L('Batch di immagini a colori', 'Batch of colour images')]], L('Il tensore e i suoi poteri.', 'The tensor and its powers.')),
            dFlash(pFlash(), [[L('La derivata di w² in w = 3 è 6', 'The derivative of w² at w = 3 is 6'), true], [L('I tensori non possono stare sulla GPU', 'Tensors cannot live on the GPU'), false], [L('Il primo numero della forma di un batch è il numero di esempi', 'The first number of a batch shape is the number of examples'), true], [L('Autograd richiede di scrivere le derivate a mano', 'Autograd requires writing derivatives by hand'), false]], L('PyTorch fa i conti per te.', 'PyTorch does the maths for you.')),
        ]),
        lesson('training', L('Il ciclo di addestramento', 'The training loop'), [
            P('In PyTorch l’addestramento si scrive a mano, ed è sempre lo stesso schema: calcola le previsioni, calcola la **loss**, azzera i gradienti, `backward()`, fai un passo con l’**ottimizzatore**. Un giro completo sui dati è un’**epoca**.', 'In PyTorch you write training by hand, and it is always the same pattern: compute predictions, compute the **loss**, zero the gradients, `backward()`, take a step with the **optimiser**. One full pass over the data is an **epoch**.'),
            C('perdita = nn.CrossEntropyLoss()
ottimizzatore = torch.optim.Adam(rete.parameters(), lr=1e-3)

for epoca in range(10):
    for X, y in caricatore:          # DataLoader: batch di esempi
        previsti = rete(X)
        loss = perdita(previsti, y)
        ottimizzatore.zero_grad()
        loss.backward()
        ottimizzatore.step()
    print(epoca, loss.item())', 'python'),
            P('`CrossEntropyLoss` si usa per la classificazione, `MSELoss` per la regressione. **Adam** è un ottimizzatore che adatta da solo il passo per ogni parametro: un buon punto di partenza.', '`CrossEntropyLoss` is for classification, `MSELoss` for regression. **Adam** is an optimiser that adapts the step for each parameter on its own: a good starting point.'),
        ], [
            Q('l1', L('Perché si chiama `zero_grad()`?', 'Why call `zero_grad()`?'), [L('I gradienti altrimenti si sommano', 'Otherwise gradients accumulate'), L('Per cancellare il modello', 'To delete the model'), L('Per fermare l’addestramento', 'To stop training'), L('Per salvare', 'To save')], 0, L('PyTorch accumula per scelta.', 'PyTorch accumulates by design.')),
            Q('l2', L('Che cos’è un’epoca?', 'What is an epoch?'), [L('Un giro completo sui dati', 'One full pass over the data'), L('Un solo esempio', 'A single example'), L('Un’ora di calcolo', 'An hour of compute'), L('Uno strato', 'A layer')], 0, L('Fatta di tanti batch.', 'Made of many batches.')),
            Q('l3', L('Quale loss per la classificazione?', 'Which loss for classification?'), ['CrossEntropyLoss', 'MSELoss', 'L1Loss', 'ReLU'], 0, L('MSE è per la regressione.', 'MSE is for regression.')),
        ], task('python', L('Completa il corpo del ciclo: calcola `loss`, azzera i gradienti, chiama `backward()` e fai lo `step()`.', 'Complete the loop body: compute `loss`, zero the gradients, call `backward()` and take the `step()`.'), "for X, y in caricatore:\n    previsti = rete(X)\n    ", [re('loss\s*=\s*perdita\s*\(\s*previsti\s*,\s*y\s*\)', L('Calcoli la loss', 'You compute the loss')), re('ottimizzatore\.zero_grad\s*\(\s*\)[\s\S]*loss\.backward\s*\(\s*\)[\s\S]*ottimizzatore\.step\s*\(\s*\)', L('Azzera, backward, step, in ordine', 'Zero, backward, step, in order'))], '', "loss = perdita(previsti, y)\n    ottimizzatore.zero_grad()\n    loss.backward()\n    ottimizzatore.step()"), [
            dOrder(pSort(), ['previsti = rete(X)', 'loss = perdita(previsti, y)', 'ottimizzatore.zero_grad()', 'loss.backward()', 'ottimizzatore.step()'], L('L’ordine sacro del ciclo.', 'The loop’s sacred order.')),
            dBlanks(pFill(), "ottimizzatore = torch.optim.___(rete.___(), lr=1e-3)\nperdita = nn.___()", ['Adam', 'parameters', 'CrossEntropyLoss'], ['Adamo', 'weights', 'ReLU'], L('Ottimizzatore e funzione di perdita.', 'Optimiser and loss function.')),
        ]),
        lesson('cnn', L('Reti convoluzionali', 'Convolutional networks'), [
            P('Per le immagini si usano le **reti convoluzionali** (CNN). Uno strato `Conv2d` fa scorrere piccoli filtri sull’immagine e impara a riconoscere bordi, poi forme, poi oggetti. Il **pooling** riduce la dimensione tenendo l’informazione più forte.', 'For images you use **convolutional networks** (CNNs). A `Conv2d` layer slides small filters over the image and learns to recognise edges, then shapes, then objects. **Pooling** shrinks the size keeping the strongest information.'),
            C('cnn = nn.Sequential(
    nn.Conv2d(3, 16, kernel_size=3, padding=1),   # 3 canali RGB → 16 filtri
    nn.ReLU(),
    nn.MaxPool2d(2),                              # 64×64 → 32×32
    nn.Conv2d(16, 32, kernel_size=3, padding=1),
    nn.ReLU(),
    nn.MaxPool2d(2),                              # → 16×16
    nn.Flatten(),
    nn.Linear(32 * 16 * 16, 10),                  # 10 classi
)', 'python'),
            P('Spesso non si parte da zero: si prende una rete già addestrata su milioni di immagini e la si adatta al proprio problema (**transfer learning**), con pochi dati e poco calcolo.', 'Often you do not start from scratch: you take a network already trained on millions of images and adapt it to your problem (**transfer learning**), with little data and little compute.'),
        ], [
            Q('c1', L('Che cosa impara uno strato convoluzionale?', 'What does a convolutional layer learn?'), [L('Filtri che riconoscono motivi visivi', 'Filters recognising visual patterns'), L('Le parole di una frase', 'The words of a sentence'), L('Le tabelle di un database', 'Database tables'), L('Il colore dello schermo', 'The screen colour')], 0, L('Bordi, forme, oggetti.', 'Edges, shapes, objects.')),
            Q('c2', L('Che cosa fa `MaxPool2d(2)`?', 'What does `MaxPool2d(2)` do?'), [L('Dimezza larghezza e altezza', 'Halves width and height'), L('Raddoppia l’immagine', 'Doubles the image'), L('Aggiunge colori', 'Adds colours'), L('Ruota l’immagine', 'Rotates the image')], 0, L('Tiene il valore massimo di ogni blocco.', 'Keeps the max of each block.')),
            Q('c3', L('Che cos’è il transfer learning?', 'What is transfer learning?'), [L('Adattare una rete già addestrata', 'Adapting an already trained network'), L('Copiare dati da un server', 'Copying data from a server'), L('Tradurre un modello', 'Translating a model'), L('Comprimere le immagini', 'Compressing images')], 0, L('Pochi dati, buoni risultati.', 'Little data, good results.')),
        ], task('python', L('Aggiungi a `strati` uno `nn.Conv2d` da 3 a 8 canali con `kernel_size=3`, poi `nn.ReLU()` e `nn.MaxPool2d(2)`.', 'Add to `strati` an `nn.Conv2d` from 3 to 8 channels with `kernel_size=3`, then `nn.ReLU()` and `nn.MaxPool2d(2)`.'), "strati = nn.Sequential(\n    \n)", [re('nn\.Conv2d\s*\(\s*3\s*,\s*8\s*,\s*kernel_size\s*=\s*3', L('La convoluzione', 'The convolution')), re('nn\.Conv2d[^\n]*\n?\s*nn\.ReLU\s*\(\s*\)\s*,\s*nn\.MaxPool2d\s*\(\s*2\s*\)', L('Attivazione e pooling dopo', 'Activation and pooling after'))], '', "strati = nn.Sequential(\n    nn.Conv2d(3, 8, kernel_size=3),\n    nn.ReLU(),\n    nn.MaxPool2d(2),\n)"), [
            dMatch(pMatch(), [['Conv2d', L('Filtri sull’immagine', 'Filters over the image')], ['MaxPool2d', L('Riduce la dimensione', 'Shrinks the size')], ['Flatten', L('Da griglia a vettore', 'From grid to vector')], [L('Transfer learning', 'Transfer learning'), L('Riusa una rete addestrata', 'Reuses a trained network')]], L('I mattoni delle CNN.', 'CNN building blocks.')),
            dFlash(pFlash(), [[L('Le CNN sono pensate per le immagini', 'CNNs are designed for images'), true], [L('Il pooling aumenta la risoluzione', 'Pooling increases resolution'), false], [L('Un’immagine RGB ha 3 canali', 'An RGB image has 3 channels'), true], [L('Il transfer learning richiede sempre milioni di immagini tue', 'Transfer learning always needs millions of your own images'), false]], L('Vedere con i filtri.', 'Seeing through filters.')),
        ]),
        lesson('regularize', L('Generalizzare meglio', 'Generalising better'), [
            P('Le reti neurali hanno tantissimi parametri e vanno facilmente in **overfitting**. Le difese principali: **dropout** (spegne a caso dei neuroni durante l’addestramento), **data augmentation** (ruota, ritaglia, cambia luce alle immagini), **early stopping** (ti fermi quando l’errore di validazione smette di scendere).', 'Neural networks have huge numbers of parameters and overfit easily. Main defences: **dropout** (randomly switches off neurons during training), **data augmentation** (rotate, crop, relight images), **early stopping** (stop when validation error stops falling).'),
            C('rete = nn.Sequential(nn.Linear(100, 64), nn.ReLU(), nn.Dropout(0.3), nn.Linear(64, 2))

rete.train()            # dropout attivo
# … addestramento …
rete.eval()             # dropout spento per valutare
with torch.no_grad():   # niente gradienti: più veloce
    previsti = rete(X_val)', 'python'),
            P('Ricorda sempre `model.eval()` e `torch.no_grad()` quando valuti o usi il modello: altrimenti dropout e gradienti alterano risultati e prestazioni.', 'Always remember `model.eval()` and `torch.no_grad()` when you evaluate or use the model: otherwise dropout and gradients skew results and performance.'),
        ], [
            Q('r1', L('Che cosa fa il dropout?', 'What does dropout do?'), [L('Spegne a caso dei neuroni in addestramento', 'Randomly switches off neurons in training'), L('Cancella dati', 'Deletes data'), L('Ferma la rete', 'Stops the network'), L('Aggiunge strati', 'Adds layers')], 0, L('La rete non dipende da un solo neurone.', 'The network does not rely on a single neuron.')),
            Q('r2', L('Che cos’è l’early stopping?', 'What is early stopping?'), [L('Fermarsi quando la validazione non migliora più', 'Stopping when validation stops improving'), L('Fermarsi alla prima epoca', 'Stopping at the first epoch'), L('Spegnere il computer', 'Turning off the computer'), L('Un errore di PyTorch', 'A PyTorch error')], 0, L('Prima di imparare il rumore.', 'Before learning the noise.')),
            Q('r3', L('Che cosa fa `rete.eval()`?', 'What does `rete.eval()` do?'), [L('Mette la rete in modalità valutazione', 'Puts the network in evaluation mode'), L('Valuta il codice Python', 'Evaluates Python code'), L('Addestra la rete', 'Trains the network'), L('Cancella i pesi', 'Deletes the weights')], 0, L('Il dropout si spegne.', 'Dropout turns off.')),
        ], task('python', L('Prima di prevedere su `X_val`, metti `rete` in modalità valutazione e usa `with torch.no_grad():`.', 'Before predicting on `X_val`, put `rete` in evaluation mode and use `with torch.no_grad():`.'), "", [re('rete\.eval\s*\(\s*\)', L('Modalità valutazione', 'Evaluation mode')), re('with\s+torch\.no_grad\s*\(\s*\)\s*:\s*\n\s+\w+\s*=\s*rete\s*\(\s*X_val\s*\)', L('Prevedi senza gradienti', 'You predict without gradients'))], '', "rete.eval()\nwith torch.no_grad():\n    previsti = rete(X_val)"), [
            dMatch(pMatch(), [['Dropout', L('Spegne neuroni a caso', 'Randomly drops neurons')], ['Data augmentation', L('Varia gli esempi', 'Varies the examples')], ['Early stopping', L('Si ferma al momento giusto', 'Stops at the right time')], ['eval()', L('Modalità valutazione', 'Evaluation mode')], ['no_grad()', L('Niente gradienti', 'No gradients')]], L('Contro l’overfitting.', 'Against overfitting.')),
            dBug(pBug(), ['rete.train()', 'with torch.no_grad():', '    previsti = rete(X_test)', 'print(accuratezza(previsti, y_test))'], 0, 'rete.eval()', L('Per valutare serve `eval()`, non `train()`.', 'Evaluating needs `eval()`, not `train()`.')),
        ]),
        lesson('data', L('Dataset e DataLoader', 'Datasets and DataLoaders'), [
            P('In PyTorch i dati passano per due classi. Un **Dataset** sa quanti esempi ci sono (`__len__`) e restituisce l’esempio numero *i* (`__getitem__`). Un **DataLoader** li raggruppa in **batch**, li mescola a ogni epoca e può caricarli in parallelo.', 'In PyTorch data flows through two classes. A **Dataset** knows how many examples there are (`__len__`) and returns example number *i* (`__getitem__`). A **DataLoader** groups them into **batches**, shuffles them every epoch and can load them in parallel.'),
            C('import torch
from torch.utils.data import Dataset, DataLoader

class Clienti(Dataset):
    def __init__(self, X, y):
        self.X = torch.tensor(X, dtype=torch.float32)
        self.y = torch.tensor(y, dtype=torch.long)

    def __len__(self):
        return len(self.y)

    def __getitem__(self, i):
        return self.X[i], self.y[i]

caricatore = DataLoader(Clienti(X, y), batch_size=32, shuffle=True)', 'python'),
            P('Mescola (`shuffle=True`) i dati di addestramento, non quelli di validazione. Il **batch** è un compromesso: più grande sfrutta meglio la GPU ma occupa più memoria. Per immagini e testi il Dataset può leggere i file dal disco uno alla volta, così non serve tenerli tutti in memoria.', 'Shuffle (`shuffle=True`) the training data, not the validation data. The **batch** is a trade-off: bigger uses the GPU better but takes more memory. For images and text the Dataset can read files from disk one at a time, so you need not hold them all in memory.'),
        ], [
            Q('d1', L('Che cosa deve restituire `__getitem__`?', 'What must `__getitem__` return?'), [L('Il numero totale di esempi', 'The total number of examples'), L('Un batch già pronto di 32 esempi', 'A ready batch of 32 examples'), L('L’esempio numero i, di solito input ed etichetta', 'Example number i, usually input and label'), L('I pesi della rete', 'The network’s weights')], 2, L('`__len__` dà il totale.', '`__len__` gives the total.')),
            Q('d2', L('Che cosa fa `shuffle=True` nel DataLoader?', 'What does `shuffle=True` do in the DataLoader?'), [L('Mescola l’ordine degli esempi a ogni epoca', 'It shuffles the order of examples every epoch'), L('Cambia le etichette a caso', 'It changes labels at random'), L('Divide i dati in train e test', 'It splits data into train and test'), L('Raddoppia il numero di esempi', 'It doubles the number of examples')], 0, L('Così la rete non impara l’ordine.', 'So the network does not learn the order.')),
            Q('d3', L('Che cosa comporta un batch più grande?', 'What does a bigger batch mean?'), [L('Meno memoria usata sulla GPU', 'Less GPU memory used'), L('Più memoria usata ma GPU sfruttata meglio', 'More memory used but the GPU used better'), L('Nessuna differenza in nessun caso', 'No difference in any case'), L('Un modello con più strati', 'A model with more layers')], 1, L('Un compromesso.', 'A trade-off.')),
        ], task('python', L('Crea `caricatore` con `DataLoader` sul dataset `dati`, con `batch_size=64` e `shuffle=True`.', 'Create `caricatore` with `DataLoader` over the dataset `dati`, with `batch_size=64` and `shuffle=True`.'), "from torch.utils.data import DataLoader", [re('caricatore\s*=\s*DataLoader\s*\(\s*dati', L('Il DataLoader', 'The DataLoader')), re('batch_size\s*=\s*64', L('Batch da 64', 'Batches of 64')), re('shuffle\s*=\s*True', L('Dati mescolati', 'Shuffled data'))], '', "caricatore = DataLoader(dati, batch_size=64, shuffle=True)"), [
            dBlanks(pFill(), "class Dati(Dataset):\n    def ___(self):\n        return len(self.y)\n    def ___(self, i):\n        return self.X[i], self.y[i]", ['__len__', '__getitem__'], ['__init__', '__call__', 'forward'], L('I due metodi di ogni Dataset.', 'The two methods of every Dataset.')),
            dMatch(pMatch(), [['Dataset', L('Dà un esempio alla volta', 'Gives one example at a time')], ['DataLoader', L('Raggruppa in batch', 'Groups into batches')], ['batch_size', L('Esempi per batch', 'Examples per batch')], ['shuffle', L('Mescola a ogni epoca', 'Shuffles every epoch')]], L('Come arrivano i dati alla rete.', 'How data reaches the network.')),
        ]),
        lesson('transfer', L('Transfer learning e salvataggio', 'Transfer learning and saving'), [
            P('Il **transfer learning** in pratica: prendi una rete già addestrata su milioni di immagini, **congeli** i suoi pesi e sostituisci solo l’ultimo strato con uno nuovo per le tue classi. Con poche centinaia di foto ottieni spesso ottimi risultati.', '**Transfer learning** in practice: take a network already trained on millions of images, **freeze** its weights and replace only the last layer with a new one for your classes. With a few hundred photos you often get great results.'),
            C('import torch
from torch import nn
from torchvision import models

rete = models.resnet18(weights=models.ResNet18_Weights.DEFAULT)
for p in rete.parameters():
    p.requires_grad = False                        # congela la rete
rete.fc = nn.Linear(rete.fc.in_features, 3)        # nuovo strato: 3 classi, addestrabile

ottimizzatore = torch.optim.Adam(rete.fc.parameters(), lr=1e-3)
# … addestramento …
torch.save(rete.state_dict(), "rete.pt")           # salva solo i pesi', 'python'),
            P('Per salvare si usa lo **`state_dict`**, il dizionario con tutti i pesi. Per ricaricarlo ricrei la stessa architettura e chiami `rete.load_state_dict(torch.load("rete.pt"))`. Ricorda poi `rete.eval()` prima di usarla per le previsioni.', 'To save you use the **`state_dict`**, the dictionary with all the weights. To reload it you recreate the same architecture and call `rete.load_state_dict(torch.load("rete.pt"))`. Then remember `rete.eval()` before using it for predictions.'),
        ], [
            Q('f1', L('Che cosa fa `p.requires_grad = False` su tutti i parametri?', 'What does `p.requires_grad = False` on all parameters do?'), [L('Cancella i pesi della rete', 'It deletes the network’s weights'), L('Sposta la rete sulla GPU', 'It moves the network to the GPU'), L('Mette la rete in modalità valutazione', 'It puts the network in evaluation mode'), L('Congela i pesi: non vengono aggiornati', 'It freezes the weights: they are not updated')], 3, L('Niente gradienti, niente aggiornamenti.', 'No gradients, no updates.')),
            Q('f2', L('Perché si sostituisce l’ultimo strato?', 'Why replace the last layer?'), [L('Per avere tante uscite quante sono le tue classi', 'To have as many outputs as your classes'), L('Per rendere la rete più veloce', 'To make the network faster'), L('Perché l’ultimo strato è sempre rotto', 'Because the last layer is always broken'), L('Per ridurre la dimensione delle immagini', 'To shrink the images')], 0, L('Le classi originali erano altre.', 'The original classes were different.')),
            Q('f3', L('Che cosa contiene lo `state_dict`?', 'What does the `state_dict` contain?'), [L('Il codice Python della rete', 'The network’s Python code'), L('I pesi della rete, strato per strato', 'The network’s weights, layer by layer'), L('Le immagini di addestramento', 'The training images'), L('La storia delle loss', 'The history of the losses')], 1, L('L’architettura la ricrei tu.', 'You recreate the architecture.')),
        ], task('python', L('Sostituisci `rete.fc` con `nn.Linear(rete.fc.in_features, 5)` e salva i pesi con `torch.save(rete.state_dict(), "rete.pt")`.', 'Replace `rete.fc` with `nn.Linear(rete.fc.in_features, 5)` and save the weights with `torch.save(rete.state_dict(), "rete.pt")`.'), "import torch\nfrom torch import nn\nfrom torchvision import models\nrete = models.resnet18(weights=models.ResNet18_Weights.DEFAULT)", [re('rete\.fc\s*=\s*nn\.Linear\s*\(\s*rete\.fc\.in_features\s*,\s*5\s*\)', L('Nuovo strato per 5 classi', 'New layer for 5 classes')), re('torch\.save\s*\(\s*rete\.state_dict\s*\(\s*\)\s*,\s*["\']rete\.pt["\']\s*\)', L('Salvi i pesi', 'You save the weights'))], '', "rete.fc = nn.Linear(rete.fc.in_features, 5)\ntorch.save(rete.state_dict(), \"rete.pt\")"), [
            dOrder(pSort(), ['rete = models.resnet18(weights=models.ResNet18_Weights.DEFAULT)', 'for p in rete.parameters(): p.requires_grad = False', 'rete.fc = nn.Linear(rete.fc.in_features, 3)', 'ottimizzatore = torch.optim.Adam(rete.fc.parameters(), lr=1e-3)', 'torch.save(rete.state_dict(), "rete.pt")'], L('Carica, congela, sostituisci, addestra, salva.', 'Load, freeze, replace, train, save.')),
            dBug(pBug(), ['rete = models.resnet18()', 'rete.fc = nn.Linear(rete.fc.in_features, 3)', 'rete.load_state_dict("rete.pt")', 'rete.eval()'], 2, 'rete.load_state_dict(torch.load("rete.pt"))', L('Prima si legge il file con `torch.load`.', 'First read the file with `torch.load`.')),
        ]),
        lesson('sequences', L('Sequenze: RNN e LSTM', 'Sequences: RNNs and LSTMs'), [
            P('Testi, audio e serie temporali sono **sequenze**: conta l’ordine. Una **rete ricorrente** (RNN) le legge un elemento alla volta e porta avanti uno **stato nascosto**, una specie di memoria di ciò che ha visto. La **LSTM** è una RNN con “porte” che decidono cosa ricordare e cosa dimenticare.', 'Text, audio and time series are **sequences**: order matters. A **recurrent network** (RNN) reads them one element at a time and carries a **hidden state**, a kind of memory of what it has seen. The **LSTM** is an RNN with “gates” deciding what to remember and what to forget.'),
            C('import torch
from torch import nn

lstm = nn.LSTM(input_size=100, hidden_size=64, batch_first=True)
x = torch.randn(8, 20, 100)       # 8 frasi, 20 token, 100 numeri per token
uscite, (h, c) = lstm(x)
print(uscite.shape)               # torch.Size([8, 20, 64]): un’uscita per token
print(h.shape)                    # torch.Size([1, 8, 64]): lo stato finale', 'python'),
            P('Limiti: leggono un passo dopo l’altro, quindi non si parallelizzano bene sulle GPU, e su sequenze lunghe faticano a ricordare ciò che stava all’inizio. Il meccanismo di **attention** e il **transformer** hanno risolto entrambi i problemi, ed è per questo che oggi dominano il linguaggio.', 'Limits: they read one step after another, so they do not parallelise well on GPUs, and on long sequences they struggle to remember what came at the start. The **attention** mechanism and the **transformer** solved both problems, which is why they dominate language today.'),
        ], [
            Q('q1', L('Che cos’è lo stato nascosto di una RNN?', 'What is an RNN’s hidden state?'), [L('Un parametro segreto non addestrabile', 'A secret, untrainable parameter'), L('Il file in cui si salva la rete', 'The file the network is saved to'), L('L’etichetta della sequenza', 'The sequence’s label'), L('Una memoria di ciò che ha letto finora', 'A memory of what it has read so far')], 3, L('Passa da un passo al successivo.', 'It passes from one step to the next.')),
            Q('q2', L('Che cosa aggiungono le porte di una LSTM?', 'What do an LSTM’s gates add?'), [L('Decidono cosa ricordare e cosa dimenticare', 'They decide what to remember and what to forget'), L('Permettono di leggere immagini a colori', 'They allow reading colour images'), L('Rendono la rete non addestrabile', 'They make the network untrainable'), L('Eliminano la necessità dei dati', 'They remove the need for data')], 0, L('Memoria più lunga e stabile.', 'Longer, steadier memory.')),
            Q('q3', L('Perché i transformer hanno superato le RNN nel linguaggio?', 'Why did transformers overtake RNNs in language?'), [L('Usano meno parametri in ogni caso', 'They always use fewer parameters'), L('Si parallelizzano meglio e collegano parti lontane del testo', 'They parallelise better and link distant parts of the text'), L('Non hanno bisogno di addestramento', 'They need no training'), L('Funzionano solo su CPU', 'They only run on CPUs')], 1, L('L’attention guarda tutto il contesto insieme.', 'Attention looks at the whole context at once.')),
        ], task('python', L('Crea `lstm = nn.LSTM(input_size=50, hidden_size=32, batch_first=True)` e applicala al tensore `x` salvando `uscite, (h, c)`.', 'Create `lstm = nn.LSTM(input_size=50, hidden_size=32, batch_first=True)` and apply it to the tensor `x`, storing `uscite, (h, c)`.'), "import torch\nfrom torch import nn\nx = torch.randn(4, 10, 50)", [re('lstm\s*=\s*nn\.LSTM\s*\(\s*input_size\s*=\s*50\s*,\s*hidden_size\s*=\s*32\s*,\s*batch_first\s*=\s*True\s*\)', L('La LSTM', 'The LSTM')), re('uscite\s*,\s*\(\s*h\s*,\s*c\s*\)\s*=\s*lstm\s*\(\s*x\s*\)', L('La applichi', 'You apply it'))], '', "lstm = nn.LSTM(input_size=50, hidden_size=32, batch_first=True)\nuscite, (h, c) = lstm(x)"), [
            dMatch(pMatch(), [['RNN', L('Legge un passo alla volta', 'Reads one step at a time')], ['LSTM', L('RNN con porte', 'RNN with gates')], [L('Stato nascosto', 'Hidden state'), L('Memoria della sequenza', 'Memory of the sequence')], ['batch_first', L('Prima dimensione = esempi', 'First dimension = examples')]], L('Reti con memoria.', 'Networks with memory.')),
            dFlash(pFlash(), [[L('Nelle sequenze l’ordine degli elementi conta', 'In sequences the order of elements matters'), true], [L('Le RNN leggono tutta la sequenza in un solo passo', 'RNNs read the whole sequence in one step'), false], [L('Le RNN faticano con le dipendenze molto lunghe', 'RNNs struggle with very long dependencies'), true], [L('Il transformer è un tipo di LSTM', 'The transformer is a kind of LSTM'), false]], L('Dalla memoria all’attenzione.', 'From memory to attention.')),
        ]),
    ],
];
