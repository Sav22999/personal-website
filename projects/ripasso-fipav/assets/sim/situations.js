/* Fischietto · Match simulation: catalogue of situations.
 * For each one: category, what to look at (help), the correct decision explained, and the references
 * (codes of the portal's references, shown and linked in the feedback). Reports use the code "SIM-<id>".
 * The texts are user-facing and stay in Italian.
 */
(function () {
  'use strict';

  const CATS = {
    servizio: 'Servizio',
    posizione: 'Falli di posizione',
    tocco: 'Tocchi di palla',
    rete: 'Rete e linea centrale',
    attacco: 'Attacchi irregolari',
    muro: 'Falli di muro',
    esito: 'Palla dentro, fuori, toccata',
    richieste: 'Tempi e sostituzioni',
    libero: 'Libero',
    condotta: 'Condotta e sanzioni',
    fischio: 'Fischi senza fallo',
  };

  const S = {
    // ---------- Service ----------
    serve_foot: {
      title: 'Piede sulla linea al servizio', cat: 'servizio',
      help: 'Al momento del colpo di servizio guarda i piedi del giocatore al servizio: non deve toccare il campo, linea di fondo compresa.',
      solution: 'Fallo del giocatore al servizio: punto e servizio alla squadra avversaria.',
      refs: ['RG-12.4.3', 'RG-12.6.1'],
    },
    serve_8s: {
      title: 'Servizio oltre gli 8 secondi', cat: 'servizio',
      help: 'Dal tuo fischio di autorizzazione il giocatore ha 8 secondi per colpire la palla: conta mentalmente e fischia allo scadere.',
      solution: 'Fallo del giocatore al servizio (ritardo oltre gli 8"): punto e servizio alla squadra avversaria.',
      refs: ['RG-12.4.4', 'RG-12.6.1'],
    },
    serve_net: {
      title: 'Servizio che non supera la rete', cat: 'servizio',
      help: 'Segui la palla di servizio: se impatta la rete e non la supera, fischia appena sei certo che non passerà.',
      solution: 'Servizio falloso: punto e servizio alla squadra avversaria.',
      refs: ['RG-12.6.2', 'CAS-12.4'],
    },
    serve_early: {
      title: 'Servizio prima del fischio', cat: 'servizio', mode: '1',
      help: 'Il servizio può partire solo dopo il tuo fischio di autorizzazione: se il giocatore serve prima, fischia.',
      solution: 'Il servizio eseguito prima del fischio è annullato e si ripete: nessun punto.',
      refs: ['RG-12.4.5', 'RG-12.3'],
    },
    // ---------- Positions ----------
    pos_serving: {
      title: 'Squadra al servizio fuori dall’ordine di rotazione', cat: 'posizione', trap: true,
      help: 'Con la sperimentazione FIPAV 2026/27 solo la squadra in ricezione deve rispettare l’ordine di rotazione al colpo di servizio: la squadra al servizio può posizionarsi liberamente (dentro il proprio campo).',
      solution: 'Nessun fallo: nella stagione 2026/27 i giocatori della squadra al servizio sono liberi di occupare qualsiasi posizione al momento del colpo di servizio. Il gioco doveva proseguire.',
      refs: ['NF-4.2', 'NF-PR.1', 'RG-7.4.3'],
    },
    pos_receiving: {
      title: 'Fallo di posizione della squadra in ricezione', cat: 'posizione',
      help: 'Al colpo di servizio controlla la squadra in ricezione: ogni difensore più lontano dalla rete del suo avanti corrispondente (I-II, VI-III, V-IV) e l’ordine destra-centro-sinistra in ogni linea. Nel 2026/27 i giocatori in ricezione possono spostarsi solo dal primo movimento del giocatore al servizio.',
      solution: 'Fallo di posizione della squadra in ricezione: punto e servizio alla squadra al servizio; le posizioni vanno corrette. È competenza del 2° arbitro.',
      refs: ['RG-7.4.3', 'RG-7.5.1', 'RG-7.5.4', 'NF-4.2', 'RG-24.3.2'],
    },
    // ---------- Ball handling ----------
    four_hits: {
      title: 'Quattro tocchi', cat: 'tocco',
      help: 'Conta i tocchi di ciascuna squadra: al massimo tre per rinviare la palla (il tocco di muro non conta).',
      solution: 'Quattro tocchi: punto e servizio alla squadra avversaria.',
      refs: ['RG-9.1', 'RG-9.3.1'],
    },
    block_plus_three: {
      title: 'Tre tocchi dopo il tocco del muro', cat: 'tocco', trap: true,
      help: 'Dopo un tocco del muro la squadra ha di nuovo diritto a tre tocchi: il contatto del muro non si conta.',
      solution: 'Nessun fallo: dopo il tocco del muro la squadra può usare tre tocchi. Il gioco doveva proseguire.',
      refs: ['RG-14.4.1', 'RG-9.1'],
    },
    catch: {
      title: 'Palla trattenuta', cat: 'tocco', aid: true,
      help: 'Osserva il palleggio: la palla deve rimbalzare dalle mani, non essere fermata e poi lanciata.',
      solution: 'Palla trattenuta: punto e servizio alla squadra avversaria.',
      refs: ['RG-9.2.2', 'RG-9.3.3'],
    },
    double_contact: {
      title: 'Doppio tocco in palleggio', cat: 'tocco', aid: true,
      help: 'Nel 2026/27, sul secondo tocco in palleggio verso un compagno, i contatti consecutivi sono permessi se avvengono in un’unica azione; restano fallo i contatti chiaramente separati (e il palleggio che diventa attacco).',
      solution: 'Doppio tocco: i due contatti sono chiaramente separati (la palla si stacca dalle mani e viene toccata di nuovo). Punto e servizio alla squadra avversaria.',
      refs: ['RG-9.3.4', 'NF-4.1', 'RG-9.2.3'],
    },
    double_first: {
      title: 'Doppio contatto al primo tocco', cat: 'tocco', trap: true, aid: true,
      help: 'Al primo tocco di squadra la palla può toccare più parti del corpo in successione, se avviene in un’unica azione.',
      solution: 'Nessun fallo: al primo tocco di squadra i contatti consecutivi in un’unica azione sono permessi. Il gioco doveva proseguire.',
      refs: ['RG-9.2.3', 'CAS-9.3'],
    },
    // ---------- Net and centre line ----------
    net_touch: {
      title: 'Tocco della rete', cat: 'rete',
      help: 'Durante l’azione di giocare la palla (salto, colpo e ricaduta) il contatto con la rete tra le antenne è fallo. Guarda la banda superiore: si muove?',
      solution: 'Fallo di contatto con la rete: punto e servizio alla squadra avversaria. Il 1° arbitro lo valuta soprattutto dal lato dell’attacco, il 2° dal lato del muro.',
      refs: ['RG-11.3.1', 'RG-23.3.2', 'RG-24.3.2', 'IT-4.3.17'],
    },
    centre_line: {
      title: 'Invasione oltre la linea centrale', cat: 'rete',
      help: 'Guarda i piedi dei giocatori a rete in ricaduta: una parte del piede deve restare sulla linea centrale o sopra di essa.',
      solution: 'Invasione: punto e servizio alla squadra avversaria. È competenza di entrambi gli arbitri: il 1° indica la linea centrale; se fischia il 2°, si sposta dal lato della squadra in fallo e fa il gesto, poi ripete l’assegnazione del servizio.',
      refs: ['RG-11.2.2', 'RG-24.3.2', 'IT-4.3.15'],
    },
    reach_beyond: {
      title: 'Invasione aerea a muro', cat: 'rete',
      help: 'Sulle alzate attaccate alla rete guarda le mani del muro: possono passare oltre la rete solo se non interferiscono con il gioco avversario prima o durante il colpo d’attacco.',
      solution: 'Fallo di muro (tocco della palla nello spazio avversario prima dell’attacco): punto e servizio alla squadra avversaria.',
      refs: ['RG-14.6.1', 'RG-11.1.1', 'RG-11.4.1', 'RG-14.3', 'IT-4.3.14'],
    },
    under_net: {
      title: 'Palla passata sotto la rete', cat: 'rete',
      help: 'Segui i palloni bassi vicino alla rete: la palla che attraversa completamente lo spazio inferiore sotto la rete è «fuori».',
      solution: 'Palla nello spazio inferiore: punto e servizio alla squadra avversaria. È competenza del solo 1° arbitro, che indica la linea centrale.',
      refs: ['RG-10.1.3', 'IT-4.3.16'],
    },
    antenna: {
      title: 'Attacco sull’antenna', cat: 'rete',
      help: 'Sugli attacchi dalle zone 4 e 2 guarda l’antenna: se la palla la tocca, è «fuori».',
      solution: 'Palla fuori (tocca l’antenna): punto e servizio alla squadra avversaria. Lo fischia il 1° arbitro; il 2° solo se l’antenna è dal suo lato.',
      refs: ['RG-8.4.3', 'RG-24.3.2', 'IT-4.3.18'],
    },
    // ---------- Attacks ----------
    attack_serve: {
      title: 'Attacco sul servizio avversario', cat: 'attacco',
      help: 'Sui servizi corti e alti guarda i giocatori a rete: non possono attaccare la palla di servizio quando è nella zona d’attacco e interamente sopra il bordo superiore della rete.',
      solution: 'Attacco irregolare (sulla palla di servizio): punto e servizio alla squadra avversaria.',
      refs: ['RG-13.3.4'],
    },
    block_serve: {
      title: 'Muro sul servizio avversario', cat: 'muro',
      help: 'Sui servizi corti e alti guarda i giocatori a rete: murare il servizio avversario è sempre fallo.',
      solution: 'Fallo di muro (muro sul servizio): punto e servizio alla squadra avversaria.',
      refs: ['RG-14.6.3'],
    },
    back_block_try: {
      title: 'Tentativo di muro di un difensore', cat: 'muro',
      help: 'Un difensore può saltare a rete: è fallo solo se il muro è effettivo, cioè se la palla lo tocca.',
      solution: 'Tentativo di muro senza tocco: nessun fallo, si giudica l’esito dell’attacco.',
      refs: ['RG-14.1.3', 'RG-14.6.2'],
    },
    back_block: {
      title: 'Muro di un difensore', cat: 'muro',
      help: 'Guarda chi salta a muro: un difensore (giocatore di seconda linea) o il Libero non può fare un muro effettivo, cioè toccare la palla a muro. Il solo tentativo, senza tocco, non è fallo.',
      solution: 'Fallo di muro: un difensore ha fatto muro effettivo (ha toccato la palla a muro). Punto e servizio alla squadra avversaria.',
      refs: ['RG-14.6.2', 'RG-14.1.3'],
    },
    back_attack: {
      title: 'Attacco del difensore dalla zona d’attacco', cat: 'attacco',
      help: 'Un difensore può attaccare sopra il bordo della rete solo se salta da dietro la linea d’attacco: guarda dove stacca.',
      solution: 'Attacco irregolare del difensore: punto e servizio alla squadra avversaria.',
      refs: ['RG-13.2.2', 'RG-13.3.3', 'RG-23.3.2'],
    },
    libero_attack: {
      title: 'Attacco del Libero sopra la rete', cat: 'attacco',
      help: 'Il Libero (maglia di colore diverso) non può completare un attacco con la palla interamente sopra il bordo superiore della rete, da nessuna posizione.',
      solution: 'Attacco irregolare del Libero: punto e servizio alla squadra avversaria.',
      refs: ['RG-19.3.1', 'RG-13.3.5'],
    },
    libero_set: {
      title: 'Attacco su alzata del Libero in zona d’attacco', cat: 'attacco',
      help: 'Se il Libero alza in palleggio (dita verso l’alto) stando nella zona d’attacco, il compagno non può attaccare con la palla interamente sopra il bordo della rete.',
      solution: 'Attacco irregolare: punto e servizio alla squadra avversaria. Da dietro la zona d’attacco l’alzata del Libero si può attaccare liberamente.',
      refs: ['RG-19.3.1', 'RG-13.3.6', 'RG-23.3.2'],
    },
    // ---------- Ball in / out ----------
    touched_out: {
      title: 'Palla toccata', cat: 'esito', aid: true,
      help: 'Guarda se la palla d’attacco cambia traiettoria sulle mani del muro prima di uscire.',
      solution: 'Palla toccata: la palla esce dopo il tocco di un giocatore della squadra che la riceve (qui il muro), quindi punto alla squadra avversaria, che l’aveva attaccata. Vale per qualunque tocco, non solo del muro.',
      refs: ['RG-8.4.1', 'RG-14.1.3', 'RG-14.6.4'],
    },
    no_fault_close: {
      title: 'Palla vicino alla linea', cat: 'esito', aid: true,
      help: 'La palla è dentro se una qualsiasi parte di essa tocca il campo, linee comprese.',
      solution: 'Decidi in base al punto di contatto: se tocca anche solo la linea, la palla è dentro.',
      refs: ['RG-8.3', 'RG-8.4.1', 'RG-1.3.2'],
    },
    rally_in: {
      title: 'Palla dentro', cat: 'esito',
      help: 'Al termine dell’azione fischia e indica il campo in cui la palla ha toccato terra.',
      solution: 'Palla dentro: punto alla squadra che l’ha inviata.',
      refs: ['RG-8.3', 'RG-6.1.1'],
    },
    rally_out: {
      title: 'Palla fuori', cat: 'esito',
      help: 'Al termine dell’azione fischia e indica se la palla è uscita.',
      solution: 'Palla fuori: punto alla squadra che non l’ha toccata per ultima.',
      refs: ['RG-8.4.1', 'RG-6.1.1'],
    },
    wrong_whistle: {
      title: 'Fischio senza fallo', cat: 'fischio',
      help: 'Si fischia la fine dell’azione solo quando si è certi che sia stato commesso un fallo e se ne è individuata la natura.',
      solution: 'Non c’era alcun fallo: il gioco doveva proseguire.',
      refs: ['RG-22.2.1'],
    },
    sanction_no_misconduct: {
      title: 'Sanzione senza comportamento scorretto', cat: 'condotta',
      help: 'Si sanziona solo un comportamento scorretto realmente avvenuto, scegliendo il provvedimento secondo la scala delle sanzioni e il destinatario giusto.',
      solution: 'Non c’era alcun comportamento da sanzionare.',
      refs: ['RG-21.1', 'RG-21.2'],
    },
    collab_ok: {
      title: 'Collaborazione con il 1° arbitro', cat: 'tocco',
      help: 'Da 2° arbitro, sui falli di tocco di competenza del 1° (doppio tocco, quattro tocchi, palla trattenuta) puoi aiutarlo con un segnale non ufficiale al petto e un passo verso la squadra in fallo.',
      solution: 'Segnale di collaborazione corretto: il 1° arbitro ha potuto fischiare il fallo.',
      refs: ['RG-24.2.2', 'IT-1.3', 'IT-4.4'],
    },
    collab_wrong: {
      title: 'Segnale di collaborazione sbagliato', cat: 'tocco',
      help: 'Il segnale di collaborazione va dato solo se si è certi del fallo e della squadra che l’ha commesso.',
      solution: 'Il segnale non corrispondeva a un fallo: il 1° arbitro lo ignora, ma un segnale sbagliato può indurlo in errore.',
      refs: ['RG-24.2.2', 'IT-1.3', 'IT-4.4'],
    },
    collab_missed: {
      title: 'Fallo sfuggito al 1° arbitro', cat: 'tocco',
      help: 'Il 1° arbitro può non vedere un fallo di tocco: il 2° può aiutarlo con il segnale di collaborazione.',
      solution: 'Il 1° arbitro non ha visto il fallo e il gioco è proseguito: con il segnale di collaborazione l’avresti aiutato.',
      refs: ['RG-24.2.2', 'IT-1.3', 'IT-4.4'],
    },
    // ---------- Requests ----------
    timeout_ok: {
      title: 'Richiesta di tempo di riposo', cat: 'richieste',
      help: 'Ogni squadra ha due tempi di riposo per set: controlla sul referto quanti ne ha già usati.',
      solution: 'Richiesta regolare: il tempo di riposo va autorizzato.',
      refs: ['RG-15.1', 'RG-15.4.1', 'RG-24.2.6'],
    },
    timeout_third: {
      title: 'Terzo tempo di riposo', cat: 'richieste',
      help: 'Controlla sul referto quanti tempi di riposo ha già chiesto la squadra nel set.',
      solution: 'Richiesta impropria (tempi di riposo esauriti): va respinta. La prima richiesta impropria della squadra nella gara non ha altre conseguenze; dalla seconda è un ritardo di gioco.',
      refs: ['RG-15.11.1', 'RG-15.11.2', 'RG-15.11.3', 'RG-16.1.4'],
    },
    sub_ok: {
      title: 'Sostituzione regolare', cat: 'richieste',
      help: 'Verifica sul referto: chi entra è registrato? Un titolare esce una sola volta e rientra solo al posto di chi l’ha sostituito; una riserva entra una sola volta, al posto di un titolare. Nel 2026/27 le sostituzioni sono otto per set.',
      solution: 'Sostituzione regolare: va autorizzata.',
      refs: ['RG-15.5.1', 'RG-15.6.1', 'RG-15.6.2', 'NF-4.3', 'RG-15.10.3'],
    },
    sub_not_on_sheet: {
      title: 'Sostituzione con un giocatore non a referto', cat: 'richieste',
      help: 'Controlla che il giocatore che entra sia iscritto nel referto della squadra.',
      solution: 'Sostituzione irregolare (giocatore non registrato a referto): va respinta e la squadra è sanzionata per ritardo di gioco (avvertimento se è il primo ritardo, penalizzazione dal secondo).',
      refs: ['RG-15.9.1', 'RG-16.1.3', 'RG-16.2.2', 'RG-16.2.3'],
    },
    sub_wrong_reentry: {
      title: 'Titolare che rientra al posto sbagliato', cat: 'richieste',
      help: 'Un titolare uscito può rientrare una sola volta e solo nella posizione che occupava, cioè al posto di chi lo aveva sostituito.',
      solution: 'Sostituzione irregolare: va respinta e la squadra è sanzionata per ritardo di gioco.',
      refs: ['RG-15.6.1', 'RG-15.9.1', 'RG-16.1.3', 'RG-16.2.2'],
    },
    sub_reserve_twice: {
      title: 'Riserva che entra per la seconda volta', cat: 'richieste',
      help: 'Una riserva entra in campo una sola volta per set e può essere sostituita solo dal titolare che aveva sostituito.',
      solution: 'Sostituzione irregolare: va respinta e la squadra è sanzionata per ritardo di gioco.',
      refs: ['RG-15.6.2', 'RG-15.9.1', 'RG-16.1.3', 'RG-16.2.2'],
    },
    sub_starter_twice: {
      title: 'Titolare che esce per la seconda volta', cat: 'richieste',
      help: 'Un titolare può uscire dal gioco una sola volta per set.',
      solution: 'Sostituzione irregolare: va respinta e la squadra è sanzionata per ritardo di gioco.',
      refs: ['RG-15.6.1', 'RG-15.9.1', 'RG-16.1.3', 'RG-16.2.2'],
    },
    sub_seventh: {
      title: 'Sostituzione oltre il limite', cat: 'richieste',
      help: 'Nella stagione 2026/27 ogni squadra ha otto sostituzioni per set (non sei): controlla il conteggio sul referto.',
      solution: 'Richiesta impropria (sostituzioni esaurite): va respinta senza sanzioni la prima volta nella gara; dalla seconda richiesta impropria è un ritardo di gioco.',
      refs: ['NF-4.3', 'RG-15.11.1', 'RG-15.11.2', 'CAS-15.3'],
    },
    // ---------- Libero ----------
    libero_regular: {
      title: 'Rimpiazzo del Libero', cat: 'libero',
      help: 'Il Libero rimpiazza un difensore attraverso la zona di rimpiazzo, a palla fuori gioco e prima del fischio di servizio, con un’azione completata tra due rimpiazzi.',
      solution: 'Rimpiazzo regolare: non va fermato.',
      refs: ['RG-19.3.2', 'RG-1.4.4'],
    },
    libero_no_rally: {
      title: 'Due rimpiazzi del Libero senza azione in mezzo', cat: 'libero',
      help: 'Tra due rimpiazzi del Libero deve esserci un’azione di gioco completata: controlla l’ultimo rimpiazzo sul referto.',
      solution: 'Rimpiazzo irregolare del Libero: va corretto e la squadra è sanzionata per ritardo di gioco.',
      refs: ['RG-19.3.2', 'RG-16.2.2', 'RG-16.2.3'],
    },
    // ---------- Conduct ----------
    conduct_minor_first: {
      title: 'Lieve condotta scorretta', cat: 'condotta',
      help: 'Le lievi condotte scorrette non si sanzionano: si prevengono, prima con un avvertimento verbale tramite il capitano, poi con il cartellino giallo.',
      solution: 'Prima lieve condotta scorretta della squadra: avvertimento verbale (fase 1), tramite il capitano in gioco.',
      refs: ['RG-21.1', 'RG-21.6', 'IT-3.6.1'],
    },
    conduct_minor_second: {
      title: 'Lieve condotta scorretta dopo l’avvertimento verbale', cat: 'condotta',
      help: 'Controlla sul referto se la squadra ha già ricevuto l’avvertimento verbale.',
      solution: 'La squadra è già stata avvertita verbalmente: avvertimento ufficiale con il cartellino giallo (fase 2). Non è una sanzione, ma viene registrato a referto.',
      refs: ['RG-21.1', 'RG-21.6', 'IT-3.3.1', 'IT-3.6.2'],
    },
    conduct_rude: {
      title: 'Condotta maleducata', cat: 'condotta',
      help: 'Un’azione contraria alle buone maniere o ai principi morali è condotta maleducata.',
      solution: 'Prima condotta maleducata del componente: penalizzazione (cartellino rosso), punto e servizio alla squadra avversaria.',
      refs: ['RG-21.2.1', 'RG-21.3.1', 'RG-21.6', 'IT-3.6.2'],
    },
    conduct_rude_second: {
      title: 'Seconda condotta maleducata', cat: 'condotta',
      help: 'Controlla sul referto se lo stesso componente è già stato penalizzato per condotta maleducata.',
      solution: 'Seconda condotta maleducata dello stesso componente: espulsione (cartellini giallo e rosso insieme).',
      refs: ['RG-21.3.2', 'RG-21.4.2', 'RG-21.6', 'IT-3.6.2'],
    },
    conduct_aggression: {
      title: 'Minacce o aggressione', cat: 'condotta',
      help: 'Un’aggressione fisica o un comportamento aggressivo o minaccioso («Ti aspetto fuori!») è la condotta più grave.',
      solution: 'Aggressione / minaccia: squalifica (cartellini giallo e rosso separati), senza altre conseguenze.',
      refs: ['RG-21.2.3', 'RG-21.3.3', 'RG-21.6', 'IT-3.6.2'],
    },
    conduct_offensive: {
      title: 'Condotta offensiva', cat: 'condotta',
      help: 'Gesti o parole insultanti o diffamatori sono condotta offensiva.',
      solution: 'Prima condotta offensiva: espulsione (cartellini giallo e rosso insieme), senza altre conseguenze.',
      refs: ['RG-21.2.2', 'RG-21.3.2', 'RG-21.6', 'IT-3.6.2'],
    },
  };

  window.SimSituations = { CATS, S };
})();
