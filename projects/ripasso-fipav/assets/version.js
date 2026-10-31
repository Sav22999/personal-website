/*
 * Portal versions and release notes (the first one is the current version).
 * To publish a new version, add an entry at the top of the list and update the ?v=
 * parameter in the <script>/<link> tags of index.html and admin.html and in assets/boot.js.
 * The release notes themselves are user-facing text and stay in Italian.
 */
window.APP_RELEASES = [
  {
    version: '3.3.2',
    date: '2026-10-31',
    title: 'Installazione solo su telefono e tablet',
    groups: [
      { title: 'Miglioramenti', items: ['La voce «Installa l’app» e la sezione «App sul telefono» del profilo compaiono solo su telefono e tablet, non sul computer, e non compaiono quando Fischietto è già aperto come app installata.'] },
    ],
  },
  {
    version: '3.3.1',
    date: '2026-10-31',
    title: 'Email per il cambio di ruolo',
    groups: [
      {
        title: 'Novità',
        items: [
          'Quando un amministratore cambia il ruolo di un utente (o passa il ruolo di master admin), l’utente riceve un’email con il nuovo ruolo e cosa può fare ora.',
        ],
      },
    ],
  },
  {
    version: '3.3.0',
    date: '2026-10-31',
    title: 'Email agli utenti',
    groups: [
      {
        title: 'Novità',
        items: [
          'Quando un account viene abilitato, l’utente riceve un’email di benvenuto con le indicazioni per accedere.',
          'Se una registrazione viene rifiutata, la persona riceve un’email che la avvisa.',
          'Dopo un reset della password, l’utente riceve via email la password temporanea, da cambiare al primo accesso.',
        ],
      },
      {
        title: 'Gestione',
        items: [
          'Nella sezione Utenti un avviso indica chi, tra gli utenti già attivi, non ha ancora ricevuto l’email di attivazione: puoi inviarla a tutti con un tocco o al singolo utente dal menu «⋯».',
          'Su ogni utente compare quando è stata inviata l’email di attivazione.',
          'Le email partono dalla casella noreply del sito, con un modello grafico dedicato a Fischietto.',
          'Corretto: scegliere una voce dal menu «⋯» di un utente apriva anche la sua panoramica.',
        ],
      },
    ],
  },
  {
    version: '3.2.1',
    date: '2026-10-30',
    title: 'Nuovo footer',
    groups: [
      { title: 'Miglioramenti', items: ['Il footer ora dice «Sviluppato con ♥ da Saverio Morelli».'] },
    ],
  },
  {
    version: '3.2.0',
    date: '2026-10-29',
    title: 'Panoramica del pannello più completa',
    groups: [
      {
        title: 'Gestione',
        items: [
          'Nuove schede nella Panoramica: attività di studio (utenti attivi, risposte date, quiz da finire), risposte corrette per fonte e riferimenti più difficili. Sono dati complessivi e anonimi.',
          'Novità della stagione: quante regole nuove o modificate, quante domande collegate e quali riferimenti non hanno ancora domande.',
          'Stato dei contenuti: riferimenti senza domande, domande disattivate o con riferimenti mancanti, con un tocco per aprire l’elenco già filtrato; distribuzione delle domande per difficoltà e tipo.',
          'Documenti (amministratori): documenti ufficiali e altri documenti utili, con l’avviso se un file manca sul server.',
          'Nuovo filtro «Con riferimenti mancanti» nell’elenco delle domande.',
        ],
      },
    ],
  },
  {
    version: '3.1.1',
    date: '2026-10-29',
    title: 'Elenchi a scorrimento continuo',
    groups: [
      {
        title: 'Miglioramenti',
        items: [
          'Gli elenchi dei riferimenti nel Ripasso e di domande e riferimenti nel pannello di gestione si caricano da soli mentre scorri: non serve più premere «Mostra altri».',
          'Le schede già aperte restano aperte mentre se ne caricano altre.',
        ],
      },
    ],
  },
  {
    version: '3.1.0',
    date: '2026-10-29',
    title: 'App installabile e panoramica utenti',
    groups: [
      {
        title: 'Novità',
        items: [
          'Fischietto si può installare come app sul telefono (Android e iPhone) e sul computer: dal menu dell’account o dalla pagina Il mio profilo.',
          'La versione del portale è visibile anche nella schermata di accesso.',
          'Statistiche: la percentuale è più distanziata dal numero, così non si confonde con il valore.',
        ],
      },
      {
        title: 'Gestione',
        items: [
          'Gli amministratori possono aprire la panoramica di ogni utente, in sola lettura: risposte date, corrette ed errate, risultati per fonte e riferimenti più sbagliati. Nessun dato personale.',
          'Su telefono le sezioni del pannello di gestione sono in una barra di navigazione in basso, come nel portale.',
        ],
      },
    ],
  },
  {
    version: '3.0.0',
    date: '2026-10-29',
    title: 'Nuovo nome, nuovo design, documenti ufficiali e profilo',
    groups: [
      {
        title: 'Novità',
        items: [
          'Il portale si chiama Fischietto, con una nuova icona.',
          'Interfaccia ridisegnata, più chiara e coerente, con tema chiaro, scuro o automatico.',
          'Ottimizzata per telefono e tablet: barra di navigazione in basso, pulsanti più grandi, finestre che si aprono dal basso.',
          'Nuova sezione Documenti: i PDF ufficiali di Regole di gioco, Casistica, Indicazioni tecniche, Normative federali e Vademecum da aprire o scaricare.',
          'Ogni riferimento normativo ha il link al documento da cui è tratto; da ogni documento, «Consulta» apre il Ripasso filtrato su quella fonte.',
          'Nuova pagina Il mio profilo: dati personali, password, cosa permette il tuo ruolo, progressi e uscita in un unico posto.',
          'Immagine del profilo da Gravatar, collegata alla tua email; se non c’è, compaiono le tue iniziali.',
          'Regole nuove o modificate: etichetta «Nuova» o «Modificata» con la stagione e una nota su cosa cambia; filtro «Novità» nel Ripasso e opzione «Solo novità della stagione» nel quiz. Già segnate le modifiche delle Normative federali 2026/27 e le Precisazioni 2026/27.',
          'Pagina Novità con le note di ogni versione (questa).',
        ],
      },
      {
        title: 'Quiz e statistiche',
        items: [
          'Il riepilogo «Il tuo quiz» mostra quante domande farai e da quali fonti, con l’opzione per salvare o meno le statistiche accanto a «Inizia».',
          'Per ogni fonte vedi quante domande sono disponibili.',
          'A fine quiz trovi l’elenco delle domande sbagliate o saltate, con il collegamento ai riferimenti da ripassare.',
          'Statistiche divise per fonte, con la percentuale di risposte corrette.',
          'I quiz sospesi sono raggruppati: vedi subito il più recente.',
        ],
      },
      {
        title: 'Account e sicurezza',
        items: [
          'Password più robuste: almeno 10 caratteri e tre tipi tra minuscole, maiuscole, numeri e simboli, con indicatore di robustezza.',
          'Dopo un reset della password, al primo accesso è obbligatorio sceglierne una nuova.',
          '«Ho dimenticato la password» indica come contattare il master admin.',
          'Tolte importazione ed esportazione dei dati utente: i progressi sono già salvati sul tuo account.',
        ],
      },
      {
        title: 'Per moderatori e amministratori',
        items: [
          'Pannello di gestione ridisegnato, con la nuova pagina Panoramica sulle cose da fare.',
          'Nuovi ruoli: master admin (uno solo), amministratore e moderatore contenuti. Solo il master admin gestisce gli amministratori.',
          'Il master admin può passare il proprio ruolo a un altro utente.',
          'Esportazione di domande e riferimenti spostata nel pannello; filtro dei riferimenti senza domande.',
          'Gli amministratori aggiungono, modificano, sostituiscono, riordinano ed eliminano gli «Altri documenti utili» (PDF) dal pannello.',
          'Nell’editor dei riferimenti si indica se la regola è nuova o modificata, in quale stagione e cosa cambia; filtro per trovarle nel pannello.',
        ],
      },
    ],
  },
  {
    version: '2.0.0',
    date: '2026-10',
    title: 'Versione web con account',
    groups: [
      {
        title: 'Novità',
        items: [
          'Il portale è online sul sito, con registrazione e accesso: un amministratore abilita i nuovi account.',
          'Statistiche, domande e riferimenti salvati, segnalazioni e quiz sospesi vengono salvati sul tuo account e li ritrovi su ogni dispositivo.',
          'Domande e riferimenti sono gestiti da un pannello dedicato, con le segnalazioni degli utenti.',
          'Importazione dei dati dalla versione precedente.',
        ],
      },
    ],
  },
  {
    version: '1.x',
    date: '',
    title: 'Prima versione',
    groups: [
      {
        items: [
          'Quiz a scelta multipla, vero/falso e a più risposte, con filtri per fonte e difficoltà e risposte mescolate.',
          'Riferimento normativo per ogni domanda, ripasso dei riferimenti e punti deboli.',
          'Domande e riferimenti salvati con note, segnalazioni, quiz sospesi e statistiche, salvati nel browser.',
        ],
      },
    ],
  },
];
window.APP_VERSION_LABEL = 'v' + window.APP_RELEASES[0].version;
