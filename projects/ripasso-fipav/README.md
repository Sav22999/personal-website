# Fischietto — ripasso del regolamento per arbitri FIPAV

Portale per ripassare il regolamento FIPAV: quiz, riferimenti normativi, documenti ufficiali e statistiche personali. Domande e riferimenti stanno nel database MySQL e arrivano da un'API autenticata; statistiche, segnalibri e quiz sospesi sono salvati per ogni utente.

Online: `https://www.saveriomorelli.com/projects/ripasso-fipav/`

## Struttura

```
ripasso-fipav/
├── index.html            portale (accesso, quiz, ripasso, documenti, statistiche, profilo)
├── admin.html            pannello di gestione (panoramica, utenti, domande, riferimenti, segnalazioni, documenti)
├── manifest.webmanifest  nome, icone e avvio dell'app installabile
├── sw.js                 service worker (app installabile, interfaccia disponibile anche con rete scarsa)
├── api/index.php         API JSON (?r=azione): unico punto che legge e scrive i dati
├── assets/               CSS, JavaScript, icone (assets/icons/) e note di rilascio (assets/version.js)
├── documenti/            PDF ufficiali (fissi) scaricabili dal portale
│   └── altri/            «Altri documenti utili», gestiti dal pannello
├── lib/                  codice PHP e configurazione (bloccata al browser)
│   ├── config.php        ← credenziali del database (NON su git)
│   └── config.example.php
├── install.php           installazione guidata (una volta sola, poi va eliminato)
├── install/seed.json     domande e riferimenti iniziali (bloccato al browser)
└── sql/                  dump SQL per phpMyAdmin (NON su git, bloccato al browser)
```

Le pagine sono HTML statico: non contengono dati. Tutto passa dall'API PHP, che controlla sessione e permessi a ogni richiesta.

## Aggiornare una versione già online

