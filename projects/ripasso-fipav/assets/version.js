/*
 * Portal versions and release notes (the first one is the current version).
 * To publish a new version, add an entry at the top of the list and update the ?v=
 * parameter in the <script>/<link> tags of index.html and admin.html and in assets/boot.js.
 * The release notes themselves are user-facing text and stay in Italian.
 */
window.APP_RELEASES = [
  {
    version: '3.6.0',
    date: '2026-11-01',
    title: 'Simulazione: sanzioni, capitano in gioco e fallosità',
    groups: [
      {
        title: 'Novità',
        items: [
          'Gestione › Segnalazioni: per le segnalazioni di un’azione della simulazione c’è «Rivedi azione», che riapre la registrazione (con rallenty, vista dall’alto, momento del fallo e del fischio) insieme alla decisione presa e al testo della segnalazione.',
          'Nuova impostazione «Posizioni delle squadre»: Semplici (ognuno nella sua zona) o Realistiche, con formazioni di ricezione a tre, palleggiatore nascosto a rete, giocatori sovrapposti al limite consentito e cambi di posizione dopo il servizio.',
          'Sanzioni con il fischio: fischi, scegli il provvedimento e poi tocchi direttamente sul campo chi lo riceve (giocatore in campo o in panchina, allenatore), con un alone al passaggio del mouse; oppure la squadra tramite il capitano in gioco. Se il destinatario è sbagliato te lo segnala.',
          'Anche dopo un fischio durante l’azione puoi scegliere «Cartellino o altra sanzione».',
          'I comportamenti scorretti non aprono più da soli la finestra: va fischiato (se non fischi, dopo qualche secondo si apre con la segnalazione del ritardo).',
          'Capitano in gioco: se il capitano non è in campo puoi chiederlo con un fischio (dal referto o mentre scegli a chi dare la sanzione); viene indicato in campo.',
          'Nuove impostazioni «Fallosità dei giocatori» (falli durante il gioco: Nessuna, Bassa, Media, Alta) e «Condotta scorretta» (comportamenti da sanzionare: Auto, che cala quando decidi bene e cresce con il malcontento dopo i tuoi errori, Nessuna, Bassa, Media, Alta).',
          'Nella registrazione dell’azione c’è anche il momento del fischio (in blu), oltre a quello del fallo (in rosso).',
          'Gestione › Segnalazioni: quiz e simulazione sono separati; le segnalazioni della simulazione riportano tutti i dati dell’azione (formazioni, falli con i tempi, fischio, esito, decisioni, opzioni) con «Copia i dati dell’azione», e le chiudi o riapri tu.',
          'Con te 1° arbitro, il 2° arbitro motiva le sue decisioni e usa la segnaletica ufficiale (riquadro in basso a destra); se respinge una richiesta che merita una sanzione per ritardo, la sanzione la decidi tu. Con te 2° arbitro scegli solo se autorizzare o respingere: la sanzione per ritardo la dà il 1°.',
          'Collaborazione «palla toccata»: quando la palla esce dopo un tocco del muro, il 2° arbitro unisce le punte delle dita delle due mani a tetto, palmi verso il petto. Da 2° arbitro puoi farlo tu da «Segnala al 1°»: il 1° a volte non vede il tocco.',
          'Collaborazione: il 2° arbitro ti segnala in modo discreto, al petto e con le dita visibili sul suo personaggio, doppio tocco e quattro tocchi; fa un passettino verso la squadra in fallo se è quella dall’altra parte, resta fermo se è quella dal suo lato. Puoi anche ignorarlo.',
          'Da 2° arbitro puoi fare tu il segnale di collaborazione al 1° («Segnala al 1°» durante l’azione): il 1° a volte non vede doppio tocco o quattro tocchi, e il tuo segnale lo aiuta. Anche questi segnali entrano nelle statistiche.',
          'Da arbitro unico (e da 2° arbitro) sostituzioni e tempi di riposo vanno fischiati: la richiesta compare in campo e decidi dopo il fischio, con la segnaletica.',
          'Puoi fischiare anche prima dell’autorizzazione del servizio o tra le azioni: sanzione, richiesta del capitano in gioco, oppure niente.',
          'Tempo di gioco e secondi dall’autorizzazione del servizio (in rosso oltre gli 8) in alto a sinistra.',
          'Nuove opzioni (spente di default): attendere i 30 secondi del tempo di riposo, con conto alla rovescia; animazione di sostituzioni e rimpiazzi del Libero.',
          'Nuovo pallone da pallavolo giallo e blu a pannelli curvi.',
          'Segnaletica «dal vivo»: l’altro arbitro esegue i gesti sul suo personaggio in campo (fischio, servizio, fallo, sanzioni). Il 2° arbitro ripete il gesto della squadra al servizio solo dopo un suo fischio e non fa mai il gesto di autorizzazione; con te 2° arbitro, dopo un tuo fischio il 1° indica la squadra al servizio.',
          'Sanzioni con cartellino: scegli se comunicarle direttamente al giocatore o tramite il capitano in gioco.',
          'La richiesta del capitano in gioco ha la sua segnaletica: l’indice tocca il petto all’altezza del numero.',
          'Nuova opzione «Dopo una decisione sbagliata»: «Si corregge» (il punteggio segue la decisione giusta) oppure «Vale la mia decisione» (come in partita: punto, sanzioni e richieste seguono quello che hai deciso).',
        ],
      },
      {
        title: 'Correzioni',
        items: [
          'Anche per l’avvertimento verbale scegli se comunicarlo direttamente al giocatore o tramite il capitano in gioco: quello corretto è tramite il capitano, altrimenti la decisione risulta sbagliata.',
          'Il 2° arbitro, dopo un suo fischio, si sposta dal lato della squadra in fallo e solo dopo fa la segnaletica (anche quando sei tu il 2° arbitro).',
          'Gli arbitri tengono sempre il fischietto in bocca: il fischio non ha più il gesto della mano alla bocca.',
          'Nella finestra della sanzione è tornato «Chiedi il capitano in gioco»: fischi, fai il gesto e poi torni alla sanzione.',
          'Sanzione per ritardo: il cartellino è appoggiato sul polso del braccio alzato, a metà cartellino.',
          'Durante il tempo di riposo i giocatori delle due squadre escono dal campo e si radunano attorno all’allenatore, vicino alla propria panchina; poi rientrano per il servizio.',
          'Sostituzioni e tempi di riposo: il 2° arbitro prima si sposta dal lato della squadra che ha fatto la richiesta, poi fa la segnaletica.',
          'Finestra della sanzione: chi riceve la sanzione si sceglie toccandolo direttamente sul campo.',
          'Fischiare senza fallo è sempre un errore: l’azione si ripete con la segnaletica del doppio fallo.',
          'Tocco della rete: la mano ha il palmo rivolto verso la rete.',
          '«Toccata dal muro e fuori» diventa «Palla toccata»: vale per qualunque tocco, non solo del muro.',
          'Fallo di posizione: l’indice è rivolto verso il pavimento, con il movimento circolare.',
          'Ritardo di gioco (Indicazioni tecniche 4.5): braccio alzato dal lato della squadra sanzionata, leggermente piegato verso il centro, dorso verso il segnapunti; l’altra mano porta il cartellino sul suo polso.',
          'Corretto un errore della pagina iniziale della simulazione: il pulsante «Fischia» e la sfumatura del campo comparivano sopra le impostazioni.',
          'Se fischi quando la palla è già a terra (o fuori), conta come fischio di fine azione e non più come fischio senza fallo.',
          'L’antenna oscilla insieme alla rete quando viene colpita.',
          'Competenze riviste su Regole di gioco, Casistica (13.1) e Indicazioni tecniche (4.3.14–4.3.22): di entrambi gli arbitri l’invasione oltre la linea centrale o sotto la rete, l’attacco irregolare (difensore e Libero), il muro irregolare, attacco e muro sul servizio; solo del 1° l’invasione sopra la rete, la palla passata sotto la rete e l’attacco su palleggio del Libero; l’antenna del 1°, e del 2° solo se dal suo lato.',
          'Muro di un difensore: è fallo solo il muro effettivo (il difensore tocca la palla a muro); ora il difensore arriva da dietro e mura da solo, e c’è anche il caso del semplice tentativo di muro, che non è fallo.',
          'Invasione oltre la linea centrale: il giocatore non attraversa più la rete, che si muove al contatto.',
          'Le sostituzioni e i tempi di riposo irregolari li segnala il segnapunti a voce al 2° arbitro (è lui a controllare il referto).',
          'Condotte scorrette più varie e realistiche, dai casi usati nei corsi arbitri: proteste e frasi (lieve condotta, maleducata, offensiva), minacce («Ti aspetto fuori!», squalifica), anche da chi è in panchina.',
          'Il 2° arbitro si sposta meno lungo la linea laterale.',
          'In campo il braccio d’attacco si muove (caricamento, colpo, accompagnamento) mentre a muro le braccia restano ferme.',
          'Cartellini: la correzione segue le Indicazioni tecniche (3.6.1 e 3.6.2): avvertimento verbale sempre tramite il capitano in gioco; cartellini mostrati direttamente al giocatore in campo, tramite il capitano in gioco per chi non è in campo.',
          'Da 2° arbitro la tua segnaletica è indicata come quella del 2° arbitro (non del 1°).',
          'Il Libero che aspetta di rientrare non compare più due volte (in campo e in zona di rimpiazzo).',
        ],
      },
    ],
  },
  {
    version: '3.5.0',
    date: '2026-11-01',
    title: 'Simulazione: nuovi falli di rete, muro e attacco',
    groups: [
      {
        title: 'Novità',
        items: [
          'Nuove situazioni in partita: invasione aerea a muro, palla passata sotto la rete, attacco sull’antenna, attacco sul servizio avversario, muro sul servizio avversario, muro di un difensore.',
          'Nuova segnaletica: invasione aerea (avambraccio orizzontale davanti al petto, palmo in basso) e fallo di muro (braccia alzate in verticale, palmi in avanti).',
          'Dopo ogni decisione compare la segnaletica che hai fatto (anche se sbagliata) con «Mostra dettagli» per aprire il riepilogo; tra le impostazioni puoi scegliere di aprire sempre il riepilogo completo.',
          'Nel riepilogo, se la decisione è sbagliata, c’è anche la tua segnaletica su sfondo rosso, accanto a quella corretta.',
          'Cronologia: tutte le azioni e le decisioni della partita, con chi le ha prese (1° o 2° arbitro, oppure tu). Si apre dalla barra in alto (tasto C) e anche dalle finestre di decisione.',
          'Sotto il punteggio: tempi di riposo usati, sostituzioni usate su 8 e sanzioni di ciascuna squadra.',
          '«Rivedi l’azione»: la registrazione dell’ultima azione in una finestra, con barra per scorrere, rallenty, vista dall’alto e il momento del fallo evidenziato.',
          'Dalla cronologia (anche a fine set) ogni voce ha «▶ Rivedi» per riguardarla a posteriori, anche quelle gestite dall’altro arbitro (azioni, tempi, sostituzioni, Libero, condotta).',
        ],
      },
      {
        title: 'Miglioramenti',
        items: [
          'Grafica dei personaggi rinnovata: corpo con spalle e vita, gambe con ginocchiere e calzettoni, scarpe, braccia con maniche e mani, collo, orecchie e acconciature diverse; arbitri con polo e colletto, pantaloni lunghi e scarpe nere; allenatori e segnapunti con la loro divisa; le riserve sono sedute in panchina.',
          'Il capitano ha la lineetta sotto il numero ben visibile (le maglie non hanno più la fascia orizzontale che la confondeva).',
          'Mani della segnaletica ridisegnate: palmo, dita, pollice, pugno, dita unite o divaricate, dorso con le nocche.',
          'Richiesta impropria rifatta: braccio teso verso la squadra ma più in basso della spalla, palmo verso l’esterno e dorso verso il campo.',
          'Tempo di riposo ridisegnato: la T è più leggibile, davanti al petto.',
          'Mentre decidi, Referto e Cronologia si aprono come pannelli laterali sopra la finestra di decisione; «Vista dall’alto» nasconde la finestra per guardare il campo, con «Torna alla decisione».',
          'Il 1° arbitro sul seggiolone si sposta molto meno: solo una leggera inclinazione verso la squadra in attacco.',
          'Attacco irregolare rifatto: braccio teso in alto che, con un unico movimento, si piega e «cade» verso il lato opposto, finendo sopra la testa col palmo in basso.',
        ],
      },
    ],
  },
  {
    version: '3.4.2',
    date: '2026-10-31',
    title: 'Simulazione: segnaletica e assegnazione del punto',
    groups: [
      {
        title: 'Miglioramenti',
        items: [
          'La segnaletica ufficiale è mostrata a specchio, con sotto la squadra che hai a sinistra e quella a destra.',
          'Per assegnare il punto tocchi direttamente la metà campo della squadra che lo vince (anche con i tasti A e B).',
          'Il fischio ha sempre lo stesso suono, come in partita.',
          'Autorizzazione al servizio rifatta: braccio teso verso la squadra al servizio, fischio, poi un’unica piegatura del braccio all’altezza della spalla, dorso verso il campo e palmo sulla spalla opposta.',
          'Palla fuori rifatta: braccia piegate a 90°, avambracci verticali, palmi verso il campo e dita unite.',
          'Rifatti anche doppio fallo (braccia tese verso il segnapunti, pollici alzati), tempo di riposo (T con una mano verticale e l’altra sopra col palmo in basso), tocco della rete (braccio teso dritto verso il segnapunti all’altezza della spalla), sostituzione (un giro completo dei pugni) e palla toccata (mano in alto, l’altra che sfiora le dita una volta da dietro in avanti).',
          'A fine set, la sezione «Segnaletica della partita» permette di rivedere il gesto corretto di ogni decisione (prima quelle sbagliate).',
        ],
      },
    ],
  },
  {
    version: '3.4.1',
    date: '2026-10-31',
    title: 'Simulazione nella barra in alto',
    groups: [
      { title: 'Miglioramenti', items: ['Per gli amministratori la Simulazione partita ha un pulsante nella barra in alto, accanto a Gestione.'] },
    ],
  },
  {
    version: '3.4.0',
    date: '2026-10-31',
    title: 'Simulazione partita (beta, solo amministratori)',
    groups: [
      {
        title: 'Novità',
        items: [
          'Simulazione partita: arbitra una partita animata in tempo reale come 1° arbitro (vista dal seggiolone), 2° arbitro (vista sotto la rete, lato segnapunti) o arbitro unico. Si apre dal menu dell’account.',
          'Autorizzi il servizio, fischi quando vedi un fallo (anche per errore: il fischio ferma sempre l’azione) e scegli la decisione. Tra le azioni gestisci tempi di riposo, sostituzioni, rimpiazzi del Libero e condotta, con il referto sempre consultabile.',
          'Situazioni da regolamento: falli di posizione, servizio (piede sulla linea, 8 secondi, servizio prima del fischio, in rete), quattro tocchi, palla trattenuta, doppio tocco, rete, linea centrale, attacchi irregolari di difensori e Libero, palla dentro, fuori e toccata, sostituzioni irregolari, richieste improprie, sanzioni. Ci sono anche situazioni “trappola” in cui la decisione giusta è non fischiare.',
          'Aggiornata alle sperimentazioni FIPAV 2026/27: squadra al servizio libera di posizionarsi, otto sostituzioni per set, doppio contatto in palleggio, nessun fischio sui servizi senza tocchi.',
          'Dopo ogni decisione: esito con la spiegazione, i riferimenti normativi e l’animazione della segnaletica ufficiale corretta, nell’ordine giusto per il 1° o il 2° arbitro.',
          'Pulsante Aiuto, pausa, vista dall’alto al volo, rallenty e punto d’impatto (facoltativi) per tocchi e palle sulla linea.',
          'L’arbitro si sposta: automaticamente (1° verso la squadra in attacco, 2° verso la squadra in ricezione) o con le frecce ← → e con i comandi a schermo sul telefono.',
          'Statistiche per tipo di situazione e pulsante «Segnala» per correggere una situazione: le segnalazioni arrivano nel pannello di gestione.',
        ],
      },
    ],
  },
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
