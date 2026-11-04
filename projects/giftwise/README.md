# Giftwise

Liste dei desideri da condividere con gli amici, che si organizzano tra loro **senza rovinare la sorpresa**.

- Ogni utente crea le sue **liste dei desideri** (compleanno, Natale, nozze, nascita, laurea, anniversario, casa nuova, altro), con data dell'evento e un messaggio per gli amici.
- In ogni lista aggiunge i **desideri**: nome, link al prodotto, immagine, prezzo, quantità, note e «il più desiderato». Incollando (o condividendo dall'app di un negozio) un link, Giftwise compila da solo nome, immagine e prezzo.
- Ogni utente ha uno **username pubblico** (3–20 caratteri: lettere, numeri, `.` e `_`), proposto dal nome alla registrazione e modificabile dal Profilo. Con lo username (o con l'email esatta) si **cercano le persone** e le si **segue**; si può smettere di seguire o togliere qualcuno dai propri follower in qualsiasi momento.
- **Si vedono le liste di chi si segue.** Di default chi vuole seguirti deve essere approvato (le liste dei desideri sono personali); dal Profilo si può scegliere «Chiunque, senza chiedere».
- Il **link d'invito** resta: chi lo apre (anche registrandosi in quel momento) e chi l'ha mandato si seguono subito a vicenda.
- Sulle liste di chi segui puoi segnare un desiderio come **«Ci sto pensando»** o **«Lo prendo io»** e **chattare** con gli altri follower, per ogni desiderio o per tutta la lista.
- **Chi ha creato la lista non vede niente di tutto questo.** La regola è applicata dal server: le API non restituiscono mai prenotazioni e messaggi al proprietario della lista, e rifiutano le sue richieste di vederli o crearli (vedi il commento in testa a `api/index.php`).
- Una lista può essere visibile a tutti i follower o solo ad alcuni; si può archiviare (sparisce per gli amici) o eliminare.

Web app in PHP + MySQL con lo stesso design system di FoodTrack (rosa lampone, chiaro e scuro, mobile e desktop), in italiano e inglese, installabile come app (PWA).

## Aggiornamenti del database

Al primo accesso dopo un aggiornamento il database si aggiorna da solo (`migrate_schema` in `lib/schema.php`). Versione 2: username per tutti gli account esistenti (generati dal nome, modificabili) e le vecchie amicizie diventano «follow» nei due sensi.

## Struttura

```
giftwise/
├── index.html, privacy.html, manifest.webmanifest, sw.js
├── install-db.php          installazione guidata (crea le tabelle gw_ e credentials.php)
├── credentials-sample.php  modello delle credenziali (credentials.php è in .gitignore)
├── api/index.php           API JSON
├── lib/                    bootstrap (sessioni, sicurezza, Google), schema, anteprima link, email, traduzioni
├── assets/                 app.js, ui.js, api.js, i18n.js, app.css + giftwise.css, icone, font
├── install/giftwise.sql    le stesse tabelle per phpMyAdmin (rigenera con php tools/build-sql.php)
└── tools/build-sql.php
```

## Installazione sul server

1. Carica la cartella `projects/giftwise/` (via FTP o con il deploy del sito).
2. Apri `https://www.saveriomorelli.com/projects/giftwise/install-db.php`, inserisci le credenziali del database MySQL (va bene anche lo stesso database di FoodTrack: le tabelle hanno il prefisso `gw_`) e, facoltativo, il Client ID di Google.
3. La pagina crea le tabelle e scrive `credentials.php`. Poi si blocca da sola (si può rieseguire solo con la password del database). Per sicurezza eliminala dal server.
4. Apri l'app e crea il primo account con «Crea account».

In alternativa: phpMyAdmin → importa `install/giftwise.sql`, poi copia `credentials-sample.php` in `credentials.php` e compilalo.

## Accesso con Google (facoltativo)

Si può usare lo **stesso client OAuth «Applicazione web» di FoodTrack**: l'origine `https://www.saveriomorelli.com` è già autorizzata. Copia il Client ID in `credentials.php` (`$google_client_id`) o nel campo di `install-db.php`.

## Email

Email di benvenuto, «X vuole seguirti» (quando serve la tua approvazione) e «X ha aperto il tuo invito», nella lingua di chi le riceve. Usa `$mail_config` in `credentials.php` se presente, altrimenti le impostazioni del modulo di contatto del sito (`/.mail-config.php`). Senza configurazione non parte nessuna email e l'app funziona lo stesso.

## Aggiungere un desiderio da un'altra app

- **Android (app installata dal browser)**: Giftwise compare nel menu «Condividi» di Chrome e delle app dei negozi (Web Share Target): il link arriva in «Aggiungi un desiderio» già compilato.
- **Ovunque**: copia il link del prodotto e incollalo nel campo «Link al prodotto».

L'anteprima del link la fa il server (`lib/preview.php`): legge Open Graph, Twitter card, JSON-LD e microdati; protezioni contro le richieste alla rete interna (solo porte 80/443, IP pubblici, connessione vincolata all'IP controllato, redirect verificati uno per uno, timeout brevi, massimo 1,5 MB). Alcuni negozi (per esempio Amazon) a volte rispondono con una pagina di verifica: in quel caso il titolo viene ricavato dall'indirizzo e il resto si compila a mano.

## Sicurezza

Password con `password_hash`, sessione con token casuale in cookie HttpOnly/Secure/SameSite (nel database solo l'hash SHA-256), token CSRF su ogni modifica, limiti ai tentativi (accesso, registrazione, inviti, anteprime, messaggi), `credentials.php`, `lib/`, `install/` e `tools/` bloccati dal browser.
