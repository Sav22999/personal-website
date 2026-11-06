# FoodTrack

Web app multilingua (italiano e inglese), installabile come app Android, per gestire gli alimenti di casa: spazi (frigo,
freezer, dispensa…), prodotti con scadenza, avvisi su prodotti in scadenza e scaduti con informazioni utili, liste della
spesa, ricette con calcolo degli ingredienti disponibili, piano settimanale dei pasti e **case condivise** tra più
utenti. Include un **portale di amministrazione** per ricette, catalogo prodotti, categorie e utenti.

Nato come progetto del corso di Progettazione di Applicazioni Mobili (Università di Udine) — prototipo Flutter — e
riscritto come web app con backend PHP/MySQL.

Online: `https://www.saveriomorelli.com/projects/foodtrack/`

## Struttura

```
foodtrack/
├── index.html              web app (Liste · Spazi · Ricette, piano, ricerca, profilo e case condivise)
├── admin.html              portale admin (solo amministratori e master admin)
├── privacy.html            informativa privacy (serve anche per Google Play)
├── install-db.php          installazione guidata del database (da eliminare dopo l'uso)
├── credentials-sample.php  modello delle credenziali del database
├── credentials.php         ← credenziali reali (NON su git, bloccato dal browser)
├── manifest.webmanifest    nome, icone e avvio dell'app installabile
├── sw.js                   service worker (installabile, interfaccia disponibile con rete scarsa, notifiche)
├── api/index.php           API JSON (?r=azione): unico punto che legge e scrive i dati
├── lib/                    PHP: bootstrap, schema delle tabelle, dati iniziali, case condivise
├── install/foodtrack.sql   query SQL per phpMyAdmin (tabelle + dati iniziali)
├── tools/build-sql.php     rigenera install/foodtrack.sql da lib/schema.php, lib/seed.php e lib/seed-en.php
├── tools/build-icons.py    rigenera lo sprite SVG delle icone (anteprima: tools/icons-preview.html)
├── assets/                 CSS (design system in app.css), JavaScript, traduzioni (i18n.js), font Inter
│   ├── img/icons.svg       set di icone SVG di prodotti, spazi e liste (generato da tools/build-icons.py)
│   ├── img/recipes/        foto delle ricette iniziali (crediti in CREDITS.md)
│   └── vendor/             ZXing (lettore di codici a barre per i browser senza BarcodeDetector)
├── uploads/recipes/        immagini delle ricette caricate dal portale (non su git)
└── android/                progetto Android Studio (Trusted Web Activity) per APK/AAB del Play Store
```

## Installazione sul server (Aruba)

1. Carica via FTP la cartella `projects/foodtrack/` (la cartella `android/` non serve sul server, ma se la carichi è
   comunque bloccata).
2. Apri **`https://www.saveriomorelli.com/projects/foodtrack/install-db.php`** e inserisci:
    - host (`$localhost_db`), utente (`$username_db`), password (`$password_db`) e nome del database (`$database_name`)
      presi dal pannello Aruba;
    - prefisso tabelle (predefinito `ft_`);
    - nome, email e password del **master admin**.
3. La pagina crea le tabelle, carica 13 categorie, 500 prodotti e 206 ricette (in italiano e inglese, da oltre 35
   paesi), crea il master admin (con la sua casa) e scrive `credentials.php`.
   Se PHP non può scrivere il file, la pagina mostra il contenuto da creare a mano via FTP.
4. **Elimina `install-db.php` dal server** (ricaricalo solo quando serve aggiornare le tabelle: in quel caso chiede la
   password del database già salvata).
5. PHP deve poter scrivere in `uploads/recipes/` (permessi `755`) per caricare le immagini delle ricette dal portale.

### In alternativa: phpMyAdmin

Importa `install/foodtrack.sql` (scheda «Importa», una sola volta, su database vuoto), copia `credentials-sample.php` in
`credentials.php` e compilalo. Poi registrati dall'app e rendi il tuo account master admin:

```sql
UPDATE ft_users SET role = 10 WHERE email = 'tua@email.it';
```

Dopo aver modificato `lib/schema.php` o `lib/seed.php`, rigenera il file SQL con `php tools/build-sql.php`.

