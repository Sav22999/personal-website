<?php
/** Deep learning basics with PyTorch: neurons and layers, tensors, the training loop, CNNs for images, overfitting tricks. */

return [
    'id' => 'dl-base', 'family' => 'dl', 'after' => 'ml-base',
    'title' => L('Deep learning', 'Deep Learning'), 'short' => L('DL base', 'DL Basics'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Le reti neurali con PyTorch: neuroni e strati, tensori, il ciclo di addestramento, reti convoluzionali per le immagini, dropout e GPU.', 'Neural networks with PyTorch: neurons and layers, tensors, the training loop, convolutional networks for images, dropout and GPUs.'),
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
    ],
];
