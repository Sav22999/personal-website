# Chess

Gioco degli scacchi online e offline sviluppato da Saverio Morelli.
Disponibile come web app e come applicazione Android per Google Play Store.

## Caratteristiche

- **Stanze online con codice**: sfida gli amici condividendo un codice stanza a 6 caratteri o il link d'invito diretto.
- **Matchmaking casuale**: trova rapidamente avversari online.
- **Due giocatori sullo stesso dispositivo**: modalità pass & play su smartphone o tablet.
- **Contro il computer**: motore AI con diversi livelli di difficoltà.
- **Aiuti e regole**: evidenziazione delle mosse legali, guide per ciascun pezzo e suggerimenti.
- **Personalizzazione**: temi per scacchiera e set di pezzi, orologio di gioco regolabile, chat in tempo reale.
- **Tema Auto, Chiaro o Scuro**: schede su desktop, un pulsante sui telefoni (`theme.js`).
- **Multilingua**: italiano, inglese, spagnolo, francese, tedesco.
- **Senza account né pubblicità**: 100% gratuito.

## App Android (Google Play)

La cartella `android/` è un progetto Android Studio (Java) con una shell nativa WebView a schermo intero:

- Layout edge-to-edge con adattamento alla barra di sistema;
- Deep Links / App Links automatici per l'apertura immediata dei codici stanza (`https://www.saveriomorelli.com/projects/chess/?code=ABC123`);
- Gestione della navigazione indietro con `OnBackInvokedDispatcher`;
- Schermata di errore offline con pulsante di riprova personalizzato;
- Asset completi pronti per Google Play Store (`play-store-icon-512.png`, `store/en-GB/` con immagine in primo piano e screenshot 1080×1920, `store/listing.md`).

### Compilazione e pubblicazione

1. Apri la cartella `projects/chess/android` in Android Studio (File → Open).
2. Attendi il completamento della sincronizzazione di Gradle.
3. Copia `keystore.properties.sample` in `keystore.properties` (ignorato da git) e inserisci le informazioni della tua chiave di firma per Google Play.
4. Genera il file Android App Bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Carica l'`.aab` generato (in `app/release/app-release.aab`) su Google Play Console.
6. Copia i testi della scheda dello store da `android/store/listing.md` e usa l'icona e l'immagine in evidenza fornite.
