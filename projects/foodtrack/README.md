# FoodTrack

Web app multilingua (italiano e inglese), installabile come app Android, per gestire gli alimenti di casa: spazi (frigo, freezer, dispensa…), prodotti con scadenza, avvisi su prodotti in scadenza e scaduti con informazioni utili, liste della spesa, ricette con calcolo degli ingredienti disponibili, piano settimanale dei pasti e **case condivise** tra più utenti. Include un **portale di amministrazione** per ricette, catalogo prodotti, categorie e utenti.

Nato come progetto del corso di Progettazione di Applicazioni Mobili (Università di Udine) — prototipo Flutter — e riscritto come web app con backend PHP/MySQL.

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
│   ├── img/recipes/        foto delle ricette iniziali
│   └── vendor/             ZXing (lettore di codici a barre per i browser senza BarcodeDetector)
├── uploads/recipes/        immagini delle ricette caricate dal portale (non su git)
└── android/                progetto Android Studio (Trusted Web Activity) per APK/AAB del Play Store
```

## Installazione sul server (Aruba)

1. Carica via FTP la cartella `projects/foodtrack/` (la cartella `android/` non serve sul server, ma se la carichi è comunque bloccata).
2. Apri **`https://www.saveriomorelli.com/projects/foodtrack/install-db.php`** e inserisci:
   - host (`$localhost_db`), utente (`$username_db`), password (`$password_db`) e nome del database (`$database_name`) presi dal pannello Aruba;
   - prefisso tabelle (predefinito `ft_`);
   - nome, email e password del **master admin**.
3. La pagina crea le tabelle, carica 13 categorie, 68 prodotti e 7 ricette, crea il master admin (con la sua casa) e scrive `credentials.php`.
   Se PHP non può scrivere il file, la pagina mostra il contenuto da creare a mano via FTP.
4. **Elimina `install-db.php` dal server** (ricaricalo solo quando serve aggiornare le tabelle: in quel caso chiede la password del database già salvata).
5. PHP deve poter scrivere in `uploads/recipes/` (permessi `755`) per caricare le immagini delle ricette dal portale.

### In alternativa: phpMyAdmin

Importa `install/foodtrack.sql` (scheda «Importa», una sola volta, su database vuoto), copia `credentials-sample.php` in `credentials.php` e compilalo. Poi registrati dall'app e rendi il tuo account master admin:

```sql
UPDATE ft_users SET role = 10 WHERE email = 'tua@email.it';
```

Dopo aver modificato `lib/schema.php` o `lib/seed.php`, rigenera il file SQL con `php tools/build-sql.php`.

## Accesso con Google (facoltativo)

L'app e il portale admin mostrano il pulsante ufficiale «Continua con Google» quando in `credentials.php` è impostato `$google_client_id`. Non serve nessun segreto: il server verifica il token di Google, controlla che sia destinato al tuo Client ID e che l'email sia verificata.

