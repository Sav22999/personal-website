<?php
/** Alibaba Qwen: the open model family, Qwen Chat, running it locally, the OpenAI-compatible API, comparing with DeepSeek. */

/** The range of open Qwen sizes, from a phone to a server, with the closed Max models only in the cloud. Labels in one language. */
if (!function_exists('fig_qwen_sizes')) {
    function fig_qwen_sizes(string $lg): string
    {
        $t = $lg === 'it'
            ? ['telefono', 'portatile', 'PC con GPU', 'server con più GPU', 'solo cloud, chiuso', 'B = miliardi di parametri (taglie indicative)', 'pesi aperti']
            : ['phone', 'laptop', 'PC with a GPU', 'multi-GPU server', 'cloud only, closed', 'B = billions of parameters (indicative sizes)', 'open weights'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="16" y="26" font-size="13" font-weight="700" fill="#0B7F5C">{$t[6]}</text>
<g fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"><rect x="110" y="38" width="40" height="32" rx="8"/><rect x="110" y="80" width="110" height="32" rx="8"/><rect x="110" y="122" width="210" height="32" rx="8"/><rect x="110" y="164" width="380" height="32" rx="8"/></g>
<rect x="110" y="206" width="510" height="32" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<g font-weight="700" text-anchor="end"><text x="98" y="60">0.6B</text><text x="98" y="102">8B</text><text x="98" y="144">32B</text><text x="98" y="186">235B</text><text x="98" y="228">Max</text></g>
<g font-size="14"><text x="162" y="60">{$t[0]}</text><text x="232" y="102">{$t[1]}</text><text x="332" y="144">{$t[2]}</text><text x="502" y="186">{$t[3]}</text><text x="126" y="228">{$t[4]}</text></g>
<text x="16" y="262" font-size="13">{$t[5]}</text>
</svg>
SVG;
    }
}

/** Tool calling as a conversation between your program and the model: the model asks for a call, your program runs it and sends back the result. Labels in one language. */
if (!function_exists('fig_qwen_tools')) {
    function fig_qwen_tools(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Il tuo programma', 'Il modello', '1  domanda + elenco strumenti', '«Che tempo fa a Trieste?»', '2  chiede una chiamata', '3  esegue meteo() e manda il risultato', '4  risponde', '«A Trieste 18 °C e sole»']
            : ['Your program', 'The model', '1  question + list of tools', '“What’s the weather in Trieste?”', '2  asks for a call', '3  runs meteo() and sends the result', '4  answers', '“18 °C and sunny in Trieste”'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 350" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="12" width="170" height="44" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><rect x="454" y="12" width="170" height="44" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<g font-weight="700" text-anchor="middle"><text x="101" y="39" fill="#1B66BC">{$t[0]}</text><text x="539" y="39" fill="#5A36C9">{$t[1]}</text></g>
<g stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 4"><path d="M101 56V342"/><path d="M539 56V342"/></g>
<g stroke="#1E1C52" stroke-width="2" marker-end="url(#a)"><path d="M101 98H535"/><path d="M539 160H105"/><path d="M101 240H535"/><path d="M539 316H105"/></g>
<g font-size="14" font-weight="700" text-anchor="middle"><text x="320" y="86">{$t[2]}</text><text x="320" y="148">{$t[4]}</text><text x="320" y="228">{$t[5]}</text><text x="320" y="304">{$t[6]}</text></g>
<g font-size="13" text-anchor="middle"><text x="320" y="118">{$t[3]}</text><text x="320" y="336">{$t[7]}</text></g>
<rect x="196" y="168" width="248" height="26" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="320" y="186" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace" fill="#B84508">meteo({"citta": "Trieste"})</text>
<rect x="250" y="248" width="140" height="26" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="320" y="266" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace" fill="#0B7F5C">{"temp": 18}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'qwen', 'family' => 'assistants', 'after' => null,
    'title' => L('Alibaba Qwen', 'Alibaba Qwen'), 'short' => L('Qwen', 'Qwen'), 'level' => L('Base', 'Basics'),
    'desc' => L('La famiglia di modelli aperti di Alibaba: dimensioni e varianti per codice e immagini, Qwen Chat, uso in locale, API compatibile e confronto con DeepSeek.', 'Alibaba’s open model family: sizes and variants for code and images, Qwen Chat, local use, compatible API and comparison with DeepSeek.'),
    'lessons' => [
        lesson('models', L('La famiglia Qwen', 'The Qwen family'), [
            P('**Qwen** è la famiglia di modelli linguistici di **Alibaba Cloud**. Molti modelli sono pubblicati **open-weight**, spesso con licenza **Apache 2.0**, in tante dimensioni: da modelli minuscoli che girano su un telefono a modelli enormi per il cloud. I modelli di punta della serie **Max**, invece, sono di solito chiusi e si usano solo via app o API. Le generazioni si susseguono in fretta (Qwen3, Qwen3.5 e oltre).', '**Qwen** is **Alibaba Cloud**’s family of language models. Many models are published **open-weight**, often under the **Apache 2.0** licence, in many sizes: from tiny models that run on a phone to huge cloud models. The flagship **Max** models, however, are usually closed and only available through the app or API. Generations follow each other quickly (Qwen3, Qwen3.5 and beyond).'),
            Fig(L(fig_qwen_sizes('it'), fig_qwen_sizes('en')),
                L('Barre orizzontali sempre più lunghe: 0.6B per il telefono, 8B per il portatile, 32B per un PC con GPU, 235B per un server con più GPU, tutte verdi con pesi aperti; l’ultima, Max, è grigia e tratteggiata: solo cloud, chiusa.', 'Ever longer horizontal bars: 0.6B for a phone, 8B for a laptop, 32B for a PC with a GPU, 235B for a multi-GPU server, all green with open weights; the last one, Max, is grey and dashed: cloud only, closed.'),
                L('Più parametri significano più capacità, ma anche più memoria per farli girare.', 'More parameters mean more capability, but also more memory to run them.')),
            P('Ci sono varianti specializzate: per il **codice** (Qwen-Coder), per la **visione** (Qwen-VL, che legge immagini e documenti), per l’**audio** e per il **ragionamento**. I modelli Qwen sono molto usati come base per versioni adattate su Hugging Face e sono forti in molte lingue, compreso il cinese.', 'There are specialised variants: for **code** (Qwen-Coder), for **vision** (Qwen-VL, which reads images and documents), for **audio** and for **reasoning**. Qwen models are widely used as a base for fine-tuned versions on Hugging Face and are strong in many languages, including Chinese.'),
        ], [
            Q('m1', L('Quale azienda sviluppa Qwen?', 'Which company develops Qwen?'), ['Alibaba Cloud', 'Meta', 'Mistral AI', 'xAI'], 0, L('Gruppo cinese Alibaba.', 'Chinese group Alibaba.')),
            Q('m2', L('Quale variante legge immagini e documenti?', 'Which variant reads images and documents?'), ['Qwen-VL', 'Qwen-Coder', 'Qwen-Mini', 'Qwen-Audio-Only'], 0, L('VL = vision-language.', 'VL = vision-language.')),
            Q('m3', L('Con quale licenza sono rilasciati molti modelli Qwen?', 'Under which licence are many Qwen models released?'), ['Apache 2.0', L('Nessuna', 'None'), 'GPL', L('Solo uso accademico', 'Academic use only')], 0, L('Controlla sempre la scheda del singolo modello.', 'Always check the individual model card.')),
        ], task('text', L('Abbina ogni variante al suo uso, una per riga come `variante: uso`. Varianti: `Qwen-Coder`, `Qwen-VL`. Usi: codice, immagini.', 'Match each variant to its use, one per line as `variant: use`. Variants: `Qwen-Coder`, `Qwen-VL`. Uses: code, images.'), '', [re('Qwen-Coder\s*:\s*(codice|code)', L('Coder per il codice', 'Coder for code')), re('Qwen-VL\s*:\s*(immagini|images)', L('VL per le immagini', 'VL for images'))], '', "Qwen-Coder: codice\nQwen-VL: immagini"), [
            dMatch(pMatch(), [['Alibaba Cloud', L('L’azienda', 'The company')], ['Qwen-Coder', L('Codice', 'Code')], ['Qwen-VL', L('Immagini e documenti', 'Images and documents')], ['Apache 2.0', L('Licenza permissiva', 'Permissive licence')]], L('La famiglia Qwen.', 'The Qwen family.')),
            dFlash(pFlash(), [[L('Esistono modelli Qwen molto piccoli', 'There are very small Qwen models'), true], [L('Qwen è sviluppato da Google', 'Qwen is developed by Google'), false], [L('Qwen ha varianti per il codice', 'Qwen has code variants'), true], [L('Nessun modello Qwen è scaricabile', 'No Qwen model is downloadable'), false]], L('Tante dimensioni, tanti usi.', 'Many sizes, many uses.')),
        ]),
        lesson('chat-local', L('Qwen Chat e uso in locale', 'Qwen Chat and local use'), [
            P('**Qwen Chat** (anche come app **Qwen**) è l’assistente web e mobile di Alibaba basato sui modelli Qwen: conversazione, ricerca, analisi di file e immagini, generazione di immagini. Come per DeepSeek, valuta dove vengono trattati i dati prima di inserire informazioni riservate.', '**Qwen Chat** (also as the **Qwen** app) is Alibaba’s web and mobile assistant based on Qwen models: conversation, search, file and image analysis, image generation. As with DeepSeek, consider where data is processed before entering confidential information.'),
            C('ollama run qwen3            # modello Qwen in locale
ollama run qwen2.5-coder    # variante per il codice', 'bash'),
            P('Grazie ai pesi aperti puoi far girare Qwen **in locale** con Ollama, LM Studio o llama.cpp: i dati restano sul tuo computer. I modelli piccoli e medi sono una scelta molto popolare per assistenti di programmazione locali e per il fine-tuning.', 'Thanks to the open weights you can run Qwen **locally** with Ollama, LM Studio or llama.cpp: data stays on your computer. Small and medium models are a very popular choice for local coding assistants and for fine-tuning.'),
        ], [
            Q('c1', L('Come usi Qwen senza inviare dati online?', 'How do you use Qwen without sending data online?'), [L('In locale con i pesi aperti', 'Locally with the open weights'), L('Solo con Qwen Chat', 'Only with Qwen Chat'), L('Non si può', 'You cannot'), L('Via email', 'Via email')], 0, L('Ollama, LM Studio, llama.cpp.', 'Ollama, LM Studio, llama.cpp.')),
            Q('c2', L('Prima di usare Qwen Chat con dati riservati…', 'Before using Qwen Chat with confidential data…'), [L('Valuta dove vengono trattati i dati', 'Consider where data is processed'), L('Non serve pensarci', 'No need to think'), L('Spegni lo schermo', 'Turn off the screen'), L('Cambia browser', 'Change browser')], 0, L('Come per ogni servizio.', 'As with any service.')),
            Q('c3', L('Quale comando avvia la variante per il codice?', 'Which command runs the code variant?'), ['ollama run qwen2.5-coder', 'ollama code qwen', 'qwen --code', 'pip install coder'], 0, L('Utile come assistente locale.', 'Handy as a local assistant.')),
        ], task('bash', L('Scrivi il comando per avviare in locale il modello `qwen3` con Ollama.', 'Write the command to run the `qwen3` model locally with Ollama.'), '', [re('ollama\s+(run|pull)\s+qwen3', L('Usi Ollama con qwen3', 'You use Ollama with qwen3'))], '', 'ollama run qwen3'), [
            dMatch(pMatch(), [['Qwen Chat', L('Assistente web', 'Web assistant')], ['Ollama', L('Uso in locale', 'Local use')], ['qwen2.5-coder', L('Assistente di codice', 'Code assistant')], [L('Pesi aperti', 'Open weights'), L('I dati restano qui', 'Data stays here')]], L('Online o locale.', 'Online or local.')),
            dFlash(pFlash(), [[L('Qwen si può usare in locale', 'Qwen can be used locally'), true], [L('In locale i dati vanno ad Alibaba', 'Locally data goes to Alibaba'), false], [L('Qwen Chat analizza anche immagini', 'Qwen Chat also analyses images'), true], [L('Qwen non ha varianti per il codice', 'Qwen has no code variants'), false]], L('Scegliere dove girano i dati.', 'Choosing where data goes.')),
        ]),
        lesson('api', L('L’API', 'The API'), [
            P('I modelli Qwen si usano via API su **Alibaba Cloud Model Studio**, con un’interfaccia **compatibile con OpenAI**: lo stesso SDK con un `base_url` diverso. Esiste anche un endpoint internazionale per chi non è in Cina.', 'Qwen models are used via API on **Alibaba Cloud Model Studio**, with an **OpenAI-compatible** interface: the same SDK with a different `base_url`. There is also an international endpoint for those outside China.'),
            C('import os
from openai import OpenAI

client = OpenAI(
    api_key=os.environ["DASHSCOPE_API_KEY"],
    base_url="https://dashscope-intl.aliyuncs.com/compatible-mode/v1",
)
r = client.chat.completions.create(
    model="qwen-plus",
    messages=[{"role": "user", "content": "Traduci in cinese: buongiorno"}],
)
print(r.choices[0].message.content)', 'python'),
            P('Molti fornitori cloud e piattaforme di inferenza ospitano anche i modelli Qwen aperti: puoi scegliere quello nella giurisdizione che ti serve. Controlla sempre i nomi dei modelli disponibili e i prezzi nella documentazione.', 'Many cloud providers and inference platforms also host the open Qwen models: you can choose the one in the jurisdiction you need. Always check available model names and prices in the documentation.'),
            P('Attenzione alla **regione**: in Model Studio la chiave appartiene alla regione in cui l’hai creata (per esempio quella internazionale o quella di Pechino) e funziona solo con il `base_url` di quella regione. Se li mescoli ottieni un errore di autenticazione, anche con una chiave valida. La regione decide anche dove vengono elaborati i dati: sceglila prima di creare la chiave.', 'Watch out for the **region**: in Model Studio a key belongs to the region where you created it (for example the international one or the Beijing one) and works only with that region’s `base_url`. Mix them up and you get an authentication error, even with a valid key. The region also decides where the data is processed: choose it before creating the key.'),
        ], [
            Q('a1', L('L’API di Qwen è compatibile con quale SDK?', 'Which SDK is the Qwen API compatible with?'), ['OpenAI', 'Gmail', 'Excel', L('Nessuno', 'None')], 0, L('Cambia il base_url.', 'Change the base_url.')),
            Q('a2', L('Su quale piattaforma si usa l’API ufficiale?', 'Which platform hosts the official API?'), ['Alibaba Cloud Model Studio', 'GitHub', 'YouTube', 'Hugging Face Spaces'], 0, L('Con endpoint internazionale.', 'With an international endpoint.')),
            Q('a3', L('Perché scegliere un altro fornitore che ospita Qwen?', 'Why choose another provider hosting Qwen?'), [L('Per la giurisdizione dei dati', 'For data jurisdiction'), L('Per il colore', 'For the colour'), L('Non ha senso', 'It makes no sense'), L('Per avere meno modelli', 'To have fewer models')], 0, L('I pesi aperti lo permettono.', 'Open weights allow it.')),
        ], task('python', L('Crea il client `OpenAI` con la chiave da `os.environ["DASHSCOPE_API_KEY"]` e `base_url="https://dashscope-intl.aliyuncs.com/compatible-mode/v1"`, poi chiama il modello `qwen-plus`.', 'Create the `OpenAI` client with the key from `os.environ["DASHSCOPE_API_KEY"]` and `base_url="https://dashscope-intl.aliyuncs.com/compatible-mode/v1"`, then call the `qwen-plus` model.'), "import os\nfrom openai import OpenAI\n", [re('api_key\s*=\s*os\.environ\s*\[\s*["\']DASHSCOPE_API_KEY["\']\s*\]', L('La chiave', 'The key')), re('base_url\s*=\s*["\']https://dashscope-intl\.aliyuncs\.com/compatible-mode/v1["\']', L('L’indirizzo', 'The address')), re('model\s*=\s*["\']qwen-plus["\']', L('Il modello', 'The model'))], '', "client = OpenAI(api_key=os.environ[\"DASHSCOPE_API_KEY\"], base_url=\"https://dashscope-intl.aliyuncs.com/compatible-mode/v1\")\nr = client.chat.completions.create(model=\"qwen-plus\", messages=[{\"role\": \"user\", \"content\": \"Ciao\"}])"), [
            dMatch(pMatch(), [['Model Studio', L('Piattaforma di Alibaba Cloud', 'Alibaba Cloud platform')], ['base_url', L('Indirizzo compatibile', 'Compatible address')], ['DASHSCOPE_API_KEY', L('La chiave', 'The key')], [L('Altri fornitori', 'Other providers'), L('Ospitano i modelli aperti', 'Host the open models')]], L('Qwen via API.', 'Qwen via API.')),
            dBlanks(pFill(), "client = OpenAI(api_key=chiave, ___=\"https://dashscope-intl.aliyuncs.com/compatible-mode/v1\")\nr = client.chat.___.create(model=\"qwen-plus\", messages=msgs)", ['base_url', 'completions'], ['url', 'messages', 'responses'], L('La chiamata compatibile.', 'The compatible call.')),
        ]),
        lesson('compare', L('Qwen e DeepSeek a confronto', 'Qwen and DeepSeek compared'), [
            P('Qwen e DeepSeek sono i due grandi laboratori cinesi di modelli aperti. Hanno punti in comune (pesi scaricabili, prestazioni alte, API economiche compatibili con OpenAI) e differenze: Qwen copre moltissime **dimensioni** e varianti (visione, audio, codice), DeepSeek si è distinto soprattutto per i grandi modelli **Mixture of Experts** e il ragionamento.', 'Qwen and DeepSeek are the two big Chinese labs of open models. They share traits (downloadable weights, high performance, cheap OpenAI-compatible APIs) and differ: Qwen covers a great many **sizes** and variants (vision, audio, code), DeepSeek stood out mainly for its large **Mixture of Experts** models and reasoning.'),
            P('Per entrambi valgono le stesse considerazioni: i servizi online trattano i dati secondo le loro informative; per dati riservati preferisci l’uso **in locale** o un fornitore di fiducia nella tua giurisdizione. E, come per ogni modello, verifica le risposte su temi delicati.', 'The same considerations apply to both: online services process data under their privacy policies; for confidential data prefer **local** use or a trusted provider in your jurisdiction. And, as with any model, verify answers on sensitive topics.'),
        ], [
            Q('q1', L('Che cosa hanno in comune Qwen e DeepSeek?', 'What do Qwen and DeepSeek have in common?'), [L('Pesi aperti e API compatibili con OpenAI', 'Open weights and OpenAI-compatible APIs'), L('Sono americani', 'They are American'), L('Funzionano solo offline', 'They only work offline'), L('Non hanno API', 'They have no API')], 0, L('Laboratori cinesi di modelli aperti.', 'Chinese labs of open models.')),
            Q('q2', L('Per che cosa si distingue Qwen?', 'What does Qwen stand out for?'), [L('Tantissime dimensioni e varianti', 'Very many sizes and variants'), L('Un solo modello', 'A single model'), L('Solo immagini', 'Only images'), L('Solo musica', 'Only music')], 0, L('Visione, audio, codice…', 'Vision, audio, code…')),
            Q('q3', L('Con dati riservati, quale scelta è più prudente?', 'With confidential data, which choice is most prudent?'), [L('Uso in locale o fornitore di fiducia', 'Local use or a trusted provider'), L('Il servizio online senza pensarci', 'The online service without thinking'), L('Pubblicarli', 'Publishing them'), L('Nessuna', 'None')], 0, L('Tu scegli dove vanno i dati.', 'You choose where data goes.')),
        ], task('text', L('Scrivi due righe con `-`: una cosa in comune tra Qwen e DeepSeek e una differenza.', 'Write two lines with `-`: one thing Qwen and DeepSeek share and one difference.'), "- \n- ", [re('^-\s*\S.{5,}\n-\s*\S.{5,}', L('Due righe', 'Two lines')), re('aper|open|pesi|weights|API|compatib', L('Un punto in comune', 'A shared point')), re('dimension|size|variant|Mixture|MoE|ragionament|reasoning|visione|vision', L('Una differenza', 'A difference'))], '', "- entrambi pubblicano pesi aperti con API compatibili con OpenAI\n- Qwen ha tantissime dimensioni e varianti, DeepSeek punta su grandi modelli Mixture of Experts"), [
            dMatch(pMatch(), [['Qwen', L('Molte dimensioni e varianti', 'Many sizes and variants')], ['DeepSeek', L('Grandi modelli MoE e ragionamento', 'Large MoE models and reasoning')], [L('Entrambi', 'Both'), L('Pesi aperti', 'Open weights')], [L('Dati riservati', 'Confidential data'), L('Meglio in locale', 'Better local')]], L('Due laboratori a confronto.', 'Two labs compared.')),
            dFlash(pFlash(), [[L('Qwen e DeepSeek offrono API compatibili con OpenAI', 'Qwen and DeepSeek offer OpenAI-compatible APIs'), true], [L('Nessuno dei due pubblica i pesi', 'Neither publishes weights'), false], [L('Per dati riservati conviene l’uso in locale', 'Local use suits confidential data'), true], [L('Le loro risposte non vanno mai verificate', 'Their answers never need checking'), false]], L('Scelte informate.', 'Informed choices.')),
        ]),
        lesson('coding', L('Qwen per il codice e gli strumenti', 'Qwen for code and tools'), [
            P('La famiglia Qwen comprende modelli specializzati nel **codice** (Qwen Coder), che completano funzioni, spiegano errori e scrivono test in molti linguaggi. Esistono in taglie diverse: le piccole si eseguono in locale accanto all’editor.', 'The Qwen family includes models specialised in **code** (Qwen Coder), which complete functions, explain errors and write tests in many languages. They come in several sizes: the small ones run locally next to the editor.'),
            P('I modelli Qwen supportano il **tool calling**: ricevono la descrizione di funzioni (nome, parametri in JSON Schema) e rispondono chiedendo di chiamarne una con certi argomenti. Il tuo programma esegue la funzione e rimanda il risultato.', 'Qwen models support **tool calling**: they receive function descriptions (name, parameters in JSON Schema) and answer by asking to call one with certain arguments. Your program runs the function and sends back the result.'),
            C('{
  "name": "meteo",
  "description": "Previsioni per una città",
  "parameters": {
    "type": "object",
    "properties": { "citta": { "type": "string" } },
    "required": ["citta"]
  }
}', 'json'),
            Fig(L(fig_qwen_tools('it'), fig_qwen_tools('en')),
                L('Due linee verticali, il tuo programma a sinistra e il modello a destra, con quattro frecce che si alternano: 1 il programma manda la domanda sul meteo di Trieste con l’elenco degli strumenti; 2 il modello chiede la chiamata meteo con citta Trieste; 3 il programma esegue la funzione e rimanda il risultato, temperatura 18; 4 il modello risponde con una frase.', 'Two vertical lines, your program on the left and the model on the right, with four alternating arrows: 1 the program sends the question about the weather in Trieste with the list of tools; 2 the model asks for the meteo call with citta Trieste; 3 the program runs the function and sends back the result, temperature 18; 4 the model answers with a sentence.'),
                L('Il modello scrive la chiamata, ma a eseguirla è sempre il tuo programma.', 'The model writes the call, but your program is always the one that runs it.')),
            Tip('Prima di eseguire una chiamata controlla gli argomenti: un nome di città è innocuo, un comando che cancella file no. Gestisci anche il caso in cui la funzione fallisce: rimanda al modello un messaggio d’errore chiaro, così può riprovare o spiegarlo all’utente.', 'Before running a call, check its arguments: a city name is harmless, a command that deletes files is not. Also handle the case where the function fails: send the model a clear error message, so it can try again or explain it to the user.'),
        ], [
            Q('qc1', L('A che cosa servono i modelli Qwen Coder?', 'What are Qwen Coder models for?'), [L('Scrivere e spiegare codice', 'Writing and explaining code'), L('Generare musica', 'Generating music'), L('Tradurre solo il cinese', 'Translating Chinese only'), L('Montare video', 'Editing video')], 0, L('Completano, spiegano, testano.', 'They complete, explain, test.')),
            Q('qc2', L('Nel tool calling, chi esegue la funzione?', 'In tool calling, who runs the function?'), [L('Il tuo programma', 'Your program'), L('Il modello', 'The model'), L('Il server di Qwen, sempre', 'Qwen’s server, always'), L('Nessuno', 'Nobody')], 0, L('Il modello chiede soltanto.', 'The model only asks.')),
            Q('qc3', L('Come si descrivono i parametri di uno strumento?', 'How are a tool’s parameters described?'), [L('Con JSON Schema', 'With JSON Schema'), L('Con un’immagine', 'With an image'), L('In un file Excel', 'In an Excel file'), L('Non si descrivono', 'They are not described')], 0, L('Tipo, proprietà, obbligatori.', 'Type, properties, required.')),
        ], task('json', L('Scrivi la descrizione JSON di uno strumento con `"name": "somma"` e il parametro obbligatorio `"a"` di tipo `number`.', 'Write the JSON description of a tool with `"name": "somma"` and the required parameter `"a"` of type `number`.'), '', [re('"name"\s*:\s*"somma"', L('Il nome è giusto', 'The name is right')), re('"a"\s*:\s*\{\s*"type"\s*:\s*"number"\s*\}', L('Il parametro è un numero', 'The parameter is a number')), re('"required"\s*:\s*\[\s*"a"\s*\]', L('Il parametro è obbligatorio', 'The parameter is required'))], '', "{\n  \"name\": \"somma\",\n  \"parameters\": {\n    \"type\": \"object\",\n    \"properties\": { \"a\": { \"type\": \"number\" } },\n    \"required\": [\"a\"]\n  }\n}"), [
            dOrder(pSort(), [L('Descrivi gli strumenti', 'Describe the tools'), L('Il modello chiede una chiamata', 'The model requests a call'), L('Il programma esegue la funzione', 'The program runs the function'), L('Il risultato torna al modello', 'The result goes back to the model'), L('Il modello risponde', 'The model answers')], L('Il giro del tool calling.', 'The tool-calling loop.')),
            dFlash(pFlash(), [[L('Qwen Coder è pensato per il codice', 'Qwen Coder is designed for code'), true], [L('Il modello esegue da solo le funzioni', 'The model runs functions by itself'), false], [L('I parametri si descrivono in JSON Schema', 'Parameters are described in JSON Schema'), true], [L('Esiste una sola taglia di Qwen', 'There is only one Qwen size'), false]], L('Codice e strumenti.', 'Code and tools.')),
        ]),
    ],
];
