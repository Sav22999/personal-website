<?php
/** LLM APIs: messages and roles, tokens and costs, streaming, tool use, keys and errors, structured output, prompt caching and batches, images and documents. Examples with the Anthropic Python SDK. */

return [
    'id' => 'llm-api', 'family' => 'llm', 'after' => 'prompt-base',
    'title' => L('API dei modelli linguistici', 'Language Model APIs'), 'short' => L('API LLM', 'LLM APIs'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Usare un LLM dal codice: messaggi e ruoli, token e costi, streaming, strumenti (tool use), chiavi API ed errori, output strutturato, prompt caching e batch, immagini e documenti. Esempi in Python.', 'Using an LLM from code: messages and roles, tokens and costs, streaming, tools (tool use), API keys and errors, structured output, prompt caching and batches, images and documents. Examples in Python.'),
    'lessons' => [
        lesson('messages', L('Messaggi e ruoli', 'Messages and roles'), [
            P('Le API dei modelli linguistici funzionano a **messaggi**: un elenco di turni con un ruolo (`user` per chi chiede, `assistant` per il modello) più un **system prompt** con le regole. L’API non ricorda niente tra una chiamata e l’altra: per continuare una conversazione si rimanda tutta la storia.', 'Language model APIs work with **messages**: a list of turns with a role (`user` for whoever asks, `assistant` for the model) plus a **system prompt** with the rules. The API remembers nothing between calls: to continue a conversation you send the whole history again.'),
            C('import anthropic

client = anthropic.Anthropic()   # legge la chiave da ANTHROPIC_API_KEY

risposta = client.messages.create(
    model="claude-opus-5-5",
    max_tokens=1024,
    system="Sei un tutor di programmazione paziente. Rispondi in italiano.",
    messages=[{"role": "user", "content": "Che differenza c’è tra lista e tupla in Python?"}],
)
for blocco in risposta.content:
    if blocco.type == "text":
        print(blocco.text)', 'python'),
            P('Le API di OpenAI, Google, Mistral e DeepSeek seguono lo stesso schema con nomi un po’ diversi. `max_tokens` è il tetto della risposta: se lo raggiunge, il testo si interrompe a metà.', 'The OpenAI, Google, Mistral and DeepSeek APIs follow the same pattern with slightly different names. `max_tokens` caps the answer: if it is reached, the text stops midway.'),
        ], [
            Q('m1', L('L’API ricorda la conversazione precedente?', 'Does the API remember the previous conversation?'), [L('No, si rimanda la storia a ogni chiamata', 'No, you resend the history each call'), L('Sì, sempre', 'Yes, always'), L('Solo il lunedì', 'Only on Mondays'), L('Solo con la chiave premium', 'Only with a premium key')], 0, L('Ogni richiesta è indipendente.', 'Each request is independent.')),
            Q('m2', L('Quale ruolo ha il messaggio di chi chiede?', 'Which role does the asker’s message have?'), ['user', 'assistant', 'system', 'admin'], 0, L('`assistant` è il modello.', '`assistant` is the model.')),
            Q('m3', L('Che cosa succede se la risposta raggiunge `max_tokens`?', 'What happens if the answer hits `max_tokens`?'), [L('Si interrompe a metà', 'It stops midway'), L('Continua lo stesso', 'It continues anyway'), L('Dà errore 404', 'It returns a 404'), L('Si ripete', 'It repeats')], 0, L('Meglio non tenerlo troppo basso.', 'Better not set it too low.')),
        ], task('python', L('Completa la chiamata: modello `claude-opus-5-5`, `max_tokens=500` e un messaggio `user` con il testo `"Spiegami le API REST"`.', 'Complete the call: model `claude-opus-5-5`, `max_tokens=500` and a `user` message with the text `"Spiegami le API REST"`.'), "import anthropic\nclient = anthropic.Anthropic()\nrisposta = client.messages.create(\n\n)", [re('model\s*=\s*["\']claude-opus-5-5["\']', L('Il modello', 'The model')), re('max_tokens\s*=\s*500', L('Il tetto della risposta', 'The answer cap')), re('messages\s*=\s*\[\s*\{\s*["\']role["\']\s*:\s*["\']user["\']\s*,\s*["\']content["\']\s*:\s*["\']Spiegami le API REST["\']\s*\}\s*\]', L('Il messaggio dell’utente', 'The user message'))], '', "risposta = client.messages.create(\n    model=\"claude-opus-5-5\",\n    max_tokens=500,\n    messages=[{\"role\": \"user\", \"content\": \"Spiegami le API REST\"}],\n)"), [
            dMatch(pMatch(), [['system', L('Regole generali', 'General rules')], ['user', L('Chi chiede', 'Who asks')], ['assistant', L('Il modello', 'The model')], ['max_tokens', L('Tetto della risposta', 'Answer cap')], ['messages', L('La storia della conversazione', 'The conversation history')]], L('I pezzi di una richiesta.', 'The parts of a request.')),
            dOrder(pSort(), ['messages = [{"role": "user", "content": "Ciao"}]', 'r = client.messages.create(model=M, max_tokens=500, messages=messages)', 'messages.append({"role": "assistant", "content": r.content})', 'messages.append({"role": "user", "content": "E poi?"})', 'r = client.messages.create(model=M, max_tokens=500, messages=messages)'], L('Una chat si costruisce rimandando la storia.', 'A chat is built by resending the history.')),
        ]),
        lesson('tokens', L('Token, contesto e costi', 'Tokens, context and costs'), [
            P('Le API si pagano a **token**, con prezzi diversi per i token in **ingresso** (prompt, storia, documenti) e quelli in **uscita** (la risposta, di solito più cari). Ogni risposta riporta quanti token ha usato.', 'APIs are billed per **token**, with different prices for **input** tokens (prompt, history, documents) and **output** tokens (the answer, usually pricier). Each response reports how many tokens it used.'),
            C('print(risposta.usage.input_tokens, risposta.usage.output_tokens)

# stima con prezzi di esempio: 20.000 token in ingresso a 4 $ per milione + 1.000 in uscita a 20 $ per milione
costo = 20_000 / 1_000_000 * 4 + 1_000 / 1_000_000 * 20
print(f"{costo:.3f} $")   # 0.100 $', 'python'),
            P('La **finestra di contesto** limita quanto può stare in una richiesta (storia + documenti + risposta). Per risparmiare: scegli il modello adatto al compito (i piccoli costano molto meno), accorcia la storia, e usa il **prompt caching** per le parti che si ripetono.', 'The **context window** limits how much fits in a request (history + documents + answer). To save: pick the model that suits the task (small ones cost far less), trim the history, and use **prompt caching** for repeated parts.'),
        ], [
            Q('t1', L('Che cosa si paga in un’API LLM?', 'What do you pay for in an LLM API?'), [L('Token in ingresso e in uscita', 'Input and output tokens'), L('Il numero di parole del titolo', 'The words in the title'), L('Le ore di connessione', 'Connection hours'), L('I clic', 'Clicks')], 0, L('Con prezzi diversi.', 'At different prices.')),
            Q('t2', L('Dove leggi i token usati?', 'Where do you read the tokens used?'), ['usage', 'headers', 'model', 'system'], 0, L('`input_tokens` e `output_tokens`.', '`input_tokens` and `output_tokens`.')),
            Q('t3', L('Come risparmi su un prompt lungo che si ripete?', 'How do you save on a long prompt that repeats?'), [L('Con il prompt caching', 'With prompt caching'), L('Scrivendolo in maiuscolo', 'Writing it in capitals'), L('Rimandandolo due volte', 'Sending it twice'), L('Non si può', 'You cannot')], 0, L('Le parti in cache costano molto meno.', 'Cached parts cost much less.')),
        ], task('python', L('Calcola in `costo` il prezzo di 50.000 token in ingresso a 2 $ per milione e 2.000 in uscita a 10 $ per milione, poi stampalo.', 'Compute in `costo` the price of 50,000 input tokens at $2 per million and 2,000 output tokens at $10 per million, then print it.'), '', [re('costo\s*=\s*50_?000\s*/\s*1_?000_?000\s*\*\s*2\s*\+\s*2_?000\s*/\s*1_?000_?000\s*\*\s*10', L('Il calcolo è giusto', 'The calculation is right')), re('print\s*\(', L('Lo stampi', 'You print it'))], '', "costo = 50_000 / 1_000_000 * 2 + 2_000 / 1_000_000 * 10\nprint(costo)"), [
            dMatch(pMatch(), [[L('Token in ingresso', 'Input tokens'), L('Prompt e storia', 'Prompt and history')], [L('Token in uscita', 'Output tokens'), L('La risposta', 'The answer')], [L('Contesto', 'Context'), L('Tutto quello che sta in una richiesta', 'Everything in one request')], [L('Prompt caching', 'Prompt caching'), L('Sconto sulle parti ripetute', 'Discount on repeated parts')]], L('Il conto in token.', 'The bill in tokens.')),
            dFlash(pFlash(), [[L('I token in uscita costano di solito più di quelli in ingresso', 'Output tokens usually cost more than input'), true], [L('Rimandare tutta la storia non costa nulla', 'Resending the whole history costs nothing'), false], [L('Un modello piccolo costa meno', 'A small model costs less'), true], [L('La finestra di contesto è infinita', 'The context window is infinite'), false]], L('Ogni token conta.', 'Every token counts.')),
        ]),
        lesson('streaming', L('Streaming', 'Streaming'), [
            P('Una risposta lunga può impiegare molti secondi. Con lo **streaming** il testo arriva a pezzi mentre viene generato, come nelle chat: l’utente vede subito qualcosa e le richieste lunghe non vanno in timeout.', 'A long answer can take many seconds. With **streaming** the text arrives in pieces while it is generated, as in chat apps: the user sees something at once and long requests do not time out.'),
            C('with client.messages.stream(
    model="claude-opus-5-5",
    max_tokens=4000,
    messages=[{"role": "user", "content": "Scrivi un racconto breve sul mare."}],
) as stream:
    for pezzo in stream.text_stream:
        print(pezzo, end="", flush=True)
    finale = stream.get_final_message()   # il messaggio completo, con usage', 'python'),
            P('Sul web lo streaming arriva al browser con i **Server-Sent Events** (SSE) o i WebSocket. Il messaggio finale serve per salvare la risposta intera e i token usati.', 'On the web streaming reaches the browser through **Server-Sent Events** (SSE) or WebSockets. The final message is used to store the full answer and the tokens used.'),
        ], [
            Q('s1', L('Perché usare lo streaming?', 'Why use streaming?'), [L('L’utente vede il testo mentre arriva', 'The user sees text as it arrives'), L('Costa meno token', 'It costs fewer tokens'), L('Migliora la grammatica', 'It improves grammar'), L('È obbligatorio', 'It is mandatory')], 0, L('E si evitano i timeout.', 'And timeouts are avoided.')),
            Q('s2', L('Con quale tecnologia arriva spesso lo streaming al browser?', 'Which technology often carries streaming to the browser?'), ['Server-Sent Events', 'FTP', 'SMTP', 'CSV'], 0, L('O i WebSocket.', 'Or WebSockets.')),
            Q('s3', L('A che cosa serve `get_final_message()`?', 'What is `get_final_message()` for?'), [L('Avere la risposta completa alla fine', 'Getting the complete answer at the end'), L('Annullare lo stream', 'Cancelling the stream'), L('Ricominciare', 'Restarting'), L('Cambiare modello', 'Changing model')], 0, L('Con testo e usage.', 'With text and usage.')),
        ], task('python', L('Dentro il blocco `with … as stream:` stampa ogni pezzo di `stream.text_stream` con `end=""`.', 'Inside the `with … as stream:` block print every piece of `stream.text_stream` with `end=""`.'), "with client.messages.stream(model=\"claude-opus-5-5\", max_tokens=2000, messages=messaggi) as stream:\n    ", [re('for\s+(\w+)\s+in\s+stream\.text_stream\s*:', L('Scorri i pezzi', 'You loop over the pieces')), re('print\s*\(\s*\w+\s*,\s*end\s*=\s*["\']["\']', L('Li stampi senza a capo', 'You print them without newlines'))], '', "for pezzo in stream.text_stream:\n        print(pezzo, end=\"\")"), [
            dBlanks(pFill(), "with client.messages.___(model=M, max_tokens=2000, messages=msgs) as s:\n    for t in s.___:\n        print(t, end=\"\")\n    finale = s.___()", ['stream', 'text_stream', 'get_final_message'], ['create', 'text', 'close'], L('Lo schema dello streaming.', 'The streaming pattern.')),
            dFlash(pFlash(), [[L('Con lo streaming il testo arriva a pezzi', 'With streaming text arrives in pieces'), true], [L('Lo streaming cambia il contenuto della risposta', 'Streaming changes the answer’s content'), false], [L('Lo streaming aiuta con le risposte lunghe', 'Streaming helps with long answers'), true], [L('Lo streaming funziona solo da terminale', 'Streaming only works from a terminal'), false]], L('Subito qualcosa sullo schermo.', 'Something on screen at once.')),
        ]),
        lesson('tools', L('Strumenti: tool use', 'Tools: tool use'), [
            P('Con il **tool use** (o function calling) il modello può chiedere al tuo programma di eseguire una funzione: cercare un prodotto, leggere il meteo, fare un calcolo. Tu descrivi gli strumenti con nome, descrizione e uno schema JSON degli input; il modello risponde con una richiesta di uso; il tuo codice la esegue e rimanda il risultato.', 'With **tool use** (or function calling) the model can ask your program to run a function: look up a product, read the weather, do a calculation. You describe the tools with a name, a description and a JSON schema for the inputs; the model replies with a request to use one; your code runs it and sends the result back.'),
            C('strumenti = [{
    "name": "meteo",
    "description": "Restituisce il meteo attuale di una città italiana.",
    "input_schema": {
        "type": "object",
        "properties": {"citta": {"type": "string"}},
        "required": ["citta"],
    },
}]
r = client.messages.create(model="claude-opus-5-5", max_tokens=1024, tools=strumenti,
                           messages=[{"role": "user", "content": "Piove a Udine?"}])
if r.stop_reason == "tool_use":
    uso = next(b for b in r.content if b.type == "tool_use")
    print(uso.name, uso.input)    # meteo {\'citta\': \'Udine\'}', 'python'),
            P('Il modello **non esegue** niente da solo: decide quale strumento chiedere, e sei tu a eseguirlo e a rimandare un blocco `tool_result`. Una buona **descrizione** dello strumento è la cosa più importante per farlo usare bene.', 'The model **runs** nothing by itself: it decides which tool to ask for, and you run it and send back a `tool_result` block. A good tool **description** is what matters most for it to be used well.'),
        ], [
            Q('u1', L('Chi esegue davvero lo strumento?', 'Who actually runs the tool?'), [L('Il tuo programma', 'Your program'), L('Il modello', 'The model'), L('Il browser dell’utente', 'The user’s browser'), L('Nessuno', 'Nobody')], 0, L('Il modello chiede, tu esegui.', 'The model asks, you run.')),
            Q('u2', L('Che cosa indica `stop_reason == "tool_use"`?', 'What does `stop_reason == "tool_use"` mean?'), [L('Il modello vuole usare uno strumento', 'The model wants to use a tool'), L('La risposta è finita', 'The answer is over'), L('C’è un errore', 'There is an error'), L('Il token è scaduto', 'The token expired')], 0, L('Esegui e rimanda il risultato.', 'Run it and send the result back.')),
            Q('u3', L('Che cosa descrive `input_schema`?', 'What does `input_schema` describe?'), [L('Gli argomenti dello strumento', 'The tool’s arguments'), L('Il risultato', 'The result'), L('Il prezzo', 'The price'), L('L’utente', 'The user')], 0, L('Uno schema JSON.', 'A JSON schema.')),
        ], task('python', L('Definisci lo strumento `converti_valuta` con una descrizione e un `input_schema` che richiede `importo` (number) e `valuta` (string).', 'Define the tool `converti_valuta` with a description and an `input_schema` requiring `importo` (number) and `valuta` (string).'), "strumento = {\n\n}", [re('["\']name["\']\s*:\s*["\']converti_valuta["\']', L('Il nome', 'The name')), re('["\']description["\']\s*:\s*["\'][^"\']{10,}', L('Una descrizione utile', 'A useful description')), re('["\']importo["\']\s*:\s*\{\s*["\']type["\']\s*:\s*["\']number["\']', L('L’importo è un numero', 'The amount is a number')), re('["\']required["\']\s*:\s*\[\s*["\'](importo|valuta)["\']\s*,\s*["\'](importo|valuta)["\']\s*\]', L('Entrambi obbligatori', 'Both required'))], '', "strumento = {\n    \"name\": \"converti_valuta\",\n    \"description\": \"Converte un importo in euro nella valuta indicata.\",\n    \"input_schema\": {\n        \"type\": \"object\",\n        \"properties\": {\"importo\": {\"type\": \"number\"}, \"valuta\": {\"type\": \"string\"}},\n        \"required\": [\"importo\", \"valuta\"],\n    },\n}"), [
            dOrder(pSort(), [L('Invii domanda e strumenti', 'Send question and tools'), L('Il modello chiede uno strumento (tool_use)', 'The model asks for a tool (tool_use)'), L('Il tuo codice lo esegue', 'Your code runs it'), L('Rimandi il tool_result', 'Send back the tool_result'), L('Il modello scrive la risposta finale', 'The model writes the final answer')], L('Il giro completo del tool use.', 'The full tool use loop.')),
            dMatch(pMatch(), [['name', L('Come si chiama lo strumento', 'The tool’s name')], ['description', L('Quando e come usarlo', 'When and how to use it')], ['input_schema', L('Gli argomenti', 'The arguments')], ['tool_use', L('Richiesta del modello', 'The model’s request')], ['tool_result', L('Il tuo risultato', 'Your result')]], L('Il vocabolario degli strumenti.', 'Tool vocabulary.')),
        ]),
        lesson('production', L('Chiavi, errori e produzione', 'Keys, errors and production'), [
            P('La **chiave API** è un segreto: mettila in una variabile d’ambiente o in un gestore di segreti, mai nel codice, nel repository o nel JavaScript del browser. Chi ha la chiave spende a tuo nome. Le chiamate al modello si fanno dal **server**.', 'The **API key** is a secret: keep it in an environment variable or a secrets manager, never in code, in the repository or in browser JavaScript. Whoever has the key spends in your name. Model calls happen on the **server**.'),
            C('import anthropic

client = anthropic.Anthropic(max_retries=3)   # riprova da solo su 429 e 5xx
try:
    r = client.messages.create(model="claude-opus-5-5", max_tokens=1024, messages=msgs)
except anthropic.RateLimitError:
    print("Troppe richieste: rallenta")       # 429
except anthropic.APIStatusError as e:
    print("Errore", e.status_code)', 'python'),
            P('Errori tipici: **429** (troppe richieste, aspetta e riprova), **5xx** (problema del servizio, riprova), **400** (richiesta sbagliata, correggi: riprovare non serve). Registra sempre token e tempi, e imposta limiti di spesa nella console del fornitore.', 'Typical errors: **429** (too many requests, wait and retry), **5xx** (service problem, retry), **400** (bad request, fix it: retrying will not help). Always log tokens and timings, and set spending limits in the provider’s console.'),
        ], [
            Q('k1', L('Dove va tenuta la chiave API?', 'Where should the API key be kept?'), [L('In una variabile d’ambiente sul server', 'In an environment variable on the server'), L('Nel JavaScript del sito', 'In the site’s JavaScript'), L('Nel README pubblico', 'In the public README'), L('In un commento del codice', 'In a code comment')], 0, L('Chi la vede la può usare.', 'Whoever sees it can use it.')),
            Q('k2', L('Che cosa fai con un errore 429?', 'What do you do with a 429 error?'), [L('Aspetti e riprovi', 'Wait and retry'), L('Cambi chiave a caso', 'Swap keys at random'), L('Riprovi all’infinito subito', 'Retry forever at once'), L('Cancelli l’account', 'Delete the account')], 0, L('Troppe richieste.', 'Too many requests.')),
            Q('k3', L('Riprovare serve con un errore 400?', 'Does retrying help with a 400 error?'), [L('No, la richiesta va corretta', 'No, the request must be fixed'), L('Sì, sempre', 'Yes, always'), L('Solo di notte', 'Only at night'), L('Solo con streaming', 'Only with streaming')], 0, L('L’errore è nella richiesta.', 'The error is in the request.')),
        ], task('python', L('Crea il client con `max_retries=5` e gestisci `anthropic.RateLimitError` con un `except` che stampa `"riprova più tardi"`.', 'Create the client with `max_retries=5` and handle `anthropic.RateLimitError` with an `except` printing `"riprova più tardi"`.'), "import anthropic\n", [re('anthropic\.Anthropic\s*\(\s*max_retries\s*=\s*5\s*\)', L('Client con 5 tentativi', 'Client with 5 retries')), re('try\s*:', L('C’è un try', 'There is a try')), re('except\s+anthropic\.RateLimitError\s*:\s*\n\s+print\s*\(\s*["\']riprova più tardi["\']\s*\)', L('Gestisci il 429', 'You handle the 429'))], '', "client = anthropic.Anthropic(max_retries=5)\ntry:\n    r = client.messages.create(model=\"claude-opus-5-5\", max_tokens=500, messages=msgs)\nexcept anthropic.RateLimitError:\n    print(\"riprova più tardi\")"), [
            dBug(pBug(), ['// app.js, nel browser', 'const KEY = "sk-ant-…segreta…";', 'fetch("/api/chat", {method: "POST", body: domanda});'], 1, '// la chiave resta sul server, letta da una variabile d’ambiente', L('Una chiave nel JavaScript del browser è pubblica.', 'A key in browser JavaScript is public.')),
            dMatch(pMatch(), [['400', L('Richiesta sbagliata: correggi', 'Bad request: fix it')], ['429', L('Troppe richieste: aspetta', 'Too many requests: wait')], ['5xx', L('Problema del servizio: riprova', 'Service problem: retry')], [L('Variabile d’ambiente', 'Environment variable'), L('Dove sta la chiave', 'Where the key lives')]], L('Pronti per la produzione.', 'Ready for production.')),
        ]),
        lesson('structured', L('Output strutturato dall’API', 'Structured output from the API'), [
            P('Quando il tuo codice deve leggere la risposta, non basta chiedere “rispondi in JSON”: le API moderne permettono di passare uno **schema JSON** e vincolano il modello a rispettarlo. Il nome del parametro cambia da fornitore a fornitore; con l’SDK di Anthropic si usa `output_config`.', 'When your code must read the answer, asking “reply in JSON” is not enough: modern APIs let you pass a **JSON schema** and constrain the model to follow it. The parameter name varies from provider to provider; with the Anthropic SDK you use `output_config`.'),
            C('import json

r = client.messages.create(
    model="claude-opus-5-5",
    max_tokens=1024,
    messages=[{"role": "user", "content": "Estrai i dati: Mario Rossi prenota 3 biglietti"}],
    output_config={"format": {
        "type": "json_schema",
        "schema": {
            "type": "object",
            "properties": {"nome": {"type": "string"}, "biglietti": {"type": "integer"}},
            "required": ["nome", "biglietti"],
            "additionalProperties": False,
        },
    }},
)
dati = json.loads(next(b.text for b in r.content if b.type == "text"))
print(dati["biglietti"])    # 3', 'python'),
            P('Lo stesso vale per gli strumenti: con `"strict": True` nella definizione di uno strumento, gli argomenti che il modello passa rispettano esattamente il suo `input_schema`. Controlla comunque `stop_reason`: se la risposta si interrompe per `max_tokens`, il JSON può restare incompleto.', 'The same goes for tools: with `"strict": True` in a tool definition, the arguments the model passes follow its `input_schema` exactly. Still check `stop_reason`: if the answer stops at `max_tokens`, the JSON may be incomplete.'),
        ], [
            Q('j1', L('Che cosa garantisce lo schema passato in `output_config`?', 'What does the schema passed in `output_config` guarantee?'), [L('Che la risposta sia più breve', 'That the answer is shorter'), L('Che la risposta sia sempre vera', 'That the answer is always true'), L('Che il modello usi meno token', 'That the model uses fewer tokens'), L('Che la risposta rispetti la struttura dello schema', 'That the answer follows the schema’s structure')], 3, L('Il formato, non la verità del contenuto.', 'The format, not the truth of the content.')),
            Q('j2', L('A che cosa serve `"strict": True` su uno strumento?', 'What is `"strict": True` on a tool for?'), [L('Gli argomenti rispettano esattamente lo schema', 'The arguments follow the schema exactly'), L('Lo strumento viene eseguito dal modello', 'The tool is run by the model'), L('Lo strumento diventa obbligatorio', 'The tool becomes mandatory'), L('Lo strumento risponde più in fretta', 'The tool answers faster')], 0, L('Niente argomenti mancanti o in più.', 'No missing or extra arguments.')),
            Q('j3', L('Perché controllare `stop_reason` anche con l’output strutturato?', 'Why check `stop_reason` even with structured output?'), [L('Per sapere che modello ha risposto', 'To know which model answered'), L('Se si ferma a `max_tokens` il JSON può essere incompleto', 'If it stops at `max_tokens` the JSON may be incomplete'), L('Per contare le parole della risposta', 'To count the answer’s words'), L('Non serve mai controllarlo', 'It never needs checking')], 1, L('Tagliato a metà non si legge.', 'Cut in half, it cannot be parsed.')),
        ], task('python', L('Scrivi il valore di `formato` per `output_config`: tipo `"json_schema"` e uno schema con il campo obbligatorio `categoria` (string), senza campi in più.', 'Write the value of `formato` for `output_config`: type `"json_schema"` and a schema with the required field `categoria` (string), with no extra fields.'), "formato = {\n\n}", [re('["\']type["\']\s*:\s*["\']json_schema["\']', L('Il tipo json_schema', 'The json_schema type')), re('["\']categoria["\']\s*:\s*\{\s*["\']type["\']\s*:\s*["\']string["\']', L('Il campo categoria', 'The categoria field')), re('["\']required["\']\s*:\s*\[[^\]]*["\']categoria["\']', L('Campo obbligatorio', 'Required field')), re('["\']additionalProperties["\']\s*:\s*False', L('Niente campi in più', 'No extra fields'))], '', "formato = {\n    \"type\": \"json_schema\",\n    \"schema\": {\n        \"type\": \"object\",\n        \"properties\": {\"categoria\": {\"type\": \"string\"}},\n        \"required\": [\"categoria\"],\n        \"additionalProperties\": False,\n    },\n}"), [
            dBlanks(pFill(), "output_config={\"format\": {\n    \"type\": \"___\",\n    \"schema\": {\"type\": \"object\", \"properties\": P,\n               \"___\": [\"nome\"], \"additionalProperties\": ___},\n}}", ['json_schema', 'required', 'False'], ['json', 'optional', 'True'], L('Uno schema rigoroso.', 'A strict schema.')),
            dFlash(pFlash(), [[L('Lo schema vincola la forma della risposta', 'The schema constrains the answer’s shape'), true], [L('L’output strutturato garantisce che i fatti siano veri', 'Structured output guarantees the facts are true'), false], [L('`strict` vale anche per gli argomenti degli strumenti', '`strict` also applies to tool arguments'), true], [L('Una risposta tagliata a `max_tokens` è sempre JSON valido', 'An answer cut at `max_tokens` is always valid JSON'), false]], L('Forma garantita, contenuto da verificare.', 'Shape guaranteed, content to check.')),
        ]),
        lesson('cache-batch', L('Prompt caching e batch', 'Prompt caching and batches'), [
            P('Se molte richieste iniziano con lo stesso lungo testo (un manuale, un system prompt, un elenco di strumenti), il **prompt caching** lo conserva per qualche minuto: le richieste successive lo rileggono dalla cache, più veloci e a una frazione del prezzo. La cache funziona sul **prefisso**: la parte fissa va all’inizio, quella che cambia dopo.', 'If many requests start with the same long text (a manual, a system prompt, a tool list), **prompt caching** keeps it for a few minutes: later requests read it back from the cache, faster and at a fraction of the price. The cache works on the **prefix**: the fixed part goes first, the changing part after.'),
            C('r = client.messages.create(
    model="claude-opus-5-5", max_tokens=1024,
    system=[{"type": "text", "text": MANUALE, "cache_control": {"type": "ephemeral"}}],
    messages=[{"role": "user", "content": domanda}],
)
print(r.usage.cache_read_input_tokens)     # token letti dalla cache

lotto = client.messages.batches.create(requests=[
    {"custom_id": f"rec-{i}",
     "params": {"model": "claude-opus-5-5", "max_tokens": 200,
                "messages": [{"role": "user", "content": f"Classifica: {t}"}]}}
    for i, t in enumerate(recensioni)
])', 'python'),
            P('Per i lavori grandi e non urgenti c’è la **Batch API**: invii migliaia di richieste insieme, il servizio le elabora entro alcune ore a prezzo ridotto (spesso la metà), poi scarichi i risultati. I risultati possono arrivare in qualsiasi ordine: abbinali con il `custom_id`, mai con la posizione.', 'For big, non-urgent jobs there is the **Batch API**: you send thousands of requests together, the service processes them within hours at a reduced price (often half), then you download the results. Results may arrive in any order: match them by `custom_id`, never by position.'),
        ], [
            Q('b1', L('Dove va la parte fissa del prompt per sfruttare la cache?', 'Where does the fixed part of the prompt go to use the cache?'), [L('Alla fine, dopo la domanda', 'At the end, after the question'), L('In un file separato mai inviato', 'In a separate file never sent'), L('All’inizio, prima delle parti che cambiano', 'At the start, before the changing parts'), L('Non conta dove va', 'It does not matter where')], 2, L('La cache riconosce il prefisso.', 'The cache matches the prefix.')),
            Q('b2', L('Come si abbinano i risultati di un batch alle richieste?', 'How do you match batch results to requests?'), [L('Con il `custom_id` di ogni richiesta', 'With each request’s `custom_id`'), L('Con la posizione nell’elenco dei risultati', 'With the position in the result list'), L('Con l’ora di arrivo dei risultati', 'With the results’ arrival time'), L('Non si possono abbinare', 'They cannot be matched')], 0, L('L’ordine non è garantito.', 'Order is not guaranteed.')),
            Q('b3', L('Quando conviene la Batch API?', 'When is the Batch API worth it?'), [L('Per una chat dal vivo con l’utente', 'For a live chat with the user'), L('Per migliaia di richieste non urgenti', 'For thousands of non-urgent requests'), L('Per una sola domanda urgente', 'For a single urgent question'), L('Per lo streaming delle risposte', 'For streaming answers')], 1, L('Meno caro, ma non immediato.', 'Cheaper, but not immediate.')),
        ], task('python', L('Nel `system` aggiungi al blocco di testo `MANUALE` il campo `"cache_control": {"type": "ephemeral"}`, poi stampa `r.usage.cache_read_input_tokens`.', 'In `system` add to the `MANUALE` text block the field `"cache_control": {"type": "ephemeral"}`, then print `r.usage.cache_read_input_tokens`.'), "r = client.messages.create(\n    model=\"claude-opus-5-5\", max_tokens=1024,\n    system=[{\"type\": \"text\", \"text\": MANUALE}],\n    messages=[{\"role\": \"user\", \"content\": domanda}],\n)", [re('["\']text["\']\s*:\s*MANUALE\s*,\s*["\']cache_control["\']\s*:\s*\{\s*["\']type["\']\s*:\s*["\']ephemeral["\']\s*\}', L('Il blocco va in cache', 'The block is cached')), re('print\s*\(\s*r\.usage\.cache_read_input_tokens\s*\)', L('Controlli la cache', 'You check the cache'))], '', "r = client.messages.create(\n    model=\"claude-opus-5-5\", max_tokens=1024,\n    system=[{\"type\": \"text\", \"text\": MANUALE, \"cache_control\": {\"type\": \"ephemeral\"}}],\n    messages=[{\"role\": \"user\", \"content\": domanda}],\n)\nprint(r.usage.cache_read_input_tokens)"), [
            dMatch(pMatch(), [['cache_control', L('Segna cosa mettere in cache', 'Marks what to cache')], ['cache_read_input_tokens', L('Token letti dalla cache', 'Tokens read from cache')], ['batches.create', L('Invia un lotto di richieste', 'Sends a batch of requests')], ['custom_id', L('Abbina risultato e richiesta', 'Matches result and request')]], L('Pagare meno, aspettando un po’.', 'Paying less, waiting a little.')),
            dFlash(pFlash(), [[L('Un dettaglio che cambia all’inizio del prompt rovina la cache', 'A detail changing at the start of the prompt breaks the cache'), true], [L('I risultati di un batch arrivano sempre nell’ordine di invio', 'Batch results always arrive in sending order'), false], [L('Il batch è adatto ai lavori non urgenti', 'Batch suits non-urgent jobs'), true], [L('La cache conserva il prompt per sempre', 'The cache keeps the prompt forever'), false]], L('Prefisso stabile, lotti pazienti.', 'Stable prefix, patient batches.')),
        ]),
        lesson('vision', L('Immagini e documenti', 'Images and documents'), [
            P('I modelli multimodali accettano anche **immagini** e **PDF** nei messaggi. Il campo `content` diventa un elenco di blocchi: un blocco `image` o `document` (codificato in base64, oppure caricato prima con un’API dei file) e un blocco `text` con la domanda. Conviene mettere l’immagine **prima** del testo.', 'Multimodal models also accept **images** and **PDFs** in messages. The `content` field becomes a list of blocks: an `image` or `document` block (base64-encoded, or uploaded beforehand through a files API) and a `text` block with the question. It is best to put the image **before** the text.'),
            C('import base64

with open("scontrino.jpg", "rb") as f:
    dati = base64.standard_b64encode(f.read()).decode()

r = client.messages.create(model="claude-opus-5-5", max_tokens=1024, messages=[{
    "role": "user",
    "content": [
        {"type": "image", "source": {"type": "base64", "media_type": "image/jpeg", "data": dati}},
        {"type": "text", "text": "Qual è il totale dello scontrino?"},
    ],
}])
# un PDF: {"type": "document", "source": {"type": "base64", "media_type": "application/pdf", "data": …}}', 'python'),
            P('Anche immagini e pagine si pagano in **token**, in proporzione alla dimensione: ridimensiona le foto enormi prima di inviarle. Per i dati letti da un’immagine (cifre, nomi) chiedi l’output strutturato e verifica i valori importanti: una cifra letta male in una foto storta è un errore comune.', 'Images and pages are also billed in **tokens**, in proportion to their size: resize huge photos before sending them. For data read from an image (figures, names) ask for structured output and check the important values: a figure misread in a crooked photo is a common mistake.'),
        ], [
            Q('v1', L('Come si manda un’immagine insieme a una domanda?', 'How do you send an image together with a question?'), [L('Incollando il percorso del file nel testo', 'Pasting the file path into the text'), L('Con due richieste separate, una per blocco', 'With two separate requests, one per block'), L('Mettendo l’immagine nel system prompt come testo', 'Putting the image in the system prompt as text'), L('Con un elenco di blocchi: `image` e poi `text`', 'With a list of blocks: `image` then `text`')], 3, L('Più blocchi nello stesso messaggio.', 'Several blocks in the same message.')),
            Q('v2', L('Quale `type` ha il blocco di un PDF?', 'What `type` does a PDF block have?'), ['document', 'pdf', 'file_text', 'attachment'], 0, L('Con `media_type` application/pdf.', 'With `media_type` application/pdf.')),
            Q('v3', L('Perché ridimensionare una foto molto grande prima di inviarla?', 'Why resize a very large photo before sending it?'), [L('Le foto grandi non si possono codificare in base64', 'Large photos cannot be base64-encoded'), L('Le immagini si pagano in token in base alla dimensione', 'Images are billed in tokens by size'), L('Il modello legge solo immagini in bianco e nero', 'The model only reads black-and-white images'), L('Per rendere il testo più leggibile all’utente', 'To make the text more readable to the user')], 1, L('Meno pixel, meno token.', 'Fewer pixels, fewer tokens.')),
        ], task('python', L('Completa `content` con un blocco `image` (base64, `image/png`, dati in `dati`) seguito da un blocco `text` con la domanda `"Che cosa mostra il grafico?"`.', 'Complete `content` with an `image` block (base64, `image/png`, data in `dati`) followed by a `text` block with the question `"Che cosa mostra il grafico?"`.'), "content = [\n\n]", [re('["\']type["\']\s*:\s*["\']image["\']\s*,\s*["\']source["\']\s*:\s*\{\s*["\']type["\']\s*:\s*["\']base64["\']', L('Il blocco immagine', 'The image block')), re('["\']media_type["\']\s*:\s*["\']image/png["\']\s*,\s*["\']data["\']\s*:\s*dati', L('Formato e dati', 'Format and data')), re('image[\s\S]*["\']type["\']\s*:\s*["\']text["\']\s*,\s*["\']text["\']\s*:\s*["\']Che cosa mostra il grafico\?["\']', L('Poi la domanda', 'Then the question'))], '', "content = [\n    {\"type\": \"image\", \"source\": {\"type\": \"base64\", \"media_type\": \"image/png\", \"data\": dati}},\n    {\"type\": \"text\", \"text\": \"Che cosa mostra il grafico?\"},\n]"), [
            dOrder(pSort(), ['with open("foto.jpg", "rb") as f:', '    dati = base64.standard_b64encode(f.read()).decode()', 'blocco = {"type": "image", "source": {"type": "base64", "media_type": "image/jpeg", "data": dati}}', 'content = [blocco, {"type": "text", "text": "Descrivi la foto"}]', 'r = client.messages.create(model=M, max_tokens=1024, messages=[{"role": "user", "content": content}])'], L('Leggi, codifica, componi, invia.', 'Read, encode, compose, send.')),
            dMatch(pMatch(), [['image', L('Blocco immagine', 'Image block')], ['document', L('Blocco PDF', 'PDF block')], ['base64', L('Il file come testo', 'The file as text')], ['media_type', L('Il formato del file', 'The file format')]], L('Oltre il testo, dall’API.', 'Beyond text, from the API.')),
        ]),
    ],
];
