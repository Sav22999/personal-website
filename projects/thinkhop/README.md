# ThinkHop

Il gioco di logica e matematica di Saverio Morelli: un salto di ragionamento alla volta (2 ? 3 = 8 → il segno è ^).
Web app installabile (PWA) e app Android per Google Play. È elencata tra i progetti del sito, nella sezione «Built with AI».

## Il gioco

- **500 livelli in 50 mondi**, 5 domande ciascuno. La difficoltà cresce da un livello all'altro e dentro ogni livello
  (`stageTiers` in `engine.js`).
- **Otto tipi di domanda**, sbloccati strada facendo (`UNLOCK` in `engine.js`): segno mancante e sequenze (livello 1),
  algebra della frutta (3), intruso (5), chi arriva prima (7), somme strane (10), puro nonsense (13), indovinelli (16).
- **Superare un livello**: almeno metà delle risposte giuste. 3 stelle dal 90%, 2 dal 70%, 1 dal 50%.
- **Quattro gradi di difficoltà**: facile, media, difficile, esperto. Il livello 100 è tutto da esperti.
- **Punti**: 100 / 200 / 300 / 400 secondo la difficoltà, fino a +50% per la velocità e +10% per ogni risposta di fila (massimo
  +50%). Senza limite di tempo.
- **Suggerimenti**: si pagano con i punti (50 / 100 / 150 / 200 secondo la domanda) e nascondono risposte sbagliate. All'inizio
  si ricevono 100 punti in regalo.
- **Sfida del giorno**: 10 domande uguali per tutti, con la serie di giorni. **Sopravvivenza**: 3 vite.
- **Dopo ogni domanda** si vede la spiegazione.
- **Interfaccia e domande in cinque lingue**: italiano, inglese, spagnolo, francese, tedesco. Il pannello admin è in
  italiano e inglese.
- **Funziona offline** dopo la prima apertura; l'account è facoltativo.

## Le domande

Sette tipi sono generati da `engine.js`, gli indovinelli sono scritti a mano in `riddles.js` (la risposta giusta è sempre
la prima). Ogni generatore garantisce una sola risposta giusta. Per controllarlo:

```bash
node tools/check_questions.js
```

Lo script genera migliaia di domande e verifica sillogismi, ordini, segni, livelli, punti e statistiche.

## Account

Email e password o Google. Il server tiene l'account e il riepilogo delle partite finite (`games`), mai le domande: così
statistiche, stelle e punti sono uguali su ogni dispositivo. Chi gioca da ospite, quando accede, porta le partite
nell'account. Password dimenticata: modulo di contatto, come FoodTrack e Giftwise.

Si può usare lo **stesso client OAuth «Applicazione web» di FoodTrack**: basta il Client ID in `credentials.php`
(`$google_client_id`) o nel campo di `install-db.php`.

## Esercizi e difficoltà

Ogni tipo di esercizio è un «pattern» con una difficoltà `d` da 0.00 a 1.99 (`engine.js`, `puzzles.js`, `riddles.js`,
`riddles-more.js`): il giocatore vede solo la fascia (facile < 0.50, media < 1.00, difficile < 1.50, esperto). I livelli
chiedono una `d` sempre crescente, la matematica è limitata a una domanda su quattro e una domanda già vista non
torna: il dispositivo ricorda le ultime 8000 (la sfida del giorno fa eccezione, perché è uguale per tutti). Allo scadere del
tempo la domanda conta come sbagliata. `node tools/check_questions.js` controlla ogni pattern e il percorso dei 500 livelli.

## Mascotte

`mascots.js`: Punto, Sillo, Dedo e Neuro si dividono i tipi di domanda, quindi si alternano da sole. Danno un consiglio di
metodo (mai la soluzione) le prime 3 volte per tipo, poi una battuta ogni tanto; reagiscono alle risposte e al tempo che
finisce, presentano i mondi e commentano la fine del livello.

## Username, classifica e amici

L'username è l'unico dato pubblico: compare nella classifica giornaliera e nella ricerca amici. Dal profilo ognuno sceglie se comparire in classifica (se dice no resta trovabile nella ricerca amici). Nome ed email non
arrivano mai agli altri giocatori. Gli amici vanno in una sola direzione, senza richieste da accettare. I punteggi sono
calcolati sul dispositivo: il server scarta solo quelli impossibili, quindi la classifica non è a prova di imbroglio.

