# FoodTrack

Web app (installabile come app Android) per gestire gli alimenti di casa: spazi (frigo, freezer, dispensa…), prodotti con scadenza, avvisi su prodotti in scadenza e scaduti con informazioni utili, liste della spesa, ricette con calcolo degli ingredienti disponibili, piano settimanale dei pasti e **case condivise** tra più utenti. Include un **portale di amministrazione** per ricette, catalogo prodotti, categorie e utenti.

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
├── tools/build-sql.php     rigenera install/foodtrack.sql da lib/schema.php e lib/seed.php
├── assets/                 CSS, JavaScript, icone e immagini (prodotti, spazi, liste, ricette)
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

## Ruoli

| Ruolo | `role` | Cosa può fare |
|---|---|---|
| Utente | 1 | Usa l'app: le sue case, spazi, prodotti, liste, ricette preferite, piano |
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

In più: «Aggiungi i mancanti alla lista della spesa» dalla ricetta (rimossa nel prototipo Flutter), ordinamento dei prodotti, tema chiaro/scuro, consumo parziale di un prodotto.

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