1. Vai su [Google Cloud Console](https://console.cloud.google.com/) e crea un progetto (es. «FoodTrack»).
2. **Google Auth Platform → Branding** (schermata di consenso OAuth): tipo *Esterno*, nome app «FoodTrack», email di assistenza, logo (`android/play-store-icon-512.png`), dominio autorizzato `saveriomorelli.com`, link alla privacy `https://www.saveriomorelli.com/projects/foodtrack/privacy.html`. Ambiti: solo `openid`, `email`, `profile` (non sensibili, quindi nessuna verifica lunga da parte di Google). Pubblica l'app («In produzione»).
3. **Google Auth Platform → Clients → Crea client**: tipo **Applicazione web**, nome «FoodTrack Web».
   - *Origini JavaScript autorizzate*: `https://www.saveriomorelli.com` (e anche `https://saveriomorelli.com` se il sito risponde senza www).
   - *URI di reindirizzamento*: non servono (si usa il popup).
4. Copia il **Client ID** (finisce con `.apps.googleusercontent.com`) e mettilo in `credentials.php`:
   ```php
   $google_client_id = "1234567890-xxxx.apps.googleusercontent.com";
   ```
   oppure inseriscilo in `install-db.php` (anche in modalità aggiornamento).
5. Fatto: chi accede con Google per la prima volta deve solo accettare l'informativa privacy; se esiste già un account con la stessa email, viene collegato automaticamente. Dal Profilo si può collegare/scollegare Google e impostare una password.

Nell'app Android (TWA) l'accesso con Google funziona perché la pagina gira in Chrome. Nel raro caso in cui il telefono non abbia Chrome e l'app usi la WebView, Google blocca l'accesso: resta disponibile email e password.

## Tutorial

Al primo accesso su un dispositivo (e la prima volta che si apre una nuova versione principale, cioè quando cambia il primo numero di `VERSION` in `assets/app.js`) l'app mostra un breve tutorial illustrato: spazi, prodotti, avvisi di scadenza, liste, ricette e case condivise. Si può rivedere da Profilo → Preferenze → «Tutorial dell'app».

## Lingue

- L'interfaccia (app e portale) è in **italiano** e **inglese**: la lingua si sceglie in automatico dal browser e si cambia dal Profilo (o dal menu laterale del portale). I testi sorgente sono in inglese, le traduzioni sono in `assets/i18n.js` (client) e `lib/i18n.php` (messaggi dell'API).
- Anche i **contenuti** sono traducibili: categorie, prodotti del catalogo, ricette (nome, descrizione, note di cottura, passaggi) e note degli ingredienti. La lingua principale dei contenuti è l'italiano; dal portale ogni editor ha le schede «Italiano · principale» / «English». Se una traduzione manca si mostra il testo italiano.
- **Pubblicazione per lingua** delle ricette: per ogni lingua lo stato è *Pubblicata*, *Bozza* o *Non tradotta* (es. pubblicata in italiano, bozza in inglese). Nell'app una ricetta compare solo nelle lingue in cui è pubblicata; nel portale la colonna «Pubblicazione» mostra lo stato di ogni lingua (verde, giallo, barrato).
- I prodotti aggiunti dagli utenti mostrano il nome del catalogo nella lingua dell'utente finché non vengono rinominati.

## Codici a barre

La scansione usa il `BarcodeDetector` nativo quando c'è (Chrome su Android) e altrimenti la libreria **ZXing** inclusa nel progetto (`assets/vendor/`), quindi funziona anche su Firefox, Safari e Chrome desktop. Serve HTTPS e il permesso della fotocamera (il `.htaccess` di FoodTrack lo consente: `Permissions-Policy: camera=(self)`). Il codice viene cercato su Open Food Facts tramite il server.

## Ruoli

| Ruolo | `role` | Cosa può fare |
|---|---|---|
| Utente | 1 | Usa l'app: le sue case, spazi, prodotti, liste, ricette preferite, piano |
| Moderatore contenuti | 5 | + portale admin limitato: **crea e modifica** ricette (con immagini e traduzioni) e prodotti del catalogo/ingredienti. Non può eliminare, né vedere panoramica, categorie, utenti e case. Lo nominano amministratori e master admin |
| Amministratore | 9 | + portale admin: ricette (con immagini), catalogo prodotti, categorie e informazioni sulla scadenza, utenti (modifica, disabilita, reimposta password, elimina) |
| Master admin | 10 | + nomina e rimuove gli amministratori, cede il proprio ruolo. **Ce n'è sempre uno solo** |

Il portale mostra solo dati aggregati: non permette di leggere il contenuto di spazi e liste degli utenti.

## Case condivise

Ogni utente ha almeno una «casa» (creata alla registrazione con Frigorifero, Freezer e Dispensa). Spazi, prodotti, liste della spesa e piano dei pasti appartengono alla casa e sono condivisi con tutti i suoi membri.

- Invito: dal Profilo → «Invita qualcuno» si condivide un codice di 8 caratteri o il link `…/#/unisciti/CODICE` (il codice si può inserire anche in fase di registrazione).
- Il proprietario può rinominare la casa, rigenerare il codice, rimuovere membri, cedere la proprietà o eliminare la casa.
- Si può far parte di più case (es. casa e casa al mare) e passare dall'una all'altra dal chip in alto.
- L'app si sincronizza ogni 30 secondi e quando torna in primo piano, così le modifiche degli altri membri compaiono da sole.

## Funzionalità (dai requisiti dell'Assignment 1)