## Pannello admin

Si apre dal link **Admin** in fondo alla home o da `admin.html`. In **Domande** si cambiano difficoltà e stato di ogni esercizio.

`admin.html`, solo per gli account con ruolo amministratore (il server lo controlla a ogni chiamata):

- **Panoramica**: giocatori, partite, risposte giuste per tipo, livelli superati su giocati.
- **Giochi**: accende o spegne i tipi di domanda, cambia domande per livello e costo dei suggerimenti, mostra ed elimina
  le partite recenti.
- **Domande**: crea e modifica domande (italiano e inglese obbligatori, le altre lingue facoltative), spegne gli
  indovinelli inclusi nel gioco. Le domande compaiono tra gli indovinelli.
- **Giocatori**: cerca, disattiva, elimina, nomina altri amministratori.

Il gioco legge impostazioni e domande con `auth.config` e ne tiene una copia per l'uso offline.

## Installazione sul server

1. Carica la cartella `projects/thinkhop/`.
2. Apri `https://www.saveriomorelli.com/projects/thinkhop/install-db.php`: inserisci le credenziali MySQL (va bene anche il
   database di FoodTrack, le tabelle hanno il prefisso `th_`), il primo amministratore e, facoltativo, il Client ID di Google.
3. La pagina crea le tabelle e scrive `credentials.php`, poi si blocca da sola. Per sicurezza eliminala dal server.

Con `credentials.php` le tabelle si creano da sole alla prima richiesta e gli account si attivano subito: `install-db.php` serve per il primo amministratore e resta aperto (con la password del database) finché non ne esiste uno. Senza `credentials.php` il gioco funziona lo stesso, sul solo dispositivo. In alternativa all'installer:
phpMyAdmin → `install/thinkhop.sql` (rigenera con `php tools/build-sql.php`), poi copia `credentials-sample.php`.

## Installare l'app

- **Dal browser**: `manifest.webmanifest` e `sw.js` rendono il gioco installabile.
- **Android**: `android/` è un progetto Android Studio (Java) con la web app in una WebView. Parti native: «Continua con
  Google» con il selettore di sistema (`window.ThinkHopAndroid`), tasto indietro, link esterni nel browser.

Passaggi per Google Play:

1. Apri `android/` in Android Studio e attendi Gradle. Package: `com.saverio.thinkhop` (non si può più cambiare dopo la
   prima pubblicazione).
2. Copia `keystore.properties.sample` in `keystore.properties` con i dati della chiave.
3. **Login con Google**: in Google Cloud Console, nello stesso progetto del client web, crea un ID client OAuth di tipo
   **Android** con il package e l'impronta **SHA-1** di ogni chiave che firma l'app.
4. Genera l'`.aab`. Testi in `android/store/listing.md`. **Gli screenshot sono ancora da fare**, dal gioco pubblicato.
5. Per aprire i link nell'app aggiungi a `/.well-known/assetlinks.json` l'oggetto di `android/assetlinks.sample.json` con
   le impronte **SHA-256**.

## Struttura

```
thinkhop/
├── index.php, privacy.html, manifest.webmanifest, sw.js
├── engine.js      domande, livelli, punti, statistiche (senza DOM)
├── riddles.js     indovinelli in cinque lingue
├── game.js, ui.js, account.js, i18n.js, releases.js, style.css
├── admin.html, admin.js, admin.css
├── api/index.php  API JSON
├── lib/           bootstrap (sessioni, sicurezza, Google) e tabelle
├── install-db.php, install/, credentials-sample.php
├── tools/         controllo delle domande, icone, SQL (bloccata dal browser)
└── android/
```

`tools/make_icons.py` disegna tutte le icone e le immagini.

## Sicurezza

Password con `password_hash`, sessione con token casuale in cookie HttpOnly/Secure/SameSite (nel database solo l'hash
SHA-256), token CSRF su ogni modifica, limiti ai tentativi, `credentials.php`, `lib/`, `install/` e `tools/` bloccati dal
browser. I punti partono dal dispositivo e il server ne controlla solo i limiti: vanno bene per le statistiche, ma una
classifica pubblica richiederebbe di calcolarli sul server.
