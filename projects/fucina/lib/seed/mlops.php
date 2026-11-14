<?php
/** MLOps and LLMOps: experiment tracking, serving a model behind an API, monitoring and drift, evaluating LLM apps, costs and latency. */

return [
    'id' => 'mlops', 'family' => 'ml', 'after' => 'ml-intermediate',
    'title' => L('MLOps e LLMOps', 'MLOps and LLMOps'), 'short' => L('MLOps', 'MLOps'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Portare i modelli in produzione: tracciare gli esperimenti, servire un modello via API, monitorare e riconoscere il drift, valutare le app LLM, costi e latenza.', 'Taking models to production: tracking experiments, serving a model via an API, monitoring and spotting drift, evaluating LLM apps, costs and latency.'),
    'lessons' => [
        lesson('tracking', L('Tracciare gli esperimenti', 'Tracking experiments'), [
            P('Dopo dieci prove nessuno ricorda quale combinazione di dati, codice e iperparametri ha dato il modello migliore. Per questo si **tracciano** gli esperimenti: parametri, metriche, versione del codice e dei dati, e il modello prodotto.', 'After ten attempts nobody remembers which mix of data, code and hyperparameters gave the best model. That is why you **track** experiments: parameters, metrics, code and data version, and the resulting model.'),
            C('import mlflow

with mlflow.start_run():
    mlflow.log_param("max_depth", 6)
    mlflow.log_param("dati", "clienti_2026_10.parquet")
    modello.fit(X_train, y_train)
    mlflow.log_metric("f1", f1_score(y_test, modello.predict(X_test)))
    mlflow.sklearn.log_model(modello, "modello")', 'python'),
            P('Strumenti come MLflow o Weights & Biases salvano tutto e mostrano i confronti. La **riproducibilità** (stessi dati + stesso codice = stesso modello) è la base per fidarsi di un risultato.', 'Tools such as MLflow or Weights & Biases store everything and show comparisons. **Reproducibility** (same data + same code = same model) is the basis for trusting a result.'),
        ], [
            Q('k1', L('Che cosa si traccia in un esperimento?', 'What do you track in an experiment?'), [L('Parametri, metriche, versioni e modello', 'Parameters, metrics, versions and model'), L('Solo il nome del file', 'Only the file name'), L('L’ora di pranzo', 'Lunch time'), L('Niente', 'Nothing')], 0, L('Tutto quello che serve a ripeterlo.', 'Everything needed to repeat it.')),
            Q('k2', L('Che cos’è la riproducibilità?', 'What is reproducibility?'), [L('Stessi dati e codice danno lo stesso modello', 'Same data and code give the same model'), L('Copiare un modello altrui', 'Copying someone’s model'), L('Stampare i risultati', 'Printing results'), L('Ridurre il dataset', 'Shrinking the dataset')], 0, L('La base della fiducia.', 'The basis of trust.')),
            Q('k3', L('Che cosa fa `mlflow.log_metric`?', 'What does `mlflow.log_metric` do?'), [L('Salva una metrica dell’esperimento', 'Saves an experiment metric'), L('Addestra il modello', 'Trains the model'), L('Cancella il run', 'Deletes the run'), L('Scarica dati', 'Downloads data')], 0, L('Per confrontare i run.', 'To compare runs.')),
        ], task('python', L('Dentro `with mlflow.start_run():` registra il parametro `n_estimators` uguale a `300` e la metrica `accuratezza` con la variabile `acc`.', 'Inside `with mlflow.start_run():` log the parameter `n_estimators` equal to `300` and the metric `accuratezza` with the variable `acc`.'), "import mlflow\nwith mlflow.start_run():\n    ", [re('mlflow\.log_param\s*\(\s*["\']n_estimators["\']\s*,\s*300\s*\)', L('Il parametro', 'The parameter')), re('mlflow\.log_metric\s*\(\s*["\']accuratezza["\']\s*,\s*acc\s*\)', L('La metrica', 'The metric'))], '', "mlflow.log_param(\"n_estimators\", 300)\n    mlflow.log_metric(\"accuratezza\", acc)"), [
            dMatch(pMatch(), [['log_param', L('Salva un parametro', 'Saves a parameter')], ['log_metric', L('Salva una metrica', 'Saves a metric')], ['log_model', L('Salva il modello', 'Saves the model')], ['start_run', L('Apre un esperimento', 'Opens an experiment')]], L('Il diario degli esperimenti.', 'The experiment diary.')),
            dFlash(pFlash(), [[L('Tracciare la versione dei dati è importante', 'Tracking the data version matters'), true], [L('Basta ricordare a memoria i risultati', 'Remembering results is enough'), false], [L('MLflow permette di confrontare i run', 'MLflow lets you compare runs'), true], [L('La riproducibilità è inutile', 'Reproducibility is useless'), false]], L('Se non è tracciato, non è successo.', 'If it is not tracked, it did not happen.')),
        ]),
        lesson('serving', L('Servire un modello', 'Serving a model'), [
            P('Per usare un modello da un’app lo si mette dietro una **API**: un piccolo servizio che riceve i dati, li valida, chiama il modello e restituisce la previsione. In Python si usa spesso **FastAPI**; poi lo si impacchetta in un container **Docker**.', 'To use a model from an app you put it behind an **API**: a small service that receives data, validates it, calls the model and returns the prediction. In Python **FastAPI** is common; then you package it in a **Docker** container.'),
            C('from fastapi import FastAPI
from pydantic import BaseModel
import joblib

app = FastAPI()
modello = joblib.load("modello.joblib")      # caricato una volta all’avvio

class Cliente(BaseModel):
    eta: int
    reddito: float

@app.post("/previsione")
def previsione(c: Cliente):
    p = modello.predict_proba([[c.eta, c.reddito]])[0][1]
    return {"probabilita_acquisto": round(float(p), 3)}', 'python'),
            P('Carica il modello **all’avvio**, non a ogni richiesta. **Pydantic** valida gli input: se manca un campo o il tipo è sbagliato, risponde con un errore chiaro invece di far sbagliare il modello.', 'Load the model **at startup**, not on every request. **Pydantic** validates the inputs: if a field is missing or the type is wrong, it answers with a clear error instead of letting the model misbehave.'),
        ], [
            Q('v1', L('Quando va caricato il modello?', 'When should the model be loaded?'), [L('Una volta all’avvio', 'Once at startup'), L('A ogni richiesta', 'On every request'), L('Mai', 'Never'), L('Ogni ora', 'Every hour')], 0, L('Caricare è lento.', 'Loading is slow.')),
            Q('v2', L('A che cosa serve Pydantic qui?', 'What is Pydantic for here?'), [L('Validare gli input', 'Validating inputs'), L('Addestrare', 'Training'), L('Disegnare grafici', 'Drawing charts'), L('Comprimere', 'Compressing')], 0, L('Tipi e campi obbligatori.', 'Types and required fields.')),
            Q('v3', L('A che cosa serve Docker?', 'What is Docker for?'), [L('Impacchettare il servizio con le sue dipendenze', 'Packaging the service with its dependencies'), L('Addestrare più veloce', 'Training faster'), L('Scrivere i test', 'Writing tests'), L('Gestire le password', 'Managing passwords')], 0, L('Gira uguale ovunque.', 'It runs the same everywhere.')),
        ], task('python', L('Definisci con Pydantic la classe `Casa` con `metri: float` e `stanze: int`, e l’endpoint `@app.post("/prezzo")` che la riceve.', 'Define with Pydantic the class `Casa` with `metri: float` and `stanze: int`, and the endpoint `@app.post("/prezzo")` receiving it.'), "from fastapi import FastAPI\nfrom pydantic import BaseModel\napp = FastAPI()\n", [re('class\s+Casa\s*\(\s*BaseModel\s*\)\s*:\s*\n\s+metri\s*:\s*float\s*\n\s+stanze\s*:\s*int', L('Il modello dei dati', 'The data model')), re('@app\.post\s*\(\s*["\']/prezzo["\']\s*\)\s*\n\s*def\s+\w+\s*\(\s*\w+\s*:\s*Casa\s*\)', L('L’endpoint riceve una Casa', 'The endpoint receives a Casa'))], '', "class Casa(BaseModel):\n    metri: float\n    stanze: int\n\n@app.post(\"/prezzo\")\ndef prezzo(c: Casa):\n    return {\"prezzo\": float(modello.predict([[c.metri, c.stanze]])[0])}"), [
            dOrder(pSort(), ['app = FastAPI()', 'modello = joblib.load("modello.joblib")', 'class Cliente(BaseModel): …', '@app.post("/previsione")', 'def previsione(c: Cliente): …'], L('Servizio, modello, dati, endpoint.', 'Service, model, data, endpoint.')),
            dBug(pBug(), ['@app.post("/previsione")', 'def previsione(c: Cliente):', '    modello = joblib.load("modello.joblib")', '    return {"p": modello.predict([[c.eta, c.reddito]])[0]}'], 2, '    # il modello è già caricato all’avvio', L('Caricare il modello a ogni richiesta è lentissimo.', 'Loading the model on every request is very slow.')),
        ]),
        lesson('monitoring', L('Monitoraggio e drift', 'Monitoring and drift'), [
            P('Un modello in produzione invecchia: il mondo cambia e i dati nuovi non somigliano più a quelli di addestramento. Questo è il **drift**: dei dati in ingresso (*data drift*, per esempio clienti più giovani) o della relazione tra dati ed etichetta (*concept drift*, per esempio nuove truffe).', 'A model in production ages: the world changes and new data no longer resembles the training data. This is **drift**: of the input data (*data drift*, e.g. younger customers) or of the relation between data and label (*concept drift*, e.g. new fraud patterns).'),
            C('import numpy as np
from scipy.stats import ks_2samp

stat, p = ks_2samp(eta_addestramento, eta_ultima_settimana)
if p < 0.01:
    allerta("La distribuzione dell’età è cambiata: controlla il modello")', 'python'),
            P('Si monitorano: distribuzioni degli input, distribuzione delle previsioni, metriche reali quando arrivano le etichette vere, latenza ed errori. Quando la qualità scende si **riaddestra** con dati recenti, possibilmente con una pipeline automatica.', 'You monitor: input distributions, prediction distribution, real metrics when true labels arrive, latency and errors. When quality drops you **retrain** with recent data, ideally with an automated pipeline.'),
        ], [
            Q('d1', L('Che cos’è il data drift?', 'What is data drift?'), [L('I dati in ingresso cambiano rispetto all’addestramento', 'Input data changes compared to training'), L('Un furto di dati', 'A data theft'), L('Un disco pieno', 'A full disk'), L('Un errore di battitura', 'A typo')], 0, L('Il mondo cambia.', 'The world changes.')),
            Q('d2', L('Che cosa si fa quando la qualità scende?', 'What do you do when quality drops?'), [L('Si riaddestra con dati recenti', 'Retrain with recent data'), L('Si ignora', 'Ignore it'), L('Si cancella il modello', 'Delete the model'), L('Si cambia linguaggio', 'Change language')], 0, L('Meglio se automatico.', 'Better if automated.')),
            Q('d3', L('Che cosa verifica il test KS in questo esempio?', 'What does the KS test check in this example?'), [L('Se due distribuzioni sono diverse', 'Whether two distributions differ'), L('La velocità del server', 'Server speed'), L('L’accuratezza', 'Accuracy'), L('Il numero di righe', 'The row count')], 0, L('p piccolo = probabilmente diverse.', 'Small p = probably different.')),
        ], task('python', L('Confronta con `ks_2samp` i redditi `reddito_train` e `reddito_oggi`; se `p < 0.01` chiama `allerta("drift sul reddito")`.', 'Compare with `ks_2samp` the incomes `reddito_train` and `reddito_oggi`; if `p < 0.01` call `allerta("drift sul reddito")`.'), "from scipy.stats import ks_2samp\n", [re('\w+\s*,\s*p\s*=\s*ks_2samp\s*\(\s*reddito_train\s*,\s*reddito_oggi\s*\)', L('Il confronto', 'The comparison')), re('if\s+p\s*<\s*0\.01\s*:\s*\n\s+allerta\s*\(\s*["\']drift sul reddito["\']\s*\)', L('L’allerta', 'The alert'))], '', "stat, p = ks_2samp(reddito_train, reddito_oggi)\nif p < 0.01:\n    allerta(\"drift sul reddito\")"), [
            dMatch(pMatch(), [['Data drift', L('Cambiano gli input', 'Inputs change')], ['Concept drift', L('Cambia la relazione con l’etichetta', 'The relation to the label changes')], [L('Latenza', 'Latency'), L('Tempo di risposta', 'Response time')], [L('Riaddestramento', 'Retraining'), L('Aggiornare con dati recenti', 'Updating with recent data')]], L('Tenere d’occhio il modello.', 'Keeping an eye on the model.')),
            dFlash(pFlash(), [[L('Un modello può peggiorare senza cambiare il codice', 'A model can degrade without code changes'), true], [L('Una volta in produzione un modello non va più controllato', 'Once in production a model needs no checking'), false], [L('Le nuove truffe sono un esempio di concept drift', 'New fraud patterns are an example of concept drift'), true], [L('Il drift riguarda solo le immagini', 'Drift only concerns images'), false]], L('Il mondo non sta fermo.', 'The world does not stand still.')),
        ]),
        lesson('llm-evals', L('Valutare le app LLM', 'Evaluating LLM apps'), [
            P('Le app con LLM si rompono in modi silenziosi: un nuovo prompt, un modello aggiornato o documenti diversi possono peggiorare le risposte senza nessun errore. Serve un **insieme di valutazione** fisso, eseguito automaticamente a ogni modifica, come i test del software.', 'LLM apps break silently: a new prompt, an updated model or different documents can worsen answers without any error. You need a fixed **evaluation set**, run automatically on every change, like software tests.'),
            C('casi = carica_jsonl("eval/assistenza.jsonl")    # domanda + criteri attesi
punteggi = []
for caso in casi:
    risposta = app.rispondi(caso["domanda"])
    punteggi.append(giudica(risposta, caso["criteri"]))   # controlli automatici o LLM-as-judge
print(f"{sum(punteggi) / len(casi):.0%}")
assert sum(punteggi) / len(casi) >= 0.85, "Qualità sotto la soglia: non rilasciare"', 'python'),
            P('Raccogli i casi dai problemi veri degli utenti, controlla a mano un campione dei voti dati dal modello giudice, e tieni traccia anche di costo e latenza per ogni versione.', 'Collect cases from real user problems, hand-check a sample of the judge model’s grades, and also track cost and latency for every version.'),
        ], [
            Q('e1', L('Perché le app LLM servono eval automatiche?', 'Why do LLM apps need automatic evals?'), [L('Possono peggiorare senza errori visibili', 'They can degrade without visible errors'), L('Per spendere di più', 'To spend more'), L('Per legge', 'By law'), L('Non servono', 'They do not')], 0, L('Rotture silenziose.', 'Silent breakage.')),
            Q('e2', L('Da dove prendere i casi di test migliori?', 'Where do the best test cases come from?'), [L('Dai problemi veri degli utenti', 'From real user problems'), L('Inventati a caso', 'Made up at random'), L('Da un altro progetto', 'From another project'), L('Dal meteo', 'From the weather')], 0, L('Rappresentano l’uso reale.', 'They represent real use.')),
            Q('e3', L('Perché controllare a mano un campione dei voti del giudice?', 'Why hand-check a sample of the judge’s grades?'), [L('Anche il giudice può sbagliare', 'The judge can be wrong too'), L('Per perdere tempo', 'To waste time'), L('È obbligatorio', 'It is mandatory'), L('Non serve', 'It is not needed')], 0, L('Fidarsi, ma verificare.', 'Trust, but verify.')),
        ], task('python', L('Calcola `media` come somma di `punteggi` divisa per `len(casi)` e blocca il rilascio con `assert media >= 0.9`.', 'Compute `media` as the sum of `punteggi` divided by `len(casi)` and block the release with `assert media >= 0.9`.'), '', [re('media\s*=\s*sum\s*\(\s*punteggi\s*\)\s*/\s*len\s*\(\s*casi\s*\)', L('Calcoli la media', 'You compute the mean')), re('assert\s+media\s*>=\s*0\.9', L('Soglia di rilascio', 'Release threshold'))], '', "media = sum(punteggi) / len(casi)\nassert media >= 0.9, \"qualità insufficiente\""), [
            dOrder(pSort(), [L('Cambia prompt o modello', 'Change prompt or model'), L('Esegui l’insieme di valutazione', 'Run the evaluation set'), L('Confronta qualità, costo e latenza', 'Compare quality, cost and latency'), L('Rilascia solo se supera la soglia', 'Release only if it passes the threshold')], L('Il cancello di qualità.', 'The quality gate.')),
            dFlash(pFlash(), [[L('Aggiornare il modello può peggiorare l’app', 'Updating the model can worsen the app'), true], [L('Le eval si fanno una volta sola', 'Evals are done only once'), false], [L('Costo e latenza vanno tracciati per versione', 'Cost and latency should be tracked per version'), true], [L('Il giudice LLM non sbaglia mai', 'The LLM judge never errs'), false]], L('Test anche per l’AI.', 'Tests for AI too.')),
        ]),
        lesson('costs', L('Costi e latenza', 'Costs and latency'), [
            P('In produzione contano **costo** e **latenza** quanto la qualità. Leve principali: scegliere il modello giusto per ogni compito (non sempre il più grande), il **prompt caching** per le parti fisse, il **batch** per i lavori non urgenti (spesso a metà prezzo), risposte più corte, e la **cache** delle risposte a domande ripetute.', 'In production **cost** and **latency** matter as much as quality. Main levers: picking the right model for each task (not always the biggest), **prompt caching** for fixed parts, **batch** processing for non-urgent jobs (often at half price), shorter answers, and **caching** answers to repeated questions.'),
            C('compito                     modello        modalità          perché
classificare 1M recensioni  piccolo        batch notturno    volume alto, nessuna fretta
chat di assistenza          medio          streaming         serve rapidità
analisi di un contratto     più capace     singola chiamata  qualità prima di tutto', 'text'),
            P('Misura sempre il **costo per compito completato**, non per chiamata: un modello economico che sbaglia e va richiamato tre volte può costare di più di uno capace che fa giusto al primo colpo.', 'Always measure **cost per completed task**, not per call: a cheap model that errs and must be called three times can cost more than a capable one that gets it right first time.'),
        ], [
            Q('c1', L('Quando conviene il batch?', 'When is batch processing worth it?'), [L('Per lavori grandi e non urgenti', 'For big, non-urgent jobs'), L('Per la chat dal vivo', 'For live chat'), L('Mai', 'Never'), L('Per una sola domanda', 'For a single question')], 0, L('Spesso costa molto meno.', 'It often costs much less.')),
            Q('c2', L('Che cosa misurare per confrontare due modelli?', 'What should you measure to compare two models?'), [L('Il costo per compito completato', 'Cost per completed task'), L('Solo il prezzo per token', 'Only price per token'), L('Il nome', 'The name'), L('La data di uscita', 'The release date')], 0, L('Gli errori costano.', 'Errors cost money.')),
            Q('c3', L('Che cosa riduce i costi di un system prompt lungo e fisso?', 'What cuts the cost of a long, fixed system prompt?'), [L('Il prompt caching', 'Prompt caching'), L('Scriverlo due volte', 'Writing it twice'), L('Usare il maiuscolo', 'Using capitals'), L('Niente', 'Nothing')], 0, L('Le parti fisse si riusano.', 'Fixed parts are reused.')),
        ], task('text', L('Per ogni compito scegli `batch` o `streaming`, uno per riga come `compito: modalità`. Compiti: `riassumere 50.000 ticket vecchi`, `assistente chat sul sito`.', 'For each task choose `batch` or `streaming`, one per line as `task: mode`. Tasks: `summarise 50,000 old tickets`, `chat assistant on the site`.'), '', [re('(ticket)[^:\n]*:\s*batch', L('I ticket vecchi vanno in batch', 'Old tickets go to batch')), re('(chat)[^:\n]*:\s*streaming', L('La chat usa lo streaming', 'Chat uses streaming'))], '', "riassumere 50.000 ticket vecchi: batch\nassistente chat sul sito: streaming"), [
            dMatch(pMatch(), [[L('Modello piccolo', 'Small model'), L('Compiti semplici in volume', 'Simple tasks in volume')], ['Batch', L('Lavori non urgenti, meno cari', 'Non-urgent jobs, cheaper')], ['Streaming', L('Rapidità percepita', 'Perceived speed')], ['Prompt caching', L('Sconto sulle parti fisse', 'Discount on fixed parts')], [L('Costo per compito', 'Cost per task'), L('La misura giusta', 'The right measure')]], L('Le leve del conto.', 'The bill’s levers.')),
            dFlash(pFlash(), [[L('Il modello più grande è sempre la scelta migliore', 'The biggest model is always the best choice'), false], [L('Il batch spesso costa meno', 'Batch often costs less'), true], [L('Un modello economico che sbaglia può costare di più', 'A cheap model that errs can cost more'), true], [L('La latenza non conta per una chat', 'Latency does not matter for a chat'), false]], L('Qualità, costo, velocità: in equilibrio.', 'Quality, cost, speed: in balance.')),
        ]),
    ],
];
