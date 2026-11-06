# Sudoku

Il classico rompicapo logico 9×9 sviluppato da Saverio Morelli.
Disponibile come web app e come applicazione Android per Google Play Store.

## Caratteristiche

- **4 livelli di difficoltà**: Facile, Medio, Difficile, Esperto.
- **Note a matita**: candidati automatici o manuali per ogni cella.
- **Aiuto esplicativo**: suggerimenti logici passo per passo che spiegano *perché* una determinata cifra è corretta.
- **Evidenziazione conflitti**: controllo immediato di errori su righe, colonne e riquadri.
- **Tema chiaro e scuro**: stile adattivo basato sulle preferenze di sistema.
- **Statistiche e timer**: traccia i tuoi record personali.
- **100% offline**: nessuna connessione necessaria per giocare.
- **Multilingua**: italiano, inglese, spagnolo, francese, tedesco.
- **Senza account né pubblicità**: completamente gratuito.

## App Android (Google Play)

La cartella `android/` è un progetto Android Studio (Java) con una shell nativa WebView a schermo intero:

- Orientamento portrait ottimizzato per l'interazione con la griglia 9×9;
- Sistema edge-to-edge con colori della barra di stato coordinati al tema chiaro/scuro;
- Gestione della navigazione indietro con `OnBackInvokedDispatcher`;
- Schermata di errore offline;
- Asset completi pronti per Google Play Store (`play-store-icon-512.png`, `store/en-GB/` con immagine in primo piano e screenshot 1080×1920, `store/listing.md`).

### Compilazione e pubblicazione

1. Apri la cartella `projects/sudoku/android` in Android Studio (File → Open).
2. Attendi la sincronizzazione di Gradle.
3. Copia `keystore.properties.sample` in `keystore.properties` (ignorato da git) e inserisci le credenziali della tua chiave di firma per Google Play.
4. Genera il bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Carica il file `app-release.aab` su Google Play Console.
6. Compila i testi dello store usando `android/store/listing.md` e carica l'icona a 512px e l'immagine in primo piano.