| # | Requisito | Dove |
|---|---|---|
| 1 | Gestire gli alimenti in casa | Spazio → «Aggiungi un prodotto» (catalogo con icone, quantità, unità, scadenza, Nutri-Score, marca, categoria, note) |
| 2 | Ricette in base agli alimenti | Ricette: disponibilità di ogni ingrediente, «Hai X ingredienti su Y», filtri |
| 3 | Spazi della casa | Spazi: preimpostati + personalizzati con icona e note |
| 4 | Liste della spesa | Liste: da acquistare → carrello → «Sposta in uno spazio» (anche tutti insieme) |
| 5 | Ricerca alimenti | Spazi → 🔍: per nome, marca, categoria, spazio, data di scadenza |
| 6 | Ricerche recenti | Ricerca (salvate sul dispositivo) |
| 7 | Alimenti aggiunti di recente | Ricerca, a campo vuoto |
| 8–9 | Avvisi in scadenza / scaduti | Banner nella schermata Spazi + notifica (se attivata nel Profilo) |
| 10 | Inserimento tramite scansione | Codice a barre (fotocamera o a mano) → Open Food Facts |
| 11 | Informazioni sulla scadenza | Dettaglio prodotto: consigli per prodotto/categoria + «da consumarsi entro» vs «preferibilmente entro» |
| 12 | Account e sincronizzazione | Registrazione/accesso, dati sul server |
| 13 | Piano dei pasti della settimana | Ricette → «Piano della settimana» → ingredienti mancanti in lista |
| 14 | Condivisione multi-utente | Case condivise |

In più: paesi d’origine delle ricette (uno o più, oppure non definito), interfaccia multilingua, accesso con Google, «Aggiungi i mancanti alla lista della spesa» dalla ricetta (rimossa nel prototipo Flutter), ordinamento dei prodotti, tema chiaro/scuro, consumo parziale di un prodotto.

## App Android (Google Play)

La cartella `android/` è un progetto **Android Studio** che impacchetta la web app come **Trusted Web Activity** (TWA): l'app si apre a schermo intero usando Chrome (o una WebView se Chrome manca), con splash screen, icona, notifiche e link d'invito che aprono direttamente l'app. Aggiornando il sito si aggiorna anche l'app, senza pubblicare nuove versioni sul Play Store.

1. Apri `android/` in Android Studio (File → Open) e attendi la sincronizzazione di Gradle.
2. **Package name**: `com.saveriomorelli.foodtrack` (in `app/build.gradle.kts`). Se vuoi cambiarlo fallo ora: dopo la prima pubblicazione non si può più modificare.
3. Crea la chiave di caricamento: Build → Generate Signed App Bundle / APK → Android App Bundle → Create new… Salva il file `.jks` fuori dal repository e conservalo con le password.
   Oppure copia `keystore.properties.sample` in `keystore.properties` (ignorato da git) e poi usa Build → Build App Bundle(s).
4. Carica l'`.aab` (app/release/app-release.aab) nella Google Play Console. Come icona dello store usa `android/play-store-icon-512.png`; come privacy policy `https://www.saveriomorelli.com/projects/foodtrack/privacy.html`.
5. **Digital Asset Links** (senza questo passaggio l'app mostra la barra dell'indirizzo in alto):
   - Play Console → la tua app → Configurazione → Integrità dell'app → Firma dell'app: copia l'impronta **SHA-256** della chiave di firma dell'app;
   - copia `android/assetlinks.sample.json`, sostituisci l'impronta e caricalo sul server come `https://www.saveriomorelli.com/.well-known/assetlinks.json` (se il file esiste già per altre app, aggiungi l'oggetto all'array);
   - per provare un APK firmato con la tua chiave locale, aggiungi anche la sua SHA-256 (`keytool -list -v -keystore foodtrack-upload.jks`).
6. Ad ogni aggiornamento dell'app Android (raro) incrementa `versionCode` e `versionName`.

## Sicurezza

- Password con `password_hash`, sessione con token casuale in cookie HttpOnly/Secure/SameSite (nel database solo l'hash SHA-256), token CSRF su ogni modifica, richieste solo JSON.
- Limite di tentativi per accesso, registrazione e codici invito.
- Ogni dato è verificato lato server rispetto alla casa dell'utente; le azioni admin rispettano la gerarchia dei ruoli.
- `credentials.php`, `lib/`, `install/`, `tools/` e `android/` non sono raggiungibili dal browser; in `uploads/` non si eseguono script e le immagini vengono ricodificate.
