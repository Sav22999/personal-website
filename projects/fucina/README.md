# Fucina

App gamificata per imparare i linguaggi del web, a livelli sempre più difficili: **15 corsi e 93 livelli** di HTML, CSS,
animazioni, SCSS, Sass, JavaScript, TypeScript e PHP, in percorsi paralleli che partono da HTML base.

Web app in PHP + MySQL, in italiano e inglese, installabile (PWA) e pronta per Google Play (`android/`).

## Come funziona

- **Corsi e livelli**: ogni corso è una serie di livelli (teoria, domande, esercizio di codice) con difficoltà crescente.
  Un livello si apre finendo il precedente, un corso finendo quello che lo precede o superandone l’esame.
- **Sfide lampo** (`lib/drills.php`, `assets/drills.js`): tra teoria e domande, sei tipi di mini-gioco: tessere, ordina
  le righe, caccia al bug, coppie, vero o falso a tempo e figure da copiare con i comandi CSS. Si ritrovano mescolate in
  **Palestra**. Come i giochi si svolgono nel browser, quindi non sono materiale d’esame e pagano una volta sola.
- **Esercizi**: si scrive codice vero con anteprima dal vivo per HTML e CSS. Il controllo gira nel browser, in un iframe
  senza script. SCSS, Sass, JavaScript, TypeScript e PHP non hanno anteprima: il controllo guarda il codice scritto.
- **XP e Scintille**: ogni ricompensa dà XP e altrettante Scintille. Gli XP crescono soltanto e decidono il grado: 14 nomi,
  da Scintilla (grado 1) a Fiamma eterna (grado 40), elencati in `RANKS` di `lib/progress.php` e mostrati tutti nelle statistiche. Le Scintille si spendono in **suggerimenti**: su una domanda tolgono metà delle
  risposte sbagliate, su un esercizio mostrano una soluzione, nei giochi danno un aiuto. All’esame non ci sono.
- **Esame e certificato**: un esame con domande estratte a caso e un tempo massimo (15 minuti di partenza, controllato dal
  server), aperto per ogni corso anche senza fare i livelli: chi
  conosce già la materia lo supera e il corso conta come finito: si aprono i successivi e i livelli non giocati risultano
  risolti con dei fuochi al posto delle stelle (3 dal 90%, 2 dall’80%, 1 altrimenti), senza XP e senza contare per i
  badge a numero. Superato, il certificato si **sblocca
  con le Scintille** (prezzo in Impostazioni, 200 di partenza) e si può mostrare e stampare in A4
  (`certificate.php?c=CODICE`). È un’immagine generata dal server, sempre in inglese, che si salva in PNG o PDF
  (`lib/certificate.php`; lo sfondo si rigenera con `tools/build-certificate.py`). Non ha valore legale, e lo dice.
  Chiunque, anche senza account, può controllarlo con il suo numero univoco su `verify.php`.
- **Giochi**: nove giochi a livelli sul codice. *Chi è il tag?*, *Tagdoku* (4×4, poi 9×9, poi 16×16), *Quattro di fila*
  (forza quattro con le domande: risposta giusta, il tuo gettone verde; sbagliata, il computer mette un gettone rosso),
  *Scacchiera CSS* (da 3×3 a 10×10), *Salti di riga* (righe da trascinare), *Tinta giusta* (colori CSS),
  *Duello di selettori* (specificità), *Laboratorio flex* e *Che cosa stampa?* (output JavaScript).
  Danno Scintille extra, fino a un massimo giornaliero.
- **Immagine del profilo**: personaggi originali e sfondi disegnati in SVG (`assets/avatars.js`), comprati con le Scintille
  dal profilo e visibili nelle classifiche e tra gli amici. Prezzi e vendita di ogni elemento si cambiano dal portale admin
  (*Immagini del profilo*, tabella `fc_avatar_items`); gli acquisti stanno in `fc_user_items` e nell’attività dell’utente.
  L’iniziale dello username è sempre gratis.
- **Badge**: ogni nuovo badge dà XP e Scintille (50 di partenza, `xp_badge` in Impostazioni).
- **Conferma dell’email**: chi si registra con email e password riceve un link (valido 48 ore, monouso, salvato solo come
  hash in `fc_email_tokens`); finché non lo apre l’account è bloccato lato server (`must_verify()`), tranne reinvio del
  link ed eliminazione. Il link porta all’app, che chiede conferma con un pulsante (così gli scanner delle email non lo
  consumano) e fa entrare. Gli account Google e gli admin sono già confermati. Senza SMTP configurato la verifica si
  spegne, invece di chiudere fuori tutti. L’email non si può cambiare.
- **Portale admin, utenti**: XP e Scintille modificabili (ogni modifica resta nell’attività), attività di ogni utente in
  sola lettura con data e ora, e chi deve ancora confermare l’email.
- **Username e amici**: ognuno sceglie uno username, l’unico dato pubblico. Serve per cercare, aggiungere e rimuovere
  amici (in un solo senso, senza richieste) e nelle classifiche, tra tutti o tra amici.
