# LetterHop

Il gioco di parole di Saverio Morelli: da una parola all'altra cambiando una lettera alla volta (PUZZA → POZZA → COZZA).
Disponibile come web app e come applicazione Android per Google Play Store.

## Regole e punteggio

- **Tre livelli**, definiti dal minimo di parole tra l'inizio e la fine: Base 4, Media 6, Difficile 8.
- **Il minimo è esatto**: è misurato sull'intero dizionario, quindi nessun percorso è più corto. Almeno una soluzione
  minima usa solo parole comuni.
- **Parole libere**: si possono usare tutte le parole che si vuole, ma non due volte la stessa.
- **Punti**: 100 / 200 / 300 per livello, −10 per ogni parola oltre il minimo, −15 per ogni suggerimento, minimo 10. Un
  suggerimento si può chiedere solo se dopo restano almeno 10 punti. Annullare è gratis. Con "Mostra la soluzione" il
  puzzle non dà punti.
- **Stelle**: 3 con il minimo e senza suggerimenti, 2 con al massimo due parole in più, altrimenti 1.
- **Lunghezza**: 5 lettere (predefinita) o 6.

## Caratteristiche

- **Multilingua**: interfaccia e dizionari in italiano, inglese, spagnolo, francese, tedesco, con tastiera QWERTY,
  AZERTY o QWERTZ.
- **Suggerimenti a due passi**: prima la lettera da cambiare, poi la parola.
- **Partita salvata in automatico**, tempo con pausa, tema chiaro e scuro.
- **100% offline** dopo la prima apertura (`sw.js` salva anche i cinque dizionari). Nessuna richiesta a terzi: i font
  sono in `fonts/`.
- **Senza account né pubblicità**.

## I dizionari

`words/<lingua>.json` contiene:

- `words`: le parole per lunghezza, prima quelle comuni e poi le altre, ordinate per frequenza. I suggerimenti scelgono
  sempre la parola più familiare.
- `puzzles`: per lunghezza e minimo, fino a 600 coppie "inizio fine".

Gli accenti vengono ignorati (CITTÀ → CITTA). Le parole con ñ, ä, ö, ü, ß, œ non vengono usate, quindi la tastiera resta
A–Z.

Per rigenerarli servono le fonti elencate in `words/CREDITS.md` e, in un ambiente Python, `wordfreq`, `spylls`:

1. `tools/check_words.py <lingua> <cartella di lavoro>` tiene le parole accettate da Hunspell.
2. `tools/build_words.py <cartella di lavoro> <progetto>` toglie le parole escluse, costruisce il grafo e sceglie i
   puzzle.

`tools/filters.py` contiene le liste riviste a mano: `DROP` (fuori dal gioco), `LOAN` (valide ma mai inizio, fine o
soluzione), `SAFE` (parole normali catturate per errore dalle liste di volgarità). `tools/make_icons.py` disegna icone e
immagini. La cartella `tools/` non è accessibile dal browser.

## App Android (Google Play)

La cartella `android/` è un progetto Android Studio (Java) con una shell nativa WebView a schermo intero:

- Orientamento portrait, sistema edge-to-edge con barre coordinate al tema chiaro/scuro;
- Tasto indietro: chiude prima ciò che è aperto nel gioco (`window.onAndroidBack` in `game.js`);
- Schermata di errore offline;
- Icona adattiva con simbolo trasparente e icona tematica di Android 13+;
- Asset per Google Play Store (`play-store-icon-512.png`, `store/en-GB/`, `store/listing.md`).

### Compilazione e pubblicazione

1. Apri la cartella `projects/letterhop/android` in Android Studio (File → Open).
2. Attendi la sincronizzazione di Gradle.
3. Copia `keystore.properties.sample` in `keystore.properties` (ignorato da git) e inserisci le credenziali della tua
   chiave di firma per Google Play.
4. Genera il bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Carica il file `app-release.aab` su Google Play Console.
6. Compila i testi dello store usando `android/store/listing.md` e carica l'icona a 512px e l'immagine in primo piano.
