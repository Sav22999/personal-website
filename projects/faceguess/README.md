# FaceGuess

Il gioco di deduzione fisionomica a 2 giocatori sviluppato da Saverio Morelli.
Disponibile come web app e come applicazione Android per Google Play Store.

## Caratteristiche

- **Personaggi generati proceduralmente**: a ogni partita il cast dei volti è completamente nuovo e unico.
- **Stanze online con codice**: gioca con gli amici condividendo un codice stanza a 6 caratteri o il link d'invito diretto.
- **Matchmaking casuale**: trova avversari online in pochi istanti.
- **Sullo stesso dispositivo**: modalità Pass & Play a turno su smartphone o tablet.
- **Tabellone interattivo**: abbatti le carte degli indiziati con un semplice tocco.
- **Chat in tempo reale**: fai domande e rispondi direttamente nel gioco.
- **Tema Auto, Chiaro o Scuro**: schede su desktop, un pulsante sui telefoni (`theme.js`).
- **Multilingua**: italiano, inglese, spagnolo, francese, tedesco.
- **Senza registrazione né pubblicità**: 100% gratuito.

## App Android (Google Play)

La cartella `android/` è un progetto Android Studio (Java) con una shell nativa WebView a schermo intero:

- Layout edge-to-edge con adattamento alla barra di sistema;
- Deep Links / App Links automatici per l'apertura immediata delle stanze (`https://www.saveriomorelli.com/projects/faceguess/?code=ABC123`);
- Gestione della navigazione indietro con `OnBackInvokedDispatcher`;
- Schermata di errore offline;
- Asset completi pronti per Google Play Store (`play-store-icon-512.png`, `store/en-GB/` con immagine in primo piano e screenshot 1080×1920, `store/listing.md`).

### Compilazione e pubblicazione

1. Apri la cartella `projects/faceguess/android` in Android Studio (File → Open).
2. Attendi il completamento della sincronizzazione di Gradle.
3. Copia `keystore.properties.sample` in `keystore.properties` (ignorato da git) e inserisci le informazioni della tua chiave di firma per Google Play.
4. Genera il file Android App Bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Carica l'`.aab` generato (in `app/release/app-release.aab`) su Google Play Console.
6. Copia i testi della scheda dello store da `android/store/listing.md` e usa l'icona e l'immagine in evidenza fornite.
