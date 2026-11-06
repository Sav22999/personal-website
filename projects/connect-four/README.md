# Connect Four (Forza Quattro)

Il classico gioco di allineamento a 4 pedine sviluppato da Saverio Morelli.
Disponibile come web app e come applicazione Android per Google Play Store.

## Caratteristiche

- **Stanze online con codice**: gioca con gli amici condividendo un codice stanza a 6 caratteri o il link d'invito diretto.
- **Matchmaking casuale**: trova avversari in rete istantaneamente.
- **Sullo stesso dispositivo**: modalità Pass & Play a turno su smartphone o tablet.
- **Contro il computer**: motore AI con diversi livelli di sfida.
- **Personalizzazione gettoni**: combinazioni di colori vivaci per le pedine e la griglia.
- **Chat in tempo reale**: invia messaggi durante la partita.
- **Tema Auto, Chiaro o Scuro**: schede su desktop, un pulsante sui telefoni (`theme.js`).
- **Multilingua**: italiano, inglese, spagnolo, francese, tedesco.
- **Senza registrazione né pubblicità**: 100% gratuito.
- **Funziona anche offline**: il service worker (`sw.js`) apre il gioco senza connessione, per le partite sullo stesso dispositivo o contro il computer.
- **Nessuna richiesta a terzi**: i font (Fredoka e Nunito, licenza SIL OFL) sono in `fonts/`.
- **Dati temporanei**: le stanze online inattive vengono cancellate ogni notte da `projects/cron/cleanup.php`.

## App Android (Google Play)

La cartella `android/` è un progetto Android Studio (Java) con una shell nativa WebView a schermo intero:

- Layout edge-to-edge con adattamento alla barra di sistema;
- Deep Links / App Links automatici per l'apertura immediata delle stanze (supporta sia `/projects/connect-four/` che `/projects/forza-quattro/`);
- Tasto indietro: chiude prima ciò che è aperto nel gioco (`window.onAndroidBack` in `game.js`);
- Schermata di errore offline;
- Icona adattiva con simbolo trasparente e icona tematica di Android 13+ (`ic_launcher_foreground`, `ic_launcher_background`, `ic_launcher_monochrome`);
- Asset completi pronti per Google Play Store (`play-store-icon-512.png`, `store/en-GB/` con immagine in primo piano e screenshot 1080×1920, `store/listing.md`).

### Compilazione e pubblicazione

1. Apri la cartella `projects/connect-four/android` in Android Studio (File → Open).
2. Attendi il completamento della sincronizzazione di Gradle.
3. Copia `keystore.properties.sample` in `keystore.properties` (ignorato da git) e inserisci le informazioni della tua chiave di firma per Google Play.
4. Genera il file Android App Bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Carica l'`.aab` generato (in `app/release/app-release.aab`) su Google Play Console.
6. Copia i testi della scheda dello store da `android/store/listing.md` e usa l'icona e l'immagine in evidenza fornite.