## Conservazione dei prodotti

Ogni prodotto del catalogo ha un ambiente di conservazione: **freddo** (freezer), **fresco** (frigo), **temperatura
ambiente** o **caldo**. Si imposta dal portale admin; il catalogo iniziale è già classificato in
`seed_product_environment()` (`lib/seed.php`). Ogni spazio ha lo stesso campo, proposto in base all'icona e modificabile
dal modulo dello spazio. Nelle liste della spesa si può cambiare l'ambiente del singolo prodotto, utile soprattutto per
i prodotti scritti a mano e fuori catalogo.

Quando un prodotto passa dalla lista a uno spazio, l'app propone il primo spazio con lo stesso ambiente, sia per il
singolo prodotto sia con «Sposta tutti nei loro spazi». Se manca l'ambiente o non esiste uno spazio adatto, chiede di
sceglierlo. Sulle installazioni esistenti la versione 12 del database assegna l'ambiente ai prodotti iniziali e agli
spazi (in base all'icona: freezer, frigo e borsa frigo; gli altri a temperatura ambiente).

## Attività dei collaboratori

Nelle case con più membri l'app avvisa di ciò che hanno fatto gli altri: prodotti, spazi, liste, pasti, ingressi e uscite (`NOTIFIED_ACTIONS` in `lib/push.php`). Dal profilo, «Notifiche» sceglie le categorie: la scelta è salvata sull'account (`users.notify_off`) e vale su tutti i dispositivi.

- **App aperta:** controllo ogni 30 secondi, compare un messaggio.
- **Browser, app chiusa:** Web Push. Il server manda solo un segnale senza contenuto (chiavi VAPID create al primo uso e salvate in `meta`); il service worker chiede poi il testo a `activity.pending` con la sessione dell'utente. I servizi push non ricevono dati della casa. Su iPhone funziona solo con l'app aggiunta alla schermata Home.
- **App Android, app chiusa:** il WebView non supporta Web Push e Firebase non è usato: `ActivityJobService` controlla `activity.pending` circa ogni 15 minuti (Android può ritardare il controllo per risparmiare batteria).

## Menu a tendina

Con il mouse tutti i `<select>` di app e portale aprono una lista propria (sezione «Dropdowns» di `assets/ui.js`) invece di quella del sistema operativo: stesso aspetto ovunque, bandiere e icone (`flag` e `pic` nelle opzioni), ricerca nelle liste da 10 voci in su, tastiera completa. Il `<select>` vero resta nella pagina e conserva il valore, quindi i nuovi menu funzionano senza codice aggiuntivo. Sui touch screen resta il selettore nativo, più comodo.

## Modifica delle ricette (portale)

Gli ingredienti si scelgono da un catalogo a griglia con ricerca (anche per nome inglese), categorie e prodotti più usati: a tutto schermo su telefono, si apre sul prodotto già scelto e permette di aggiungerne diversi di seguito. Se un prodotto manca, «Crea» lo aggiunge al catalogo senza lasciare la ricetta. La costante `INGREDIENT_EDITOR` in `assets/admin.js` sceglie l'editor: `'v2'` (schede e catalogo) o `'v1'` (tabella con menu a tendina). Il testo non collegato delle ricette vecchie resta com'è ed è segnalato al salvataggio. Nel procedimento i passaggi si spostano con le frecce o trascinando il numero: italiano e inglese si muovono insieme, e un passaggio senza traduzione usa il testo italiano.

## Cronologia della casa

La cronologia registra tutto ciò che cambia nella casa: prodotti, spazi, liste e loro contenuto, piano dei pasti, membri e dati della casa. Si possono annullare solo le azioni sui prodotti negli spazi (`UNDOABLE_ACTIONS` in `lib/bootstrap.php`). I preferiti non compaiono perché sono personali, non della casa. Le voci si caricano 100 alla volta con «Mostra altro».

## Porzioni delle ricette

Nella pagina di una ricetta i pulsanti − e + cambiano le porzioni (da 1 a 50) e le dosi si ricalcolano da sole, arrotondate in modo adatto all'unità; «q.b.» e gli ingredienti senza quantità restano uguali. Anche gli ingredienti disponibili e quelli da aggiungere alla lista seguono le porzioni scelte. Dal profilo, «Porzioni predefinite» imposta le porzioni con cui si aprono tutte le ricette: vale anche per i conteggi nell'elenco e per la lista della spesa del piano settimanale. L'impostazione è salvata sul dispositivo, come le altre preferenze dell'app.

## Accesso con Google (facoltativo)

L'app e il portale admin mostrano il pulsante ufficiale «Continua con Google» quando in `credentials.php` è impostato
`$google_client_id`. Non serve nessun segreto: il server verifica il token di Google, controlla che sia destinato al tuo
Client ID e che l'email sia verificata.

1. Vai su [Google Cloud Console](https://console.cloud.google.com/) e crea un progetto (es. «FoodTrack»).
2. Nota: per l'accesso con Google serve **solo un client di tipo «Applicazione web»**, anche per l'app Android: la TWA
   apre il sito in Chrome, quindi Google vede l'origine `https://www.saveriomorelli.com`. Un client «Android» (che
   chiede package name e impronta SHA-1) non serve.
3. **Google Auth Platform → Branding** (schermata di consenso OAuth): tipo *Esterno*, nome app «FoodTrack», email di
   assistenza, logo (`android/play-store-icon-512.png`), dominio autorizzato `saveriomorelli.com`, link alla privacy
   `https://www.saveriomorelli.com/projects/foodtrack/privacy.html`. Ambiti: solo `openid`, `email`, `profile` (non
   sensibili, quindi nessuna verifica lunga da parte di Google). Pubblica l'app («In produzione»).
4. **Google Auth Platform → Clients → Crea client**: tipo **Applicazione web**, nome «FoodTrack Web».
    - *Origini JavaScript autorizzate*: `https://www.saveriomorelli.com` (e anche `https://saveriomorelli.com` se il
      sito risponde senza www).
    - *URI di reindirizzamento*: non servono (si usa il popup).
5. Copia il **Client ID** (finisce con `.apps.googleusercontent.com`) e mettilo in `credentials.php`:
   ```php
   $google_client_id = "1234567890-xxxx.apps.googleusercontent.com";
   ```
   oppure inseriscilo in `install-db.php` (anche in modalità aggiornamento).
6. Fatto: chi accede con Google per la prima volta deve solo accettare l'informativa privacy; se esiste già un account
   con la stessa email, viene collegato automaticamente. Dal Profilo si può collegare/scollegare Google e impostare una
   password.

Nell'app Android (TWA) l'accesso con Google funziona perché la pagina gira in Chrome. Nel raro caso in cui il telefono
non abbia Chrome e l'app usi la WebView, Google blocca l'accesso: resta disponibile email e password.

## Tutorial

Al primo accesso su un dispositivo (e la prima volta che si apre una nuova versione principale, cioè quando cambia il
primo numero di `VERSION` in `assets/app.js`) l'app mostra un breve tutorial illustrato: spazi, prodotti, avvisi di
scadenza, liste, ricette e case condivise. Si può rivedere da Profilo → Preferenze → «Tutorial dell'app». Nel tutorial
si può scegliere la lingua; le illustrazioni sono animate (le animazioni si disattivano se il sistema chiede di ridurre
il movimento).

## Email

FoodTrack invia due email quando viene creato un account (con email e password, con Google o dal portale):

- all'utente, un messaggio di benvenuto nella sua lingua (italiano o inglese) con il link all'app;
- al master admin, un avviso con nome, email e modalità di registrazione (disattivabile con
  `$mail_notify_admin = false;` in `credentials.php`; non viene inviato per gli account creati dal portale).

Configurazione SMTP: se in `credentials.php` c'è `$mail_config` (vedi `credentials-sample.php`) si usa quella;
altrimenti FoodTrack usa le stesse impostazioni del modulo di contatto del sito (`/.mail-config.php` nella radice).
Senza nessuna delle due non parte nessuna email e l'app funziona lo stesso. Le email vengono inviate dopo aver risposto
all'app, quindi la registrazione non rallenta; un errore di invio finisce solo nel log del server.

## Versione desktop

Da 960 px di larghezza in su (computer, tablet in orizzontale) l'app usa componenti adatti al desktop: barra laterale di
navigazione (con casa attiva, contatori di prodotti in scadenza e da comprare, profilo), azione principale come pulsante
nella barra in alto, schede in griglia, dettaglio ricetta su due colonne, settimana in griglia, finestre di dialogo
centrate al posto dei pannelli dal basso, menu a comparsa vicino al puntatore e accesso su due colonne. Scorciatoie: `/`
per cercare, `Esc` per chiudere una finestra. Per admin e moderatori contenuti la barra laterale ha anche il
collegamento al «Portale gestionale». Il portale usa gli stessi componenti e la stessa grafica dell'app desktop, ed è
completamente responsive e mobile-friendly su qualsiasi dispositivo (con drawer laterale a scomparsa e tabelle
ottimizzate).

## Lingue

- L'interfaccia (app e portale) è in **italiano** e **inglese**: la lingua si sceglie in automatico dal browser e si
  cambia dal Profilo (o dal menu laterale del portale). I testi sorgente sono in inglese, le traduzioni sono in
  `assets/i18n.js` (client) e `lib/i18n.php` (messaggi dell'API).
- Anche i **contenuti** sono traducibili: categorie, prodotti del catalogo, ricette (nome, descrizione, note di cottura,
  passaggi) e note degli ingredienti. La lingua principale dei contenuti è l'italiano; dal portale ogni editor ha le
  schede «Italiano · principale» / «English». Se una traduzione manca si mostra il testo italiano.
- **Pubblicazione per lingua** delle ricette: per ogni lingua lo stato è *Pubblicata*, *Bozza* o *Non tradotta* (es.
  pubblicata in italiano, bozza in inglese). Nell'app una ricetta compare solo nelle lingue in cui è pubblicata; nel
  portale la colonna «Pubblicazione» mostra lo stato di ogni lingua (verde, giallo, barrato).
- I prodotti aggiunti dagli utenti mostrano il nome del catalogo nella lingua dell'utente finché non vengono rinominati.

## Codici a barre

La scansione usa il `BarcodeDetector` nativo quando c'è (Chrome su Android) e altrimenti la libreria **ZXing** inclusa
nel progetto (`assets/vendor/`), quindi funziona anche su Firefox, Safari e Chrome desktop. Serve HTTPS e il permesso
della fotocamera (il `.htaccess` di FoodTrack lo consente: `Permissions-Policy: camera=(self)`). Il codice viene cercato
su Open Food Facts tramite il server.

## Ricette iniziali

206 ricette semplici con passaggi brevi e schematici, in italiano e inglese, da Italia, Inghilterra, Scozia, Galles,
Irlanda, Stati Uniti, Canada, Messico, Perù, Brasile, Germania, Austria, Svizzera, Francia, Belgio, Paesi Bassi, Svezia,
Polonia, Ungheria, Grecia, Spagna, Portogallo, Turchia, Libano, Egitto, Marocco, Tunisia, Cina, Giappone, Corea del Sud,
Thailandia, Vietnam e India (`lib/seed.php`, `lib/seed-recipes-2.php`, `lib/seed-recipes-3.php`, `lib/seed-recipes-4.php`, `lib/seed-recipes-5.php`, `lib/seed-recipes-6.php`). Sulle installazioni
esistenti le nuove ricette vengono aggiunte una sola volta, in automatico (una ricetta eliminata da un admin non
ricompare). **Tutte le 206 ricette iniziali hanno una foto di copertina** (`assets/img/recipes/`, abbinate in
`lib/seed-images.php`): quelle dei lotti 2, 3, 4, 5, 6 e 7 sono tutte in **pubblico dominio o CC0** (Wikimedia Commons),
ritagliate a 1200×800; fonti e autori in `assets/img/recipes/CREDITS.md`. Sulle installazioni esistenti le versioni 9,
11, 20 e 21 del database assegnano le foto alle ricette iniziali che non hanno ancora un'immagine (non toccano quelle caricate
dal portale). Le eventuali nuove ricette create senza foto mostrano una copertina illustrata con le bandiere dei paesi
d'origine.

## Ruoli

| Ruolo                | `role` | Cosa può fare                                                                                                                                                                                                                       |
|----------------------|--------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Utente               | 1      | Usa l'app: le sue case, spazi, prodotti, liste, ricette preferite, piano                                                                                                                                                            |
| Moderatore contenuti | 5      | + portale admin limitato: **crea e modifica** ricette (con immagini e traduzioni) e prodotti del catalogo/ingredienti. Non può eliminare, né vedere panoramica, categorie, utenti e case. Lo nominano amministratori e master admin |
| Amministratore       | 9      | + portale admin: ricette (con immagini), catalogo prodotti, categorie e informazioni sulla scadenza, utenti (modifica, disabilita, reimposta password, elimina)                                                                     |
| Master admin         | 10     | + nomina e rimuove gli amministratori, cede il proprio ruolo. **Ce n'è sempre uno solo**                                                                                                                                            |

Il portale mostra solo dati aggregati: non permette di leggere il contenuto di spazi e liste degli utenti.

## Case condivise

Ogni utente ha almeno una «casa» (creata alla registrazione con Frigorifero, Freezer e Dispensa). Spazi, prodotti, liste
della spesa e piano dei pasti appartengono alla casa e sono condivisi con tutti i suoi membri.

- Invito: dal Profilo → «Invita qualcuno» si condivide un codice di 8 caratteri o il link `…/#/join/CODICE` (i vecchi
  link `#/unisciti/…` continuano a funzionare) (il codice si può inserire anche in fase di registrazione).
- Il proprietario può rinominare la casa, rigenerare il codice, rimuovere membri, cedere la proprietà o eliminare la
  casa.
- Si può far parte di più case (es. casa e casa al mare) e passare dall'una all'altra dal chip in alto.
- L'app si sincronizza ogni 30 secondi e quando torna in primo piano, così le modifiche degli altri membri compaiono da
  sole.

## Funzionalità (dai requisiti dell'Assignment 1)

| #   | Requisito                       | Dove                                                                                                                 |
|-----|---------------------------------|----------------------------------------------------------------------------------------------------------------------|
| 1   | Gestire gli alimenti in casa    | Spazio → «Aggiungi un prodotto» (catalogo con icone, quantità, unità, scadenza, Nutri-Score, marca, categoria, note) |
| 2   | Ricette in base agli alimenti   | Ricette: disponibilità di ogni ingrediente, «Hai X ingredienti su Y», filtri                                         |
| 3   | Spazi della casa                | Spazi: preimpostati + personalizzati con icona e note                                                                |
| 4   | Liste della spesa               | Liste: da acquistare → carrello → «Sposta in uno spazio» (anche tutti insieme)                                       |
| 5   | Ricerca alimenti                | Spazi → 🔍: per nome, marca, categoria, spazio, data di scadenza                                                     |
| 6   | Ricerche recenti                | Ricerca (salvate sul dispositivo)                                                                                    |
| 7   | Alimenti aggiunti di recente    | Ricerca, a campo vuoto                                                                                               |
| 8–9 | Avvisi in scadenza / scaduti    | Banner nella schermata Spazi + notifica (se attivata nel Profilo)                                                    |
| 10  | Inserimento tramite scansione   | Codice a barre (fotocamera o a mano) → Open Food Facts                                                               |
| 11  | Informazioni sulla scadenza     | Dettaglio prodotto: consigli per prodotto/categoria + «da consumarsi entro» vs «preferibilmente entro»               |
| 12  | Account e sincronizzazione      | Registrazione/accesso, dati sul server                                                                               |
| 13  | Piano dei pasti della settimana | Ricette → «Piano della settimana» → ingredienti mancanti in lista                                                    |
| 14  | Condivisione multi-utente       | Case condivise                                                                                                       |

In più: paesi d’origine delle ricette (uno o più, oppure non definito), interfaccia multilingua, accesso con Google,
«Aggiungi i mancanti alla lista della spesa» dalla ricetta (rimossa nel prototipo Flutter), ordinamento dei prodotti,
tema chiaro/scuro, consumo parziale di un prodotto, cronologia delle attività della casa con funzione di annullamento
(undo), ricerca ricette con prodotti già presenti in casa, portale admin completamente mobile-friendly.

## App Android (Google Play)

La cartella `android/` è un progetto **Android Studio** (Java, nessun plugin Kotlin) con un'app nativa che mostra la web
app in una **WebView a schermo intero**. Funziona allo stesso modo su ogni telefono, qualunque browser sia installato
(prima era una Trusted Web Activity, che senza Chrome mostrava la barra del browser). Aggiornando il sito si aggiorna
anche l'app, senza pubblicare nuove versioni sul Play Store.

Parti native (`MainActivity.java`, esposte alla pagina come `window.FoodTrackAndroid`, solo sulle pagine di FoodTrack):

- **«Continua con Google»** con il selettore di account di sistema (Credential Manager): il login Google non funziona
  dentro una WebView, quindi l'app ottiene il token e lo passa alla pagina, che lo verifica sul server come sul sito;
- **notifiche** dei prodotti in scadenza (permesso chiesto quando le attivi dal Profilo);
- **fotocamera** per la scansione dei codici a barre (permesso chiesto al primo «Scansiona»);
- selezione dei file (caricamento immagini nel portale), tasto indietro, link d'invito che aprono l'app, link esterni
  (modulo di contatto, Open Food Facts…) nel browser, pagina «Nessuna connessione», tema chiaro/scuro come il sistema.

Passaggi:

1. Apri `android/` in Android Studio (File → Open) e attendi la sincronizzazione di Gradle.
2. **Package name**: `com.saverio.foodtrack` (in `app/build.gradle.kts`): dopo la prima pubblicazione non si può più
   modificare.
3. Crea la chiave di caricamento: Build → Generate Signed App Bundle / APK → Android App Bundle → Create new… Salva il
   file `.jks` fuori dal repository e conservalo con le password. Oppure copia `keystore.properties.sample` in
   `keystore.properties` (ignorato da git) e usa Build → Build App Bundle (s).
4. **Login con Google nell'app** — in Google Cloud Console, nello **stesso progetto** del client «Applicazione web» già
   usato dal sito, crea un altro *ID client OAuth* di tipo **Android**: package `com.saverio.foodtrack` e impronta
   **SHA-1**. Serve un client Android per ogni chiave che firma l'app:
    - chiave di debug (Android Studio → Gradle → app → Tasks → android → `signingReport`, variante debug);
    - chiave di firma di Google Play (Play Console → Test e rilascio → Integrità dell'app → Firma dell'app → SHA-1).
      Nel codice non cambia nulla: l'app usa il Client ID **web** che legge dal server (`$google_client_id`), e il token
      resta verificato dal server come sul sito.
5. Carica l'`.aab` (app/release/app-release.aab) nella Google Play Console. Come icona dello store usa
   `android/play-store-icon-512.png`; come privacy policy
   `https://www.saveriomorelli.com/projects/foodtrack/privacy.html`. Testi e immagini per la scheda dello store
   (screenshot 1080×1920 e immagine in primo piano 1024×500) sono in `android/store/` (`listing.md`).
6. **Digital Asset Links** (facoltativo ma consigliato, per aprire i link d'invito direttamente nell'app):
   `/.well-known/assetlinks.json` nella radice del sito, con le impronte **SHA-256** delle chiavi (debug, caricamento,
   firma di Google Play).
7. Ad ogni aggiornamento dell'app Android (raro) incrementa `versionCode` e `versionName`.

## Sicurezza

- Password con `password_hash`, sessione con token casuale in cookie HttpOnly/Secure/SameSite (nel database solo l'hash
  SHA-256), token CSRF su ogni modifica, richieste solo JSON.
- Limite di tentativi per accesso, registrazione e codici invito.
- Ogni dato è verificato lato server rispetto alla casa dell'utente; le azioni admin rispettano la gerarchia dei ruoli.
- `credentials.php`, `lib/`, `install/`, `tools/` e `android/` non sono raggiungibili dal browser; in `uploads/` non si
  eseguono script e le immagini vengono ricodificate.