- **Classifiche del giorno**: quella della sfida del giorno (più risposte giuste, a parità il più veloce, con il tempo misurato dal server) dà i premi:
  il primo riceve Scintille in base ai partecipanti, il secondo la metà e il terzo un quarto, ma serve almeno una risposta
  giusta. Non serve un cron: paga il primo accesso del giorno dopo. Quella degli XP è solo per divertimento. Ci si può
  escludere dal profilo.
- **Notifiche**: sfida del giorno, serie a rischio, premi e amici, ognuna attivabile dal profilo (`lib/push.php`). Il push
  non porta contenuti: sveglia il service worker, che chiede all’API. I promemoria serali partono da `reminders.php`: va
  pianificato ogni giorno verso le 18:00, con la stessa chiave del cron di pulizia.
- **Sfida del giorno**, serie di giorni, statistiche, calendario delle attività e 51 badge, uno anche per ogni corso.

Le regole stanno sul server: le risposte arrivano al browser solo dopo aver risposto (`quiz.check`), l’esame viene
corretto dal server e gli XP dei giochi, l’unico risultato comunicato dal browser, hanno un massimo per partita e al giorno.

## Portale admin (`admin.html`)

Riservato ad amministratori (ruolo 5) e master admin (10), in italiano e inglese. Gestisce corsi, livelli (teoria,
esercizi, controlli), quiz, sfide (il contenuto si scrive in JSON, con un esempio per ogni tipo), giochi e loro contenuti, utenti, certificati e impostazioni (XP, prezzi dei suggerimenti,
esame). Ogni testo si modifica nelle lingue affiancate; una lingua vuota è segnalata in rosso e contata in «Da tradurre»,
e nel frattempo l’app mostra l’altra.

Per aggiungere una lingua: un blocco in `assets/i18n.js` e il suo codice in `LANGS` di `lib/i18n.php`.

## Struttura

```
fucina/
├── index.html, admin.html, privacy.html, manifest.webmanifest, sw.js
├── certificate.php         pagina pubblica di un certificato
├── install-db.php          installazione guidata (tabelle, contenuti iniziali, master admin)
├── update.php              aggiorna le tabelle con le credenziali salvate
├── credentials-sample.php  modello delle credenziali (credentials.php è in .gitignore)
├── api/index.php           API JSON
├── lib/                    bootstrap, schema, catalogo (courses.php), progressi (progress.php), API admin, email
│   ├── seed.php, seed/     contenuti iniziali: usati una sola volta, poi comanda il database
├── assets/                 app.js, games.js, admin.js, ui.js, api.js, i18n.js, app.css + fucina.css, icone, font
├── install/fucina.sql      le stesse tabelle e contenuti per phpMyAdmin (php tools/build-sql.php)
├── tools/                  build-sql.php, build-icons.py
└── android/                progetto Android Studio (WebView)
```

## Installazione sul server

Il database è il quinto dell’hosting (`…_5`), con prefisso `fc_`.

1. Carica `projects/fucina/` (la cartella `android/` non serve sul server), senza `credentials.php`.
2. Apri `https://www.saveriomorelli.com/projects/fucina/install-db.php` e inserisci host, utente, password e nome del
   database (`…_5`), poi nome, email e password del **master admin**.
3. La pagina crea le tabelle e i contenuti iniziali, il master admin e `credentials.php`.
4. Eliminala dal server dopo l’uso.

In alternativa: phpMyAdmin → importa `install/fucina.sql`, poi registrati e rendi admin il tuo account:

```sql
UPDATE fc_users SET role = 10 WHERE email = 'tua@email.it';
```

## Accesso con Google

Usa lo stesso client OAuth «Applicazione web» di FoodTrack e GiftWise. Il Client ID sta in `credentials.php`.

## App Android (Google Play)

`android/` è una WebView nativa come quella di GiftWise (`com.saverio.fucina`): login Google con il selettore di sistema
(`window.FucinaAndroid`), link ai certificati che aprono l’app, tasto indietro, pagina «Nessuna connessione».

1. Apri `android/` in Android Studio e attendi Gradle.
2. Crea la chiave di caricamento, oppure copia `keystore.properties.sample` in `keystore.properties`.
3. In Google Cloud Console crea un *ID client OAuth* di tipo **Android** con package `com.saverio.fucina` e la SHA-1.
4. Carica l’`.aab` in Play Console. Icona: `android/play-store-icon-512.png`, testi: `android/store/listing.md`.
5. Per aprire i link nell’app aggiungi a `/.well-known/assetlinks.json` quanto indicato in `android/assetlinks.sample.json`.

## Sicurezza

Password con `password_hash`, sessione con token casuale in cookie HttpOnly/Secure/SameSite (nel database solo l’hash
SHA-256), token CSRF su ogni modifica, limiti ai tentativi, `credentials.php`, `lib/`, `install/` e `tools/` bloccati dal
browser. Il codice degli esercizi gira in un iframe `sandbox` senza script.

## Aggiornare il catalogo

I contenuti iniziali stanno in `lib/seed/`. Per portarli su un’installazione esistente aggiungili e alza `SCHEMA_VERSION`
in `lib/bootstrap.php`: al primo accesso vengono inseriti solo i corsi e i livelli che mancano, e ciò che è stato
modificato dal portale resta com’è. Prima di caricare, controlla tutto con `php tools/check-drills.php`, poi rigenera
`install/fucina.sql` con `php tools/build-sql.php`.