Carica via FTP tutti i file **tranne** `lib/config.php` (quello sul server resta com'è). Al primo accesso il database si aggiorna da solo:

- aggiunge la colonna per il cambio password obbligatorio;
- lascia un solo **master admin** (livello 10): l'account con id più basso tra quelli a 10; gli altri amministratori passano a 9;
- crea la tabella degli «Altri documenti utili»;
- aggiunge stagione, tipo e nota di modifica ai riferimenti e segna le novità 2026/27.

Non serve rieseguire `install.php`.

## Documenti

La sezione «Documenti» del portale ha due parti.

**Documenti ufficiali** (fissi): sono le fonti da cui sono tratte domande e riferimenti. Stanno in `documenti/` e si aggiornano solo via FTP, sostituendo il file con lo stesso nome. Il portale controlla da solo quali file esistono.

| Fonte | File |
|---|---|
| Regole di gioco 2025–2028 | `regole-di-gioco-2025-2028.pdf` |
| Casistica ufficiale 2025–2028 | `casistica-ufficiale-2025-2028.pdf` |
| Indicazioni tecniche 2025/26 | `indicazioni-tecniche-2025-2026.pdf` |
| Normative federali 2026/27 (con modifiche evidenziate) | `normative-federali-2026-2027.pdf` |
| Vademecum 2026/27 | `vademecum-2026-2027.pdf` |

**Altri documenti utili** (gestibili): amministratori e master admin li aggiungono, modificano, sostituiscono, riordinano ed eliminano dal pannello (sezione Documenti). I file stanno in `documenti/altri/` e l'elenco nel database (tabella `fr_documents`).

- Il pannello può scrivere solo in `documenti/altri/`: i documenti ufficiali non si possono modificare o cancellare da lì.
- Il server accetta solo veri PDF (controlla il contenuto, non solo l'estensione), fino a 24 MB o al limite di PHP se è più basso (`upload_max_filesize` e `post_max_size`, oggi 25 MB nel `.user.ini` del sito). Il nome del file lo decide il server a partire da titolo ed edizione.
- In `documenti/` e `documenti/altri/` il server apre solo file `.pdf`: qualunque altro file viene bloccato.
- Quando si sostituisce un PDF, il link cambia (`?v=`), così nessuno vede la versione vecchia rimasta in cache.
- **Permessi su Aruba:** PHP deve poter scrivere in `documenti/altri/`. Se il pannello mostra un errore sui permessi, imposta la cartella a `755` dal File Manager o dal client FTP.

## Regole nuove o modificate

Ogni riferimento può indicare la **stagione** in cui la regola è stata introdotta o cambiata (es. `2026/27`), il **tipo** (nuova o modificata) e una breve nota su **cosa cambia**. Si impostano dall'editor dei riferimenti nel pannello.

- La stagione in corso è calcolata da sola: la stagione FIPAV parte il 1° luglio. L'etichetta della stagione in corso è evidenziata; quelle delle stagioni passate restano come storico, più discrete.
- Nel portale: etichetta sui riferimenti (Ripasso e sotto le domande), nota «Cosa cambia», filtro «Novità» nel Ripasso, opzione del quiz «Solo novità della stagione» e indicatore «Novità» sulla domanda.
- Compilazione iniziale (automatica, al primo accesso dopo l'aggiornamento, solo sui riferimenti ancora vuoti):
  - Normative federali 2026/27 «con modifiche evidenziate»: le sezioni evidenziate in giallo nel PDF (2.8 e 4.2–4.8, con 4.6.1 e 4.7.1) come «Modificata 2026/27»;
  - Precisazioni sperimentazioni 2026/27 (NF-PR.1–4) come «Nuova 2026/27».
- Da verificare a mano: il Vademecum 2026/27 ha molte parti evidenziate (sezioni 2, 4, 5.8–5.8.2, 6.1, 7, 10, 13, 14) ma il documento non dice che siano modifiche; nelle Normative federali c'è «UNDER 23» in rosso a margine della 2.3.1.

## Profilo e menu

- Il menu dell'avatar contiene solo: «Il mio profilo», «Pannello di gestione» (moderatori e admin), tema, «Esci».
- La pagina **Il mio profilo** (`#profilo`) permette di cambiare nome e password, scegliere il tema, vedere i propri progressi, azzerare le statistiche e uscire. L'email non si modifica dal portale.
- L'esportazione JSON di domande e riferimenti è nel pannello di gestione (pulsante «Esporta» nelle pagine Domande e Riferimenti).
- Amministratori e master admin gestiscono gli «Altri documenti utili» dalla sezione Documenti del pannello.

## Versioni e pagina Novità

Il numero di versione nel footer porta alla pagina **Novità** (`#novita`), con le note di ogni versione. Le note stanno in `assets/version.js`: per pubblicare una nuova versione aggiungi un elemento in cima a `APP_RELEASES` e aggiorna il parametro `?v=` nei tag `<script>` e `<link>` di `index.html` e `admin.html`, in `assets/boot.js` e il nome `CACHE` in `sw.js`. La versione è visibile anche nella schermata di accesso.

**Importante:** aggiorna `?v=` ogni volta che modifichi un file CSS o JavaScript, anche per piccole correzioni. L'`.htaccess` del sito fa conservare CSS e JS ai browser per un mese: senza un nuovo `?v=` gli utenti continuerebbero a vedere la versione vecchia.

## App installabile

Fischietto è una web app installabile (`manifest.webmanifest` + `sw.js`, solo su HTTPS). Su Android e computer il pulsante «Installa l'app» compare nel menu dell'account e nella pagina Il mio profilo; su iPhone vengono mostrati i passaggi per «Aggiungi alla schermata Home».

Il service worker non salva mai le risposte dell'API né i PDF: conserva solo l'interfaccia (pagine, CSS, JS, icone), così i dati personali restano sempre sul server. Il nome della cache (`CACHE` in `sw.js`) segue la versione: aggiornalo insieme a `?v=`.

## Panoramica del pannello

La home del pannello mostra cosa c'è da fare (registrazioni, segnalazioni), l'attività di studio, le risposte corrette per fonte, i riferimenti più difficili, le novità della stagione, lo stato dei contenuti (con scorciatoie agli elenchi già filtrati), le domande per fonte e, per gli amministratori, i documenti. I dati di studio arrivano dall'azione API `admin.overview` (livello ≥ 5): sono solo totali, senza nomi né email.

## Panoramica utente (amministratori)

Nel pannello, toccando un utente si apre la sua panoramica in sola lettura: risposte date, corrette ed errate, risultati per fonte, riferimenti più sbagliati, domande salvate e segnalate, quiz sospesi. Non mostra email né altri dati personali (azione API `admin.user.stats`, livello ≥ 9). Su telefono le sezioni del pannello sono in una barra di navigazione in basso.

## Immagine del profilo

L'avatar viene da [Gravatar](https://gravatar.com): il server calcola l'impronta SHA-256 dell'email e il browser chiede l'immagine a Gravatar con `d=404`. Se l'utente non ha un'immagine su Gravatar compaiono le sue iniziali. L'email non viene mai inviata in chiaro a Gravatar.

## Dispositivi mobili

- Su telefono la navigazione passa a una barra in basso; menu, conferme e azioni si aprono come pannelli dal basso; l'editor delle domande è a schermo intero.
- Campi di testo a 16px sui dispositivi touch (niente zoom automatico su iPhone) e pulsanti da almeno 44px.
- Verificato senza scorrimento orizzontale a 320, 390, 768 e 1280px.

## Ruoli

Chiunque può registrarsi, ma l'account resta **in attesa** finché un amministratore non lo abilita.

| Livello | Ruolo | Cosa può fare |
|---|---|---|
| 0 | Nessun accesso | account registrato, non può entrare |
| 1 | Utente | quiz, ripasso, documenti, statistiche |
| 5 | Moderatore contenuti | + gestione domande e riferimenti, vede le segnalazioni |
| 9 | Amministratore | + abilita gli utenti, gestisce utenti e moderatori e gli «Altri documenti utili» |
| 10 | Master admin | uno solo: + gestisce anche gli amministratori |

- Gli amministratori (9) non possono modificare, reimpostare o eliminare altri amministratori né il master: solo il master admin può nominare, declassare o gestire gli amministratori.
- Il master admin non può essere modificato da nessuno. Può passare il proprio ruolo a un altro utente attivo (menu «⋯» dell'utente → «Passa il ruolo di master admin», con conferma della password): lui diventa 10 e il vecchio master 9.
- Nessuno può modificare il proprio account dal pannello (si usa il menu del profilo).
- Chi viene disabilitato o perde l'accesso è disconnesso subito.

## Password

- Regole per ogni nuova password: almeno 10 caratteri, almeno tre tipi tra minuscole, maiuscole, numeri e simboli, senza nome o email. Il portale mostra un indicatore di robustezza.
- **Password dimenticata**: dalla schermata di accesso l'utente viene indirizzato al modulo di contatto del master admin (`https://www.saveriomorelli.com/contact-me/email/`).
- **Reset dal pannello**: genera una password temporanea (mostrata una sola volta) e chiude le sessioni dell'utente. Al primo accesso l'utente deve sceglierne una nuova prima di poter usare il portale: finché non lo fa, l'API rifiuta ogni altra richiesta.

## Sicurezza

- Password salvate con `password_hash` (bcrypt). La sessione usa un token casuale in un cookie `HttpOnly`, `Secure` e `SameSite=Lax`; nel database ne è salvato solo l'hash.
- Tutte le modifiche richiedono un token CSRF e un corpo JSON.
- Dopo troppi tentativi di accesso falliti l'account viene bloccato per 15 minuti; anche le registrazioni sono limitate.
- `lib/`, `install/`, `sql/` e i file `.md`, `.json`, `.sql` e nascosti non sono raggiungibili dal browser (`.htaccess`).
- Query sempre con prepared statement.
- I commenti nel codice sono in inglese; i testi mostrati agli utenti restano in italiano.

## Notifica email (facoltativa)

Per ricevere un'email a ogni nuova registrazione, compila in `lib/config.php` `notify_email` (il tuo indirizzo) e `mail_from` (una casella del dominio, es. `noreply@saveriomorelli.com`).

## Git

`lib/config.php` e `sql/` sono esclusi da git (`.gitignore` qui e nella radice del sito). Su un nuovo server copia `lib/config.example.php` in `lib/config.php` e compilalo.
