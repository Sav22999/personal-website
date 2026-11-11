<?php
/** Hugging Face: the Hub and model cards, transformers pipelines, datasets, Spaces with Gradio. */

return [
    'id' => 'huggingface', 'family' => 'llm', 'after' => 'ai-base',
    'title' => L('Hugging Face', 'Hugging Face'), 'short' => L('Hugging Face', 'Hugging Face'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('La piattaforma dei modelli aperti: l’Hub e le schede dei modelli, le pipeline di transformers, i dataset e le demo con Spaces e Gradio.', 'The platform of open models: the Hub and model cards, transformers pipelines, datasets and demos with Spaces and Gradio.'),
    'lessons' => [
        lesson('hub', L('L’Hub e le schede dei modelli', 'The Hub and model cards'), [
            P('**Hugging Face** è la piattaforma di riferimento per l’AI aperta. Il suo **Hub** ospita centinaia di migliaia di **modelli**, **dataset** e app dimostrative (**Spaces**), con versioni gestite come in Git.', '**Hugging Face** is the reference platform for open AI. Its **Hub** hosts hundreds of thousands of **models**, **datasets** and demo apps (**Spaces**), versioned as in Git.'),
            P('Ogni modello ha una **scheda** (model card): a che cosa serve, su quali dati è stato addestrato, lingue supportate, limiti noti, **licenza** e come usarlo. Leggila prima di scegliere: un modello addestrato solo in inglese funzionerà male in italiano, e una licenza può vietare l’uso commerciale.', 'Each model has a **card** (model card): what it is for, which data trained it, supported languages, known limits, **licence** and how to use it. Read it before choosing: a model trained only in English will work poorly in Italian, and a licence may forbid commercial use.'),
        ], [
            Q('h1', L('Che cosa ospita l’Hub di Hugging Face?', 'What does the Hugging Face Hub host?'), [L('Modelli, dataset e Spaces', 'Models, datasets and Spaces'), L('Solo video', 'Only videos'), L('Solo siti web', 'Only websites'), L('Email', 'Email')], 0, L('Con versioni come in Git.', 'Versioned as in Git.')),
            Q('h2', L('Che cosa trovi in una model card?', 'What do you find in a model card?'), [L('Uso, dati, lingue, limiti e licenza', 'Use, data, languages, limits and licence'), L('Solo il prezzo', 'Only the price'), L('Le password', 'Passwords'), L('Niente', 'Nothing')], 0, L('Da leggere prima di usarlo.', 'To read before using it.')),
            Q('h3', L('Perché controllare le lingue supportate?', 'Why check supported languages?'), [L('Un modello solo inglese va male in italiano', 'An English-only model does poorly in Italian'), L('Per curiosità', 'Out of curiosity'), L('Non conta', 'It does not matter'), L('Per il colore', 'For the colour')], 0, L('I dati decidono le capacità.', 'Data decides capabilities.')),
        ], task('text', L('Scrivi tre cose da controllare nella scheda di un modello prima di usarlo in un prodotto italiano, una per riga con `-`.', 'Write three things to check in a model card before using it in an Italian product, one per line with `-`.'), "- \n- \n- ", [re('^-\s*\S.*\n-\s*\S.*\n-\s*\S', L('Tre controlli', 'Three checks')), re('licenz|licen', L('La licenza', 'The licence')), re('lingu|italian|language', L('Le lingue', 'The languages'))], '', "- la licenza permette l’uso commerciale?\n- supporta la lingua italiana?\n- quali limiti e bias sono dichiarati?"), [
            dMatch(pMatch(), [['Hub', L('Archivio di modelli e dati', 'Archive of models and data')], ['Model card', L('Scheda del modello', 'Model description')], ['Dataset', L('Dati per addestrare e valutare', 'Data to train and evaluate')], ['Space', L('App dimostrativa', 'Demo app')], [L('Licenza', 'Licence'), L('Cosa è permesso', 'What is allowed')]], L('L’ecosistema Hugging Face.', 'The Hugging Face ecosystem.')),
            dFlash(pFlash(), [[L('Le model card indicano la licenza', 'Model cards state the licence'), true], [L('Tutti i modelli dell’Hub si possono usare commercialmente', 'All Hub models can be used commercially'), false], [L('Sull’Hub ci sono anche dataset', 'The Hub also has datasets'), true], [L('Gli Spaces sono solo immagini statiche', 'Spaces are only static images'), false]], L('Scegliere con informazioni.', 'Choosing with information.')),
        ]),
        lesson('pipeline', L('Le pipeline di transformers', 'transformers pipelines'), [
            P('La libreria **transformers** carica i modelli dell’Hub con poche righe. Il modo più semplice è la **pipeline**: scegli un compito (e opzionalmente un modello) e passi i dati. Il modello viene scaricato la prima volta e poi resta in cache.', 'The **transformers** library loads Hub models in a few lines. The easiest way is the **pipeline**: you pick a task (and optionally a model) and pass the data. The model is downloaded the first time and then cached.'),
            C('from transformers import pipeline

sentimento = pipeline("sentiment-analysis",
                      model="nlptown/bert-base-multilingual-uncased-sentiment")
print(sentimento("Il corso è chiarissimo, lo consiglio!"))
# [{\'label\': \'5 stars\', \'score\': 0.8}]

riassunto = pipeline("summarization")
traduttore = pipeline("translation", model="Helsinki-NLP/opus-mt-it-en")
print(traduttore("Oggi piove a Trieste.")[0]["translation_text"])', 'python'),
            P('Compiti comuni: classificazione del testo, riconoscimento di entità (nomi, luoghi), domande e risposte, riassunto, traduzione, classificazione di immagini, trascrizione audio. Per modelli grandi serve una GPU; quelli piccoli girano anche su CPU.', 'Common tasks: text classification, entity recognition (names, places), question answering, summarisation, translation, image classification, audio transcription. Big models need a GPU; small ones also run on CPU.'),
        ], [
            Q('p1', L('Che cosa fa una pipeline?', 'What does a pipeline do?'), [L('Usa un modello per un compito in poche righe', 'Uses a model for a task in a few lines'), L('Installa Python', 'Installs Python'), L('Crea un dataset', 'Creates a dataset'), L('Pubblica un sito', 'Publishes a site')], 0, L('Compito + modello + dati.', 'Task + model + data.')),
            Q('p2', L('Dove finisce il modello dopo il primo download?', 'Where does the model go after the first download?'), [L('In una cache locale', 'In a local cache'), L('Viene cancellato', 'It is deleted'), L('Sul cloud di un amico', 'On a friend’s cloud'), L('In un’email', 'In an email')], 0, L('Le volte successive è subito pronto.', 'Next times it is ready at once.')),
            Q('p3', L('Quale compito estrae nomi di persone e luoghi?', 'Which task extracts names of people and places?'), [L('Riconoscimento di entità', 'Entity recognition'), L('Traduzione', 'Translation'), L('Riassunto', 'Summarisation'), L('Classificazione di immagini', 'Image classification')], 0, L('NER.', 'NER.')),
        ], task('python', L('Crea una pipeline `traduttore` per il compito `"translation"` con il modello `"Helsinki-NLP/opus-mt-en-it"` e traduci `"Good morning"`.', 'Create a pipeline `traduttore` for the `"translation"` task with the model `"Helsinki-NLP/opus-mt-en-it"` and translate `"Good morning"`.'), "from transformers import pipeline\n", [re('traduttore\s*=\s*pipeline\s*\(\s*["\']translation["\']\s*,\s*model\s*=\s*["\']Helsinki-NLP/opus-mt-en-it["\']\s*\)', L('La pipeline', 'The pipeline')), re('traduttore\s*\(\s*["\']Good morning["\']\s*\)', L('La traduzione', 'The translation'))], '', "traduttore = pipeline(\"translation\", model=\"Helsinki-NLP/opus-mt-en-it\")\nprint(traduttore(\"Good morning\"))"), [
            dMatch(pMatch(), [['sentiment-analysis', L('Positivo o negativo', 'Positive or negative')], ['summarization', L('Riassunto', 'Summary')], ['translation', L('Traduzione', 'Translation')], ['ner', L('Nomi e luoghi', 'Names and places')], ['automatic-speech-recognition', L('Trascrizione audio', 'Audio transcription')]], L('Compiti delle pipeline.', 'Pipeline tasks.')),
            dBlanks(pFill(), "from transformers import ___\nclf = pipeline(\"sentiment-analysis\")\nprint(___(\"Ottimo servizio!\"))", ['pipeline', 'clf'], ['model', 'transformers', 'print'], L('Importare e usare la pipeline.', 'Importing and using the pipeline.')),
        ]),
        lesson('datasets', L('Dataset', 'Datasets'), [
            P('La libreria **datasets** scarica e prepara dataset dell’Hub in modo efficiente, anche molto grandi. Un dataset ha di solito parti separate (**split**) per addestramento, validazione e test.', 'The **datasets** library downloads and prepares Hub datasets efficiently, even very large ones. A dataset usually has separate parts (**splits**) for training, validation and test.'),
            C('from datasets import load_dataset

recensioni = load_dataset("imdb")
print(recensioni)                     # split train e test
print(recensioni["train"][0])         # {"text": "...", "label": 1}

piccolo = recensioni["train"].shuffle(seed=42).select(range(1000))
positivi = piccolo.filter(lambda r: r["label"] == 1)', 'python'),
            P('Come per i modelli, anche i dataset hanno una scheda con origine, licenza e possibili problemi (dati personali, bias). Puoi caricare i tuoi dati con `load_dataset("csv", data_files="miei.csv")`.', 'As with models, datasets have a card with origin, licence and possible issues (personal data, bias). You can load your own data with `load_dataset("csv", data_files="mine.csv")`.'),
        ], [
            Q('d1', L('Che cos’è uno split?', 'What is a split?'), [L('Una parte del dataset (train, test…)', 'A part of the dataset (train, test…)'), L('Un errore', 'An error'), L('Un modello', 'A model'), L('Un tipo di licenza', 'A licence type')], 0, L('Per addestrare e valutare.', 'To train and evaluate.')),
            Q('d2', L('Come carichi un tuo file CSV?', 'How do you load your own CSV file?'), ['load_dataset("csv", data_files="miei.csv")', 'open("miei.csv").train()', 'pipeline("csv")', 'Dataset.csv()'], 0, L('Stessa libreria.', 'Same library.')),
            Q('d3', L('Che cosa fa `.select(range(1000))`?', 'What does `.select(range(1000))` do?'), [L('Tiene i primi 1000 esempi', 'Keeps the first 1000 examples'), L('Cancella 1000 esempi', 'Deletes 1000 examples'), L('Crea 1000 copie', 'Creates 1000 copies'), L('Ordina', 'Sorts')], 0, L('Utile per provare in fretta.', 'Handy for quick tries.')),
        ], task('python', L('Carica il dataset `"imdb"` in `dati` e stampa il primo esempio dello split `"test"`.', 'Load the `"imdb"` dataset into `dati` and print the first example of the `"test"` split.'), "from datasets import load_dataset\n", [re('dati\s*=\s*load_dataset\s*\(\s*["\']imdb["\']\s*\)', L('Carichi il dataset', 'You load the dataset')), re('print\s*\(\s*dati\s*\[\s*["\']test["\']\s*\]\s*\[\s*0\s*\]\s*\)', L('Stampi il primo esempio di test', 'You print the first test example'))], '', "dati = load_dataset(\"imdb\")\nprint(dati[\"test\"][0])"), [
            dMatch(pMatch(), [['load_dataset', L('Scarica un dataset', 'Downloads a dataset')], ['train / test', L('Gli split', 'The splits')], ['shuffle', L('Mescola', 'Shuffles')], ['filter', L('Tiene alcune righe', 'Keeps some rows')]], L('Lavorare con i dataset.', 'Working with datasets.')),
            dFlash(pFlash(), [[L('Un dataset può avere split train e test', 'A dataset can have train and test splits'), true], [L('I dataset non hanno licenza', 'Datasets have no licence'), false], [L('Si possono caricare file CSV propri', 'You can load your own CSV files'), true], [L('load_dataset funziona solo con immagini', 'load_dataset only works with images'), false]], L('Dati pronti all’uso.', 'Ready-to-use data.')),
        ]),
        lesson('spaces', L('Spaces e Gradio', 'Spaces and Gradio'), [
            P('Uno **Space** è una piccola app pubblicata sull’Hub per provare un modello dal browser. Si costruisce spesso con **Gradio**, una libreria che crea un’interfaccia web da una funzione Python in poche righe.', 'A **Space** is a small app published on the Hub to try a model from the browser. It is often built with **Gradio**, a library that creates a web interface from a Python function in a few lines.'),
            C('import gradio as gr
from transformers import pipeline

traduttore = pipeline("translation", model="Helsinki-NLP/opus-mt-it-en")

def traduci(testo):
    return traduttore(testo)[0]["translation_text"]

gr.Interface(fn=traduci, inputs="text", outputs="text", title="Italiano → Inglese").launch()', 'python'),
            P('Per pubblicarlo crei uno Space, carichi `app.py` e un `requirements.txt` con le librerie: Hugging Face lo costruisce e lo mette online. È un ottimo modo per mostrare un progetto a colleghi o in un portfolio.', 'To publish it you create a Space, upload `app.py` and a `requirements.txt` with the libraries: Hugging Face builds it and puts it online. It is a great way to show a project to colleagues or in a portfolio.'),
        ], [
            Q('s1', L('Che cosa fa Gradio?', 'What does Gradio do?'), [L('Crea un’interfaccia web da una funzione Python', 'Creates a web interface from a Python function'), L('Addestra modelli', 'Trains models'), L('Gestisce database', 'Manages databases'), L('Invia email', 'Sends email')], 0, L('Poche righe.', 'A few lines.')),
            Q('s2', L('Quali file servono per pubblicare uno Space Gradio?', 'Which files are needed to publish a Gradio Space?'), [L('app.py e requirements.txt', 'app.py and requirements.txt'), L('index.php', 'index.php'), L('Solo un’immagine', 'Only an image'), L('Nessuno', 'None')], 0, L('Il resto lo fa la piattaforma.', 'The platform does the rest.')),
            Q('s3', L('A che cosa servono gli Spaces?', 'What are Spaces for?'), [L('Provare e mostrare modelli dal browser', 'Trying and showing models from the browser'), L('Salvare password', 'Storing passwords'), L('Pagare', 'Paying'), L('Scrivere email', 'Writing email')], 0, L('Ottimi per un portfolio.', 'Great for a portfolio.')),
        ], task('python', L('Crea con Gradio un’interfaccia per la funzione `maiuscolo(testo)` con input e output `"text"`, poi chiama `.launch()`.', 'Create with Gradio an interface for the function `maiuscolo(testo)` with `"text"` input and output, then call `.launch()`.'), "import gradio as gr\n\ndef maiuscolo(testo):\n    return testo.upper()\n", [re('gr\.Interface\s*\(\s*fn\s*=\s*maiuscolo\s*,\s*inputs\s*=\s*["\']text["\']\s*,\s*outputs\s*=\s*["\']text["\']', L('L’interfaccia', 'The interface')), re('\.launch\s*\(\s*\)', L('La avvii', 'You launch it'))], '', 'gr.Interface(fn=maiuscolo, inputs="text", outputs="text").launch()'), [
            dOrder(pSort(), ['import gradio as gr', 'def saluta(nome):', '    return f"Ciao {nome}!"', 'gr.Interface(fn=saluta, inputs="text", outputs="text").launch()'], L('Importa, definisci, crea l’interfaccia.', 'Import, define, create the interface.')),
            dMatch(pMatch(), [['gr.Interface', L('Crea l’interfaccia', 'Creates the interface')], ['fn', L('La funzione da chiamare', 'The function to call')], ['launch()', L('Avvia l’app', 'Starts the app')], ['requirements.txt', L('Librerie dello Space', 'The Space’s libraries')]], L('Una demo in cinque minuti.', 'A demo in five minutes.')),
        ]),
    ],
];
