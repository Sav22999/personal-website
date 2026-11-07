# Pulizia giornaliera dei giochi online

`cleanup.php` fa due lavori:

- **Giochi:** cancella le stanze di Connect Four, Chess e FaceGuess senza modifiche e senza giocatori connessi da 24
  ore, insieme a giocatori, chat e carte. Lanciato una volta al giorno, i dati restano al massimo circa 48 ore
  dall'ultima attività: è il tempo dichiarato nelle privacy e nelle schede store.
- **FoodTrack, Giftwise, Fucina, ThinkHop e Fischietto:** cancella le sessioni scadute e i tentativi di accesso più vecchi di 2
  ore (l'IP serve solo al limite anti-abuso, che guarda indietro al massimo 60 minuti) e svuota IP e browser rimasti
  sulle sessioni delle versioni precedenti. Non tocca mai liste, prodotti, desideri, statistiche, progressi o altri
  dati degli utenti.

## Promemoria di ThinkHop

`thinkhop-reminders.php` è un job a parte, perché a mezzanotte un promemoria non serve a nessuno: va lanciato **una volta al
giorno alle 18:00**. Avvisa chi ha attivato le notifiche e oggi non ha giocato la sfida: della serie in pericolo se ne ha una,
altrimenti della sfida del giorno. Una notifica al massimo, solo per le tipologie lasciate accese. Cancella anche le notifiche
non lette da più di 3 giorni.

- **URL:** `https://www.saveriomorelli.com/projects/cron/thinkhop-reminders.php?key=LA_TUA_CHIAVE` (la stessa chiave)
- **Riga di comando:** `php /percorso/del/sito/projects/cron/thinkhop-reminders.php` (senza chiave)

## Installazione

1. Carica sul server tutta la cartella `projects/cron/`, compreso `credentials.php` (è escluso da git). Se manca, copia
   `credentials-sample.php` in `credentials.php` e imposta una chiave lunga e casuale.
2. Pianifica il job ogni giorno alle 00:00 con uno dei due modi:
    - **URL:** `https://www.saveriomorelli.com/projects/cron/cleanup.php?key=LA_TUA_CHIAVE`
    - **Riga di comando:** `php /percorso/del/sito/projects/cron/cleanup.php` (senza chiave)

## Risposte

- `200` con quanto è stato cancellato, per esempio
  `{"ok": true, "deleted_rooms": {"connect-four": 3, "chess": 0, "faceguess": 1}, "deleted_expired": {"foodtrack": {"sessions_anonymised": 0, "sessions": 2, "attempts": 14}, "giftwise": {"sessions_anonymised": 0, "sessions": 0, "attempts": 5}, "fucina": {"sessions_anonymised": 0, "sessions": 1, "attempts": 2}, "thinkhop": {"sessions_anonymised": 0, "sessions": 0, "attempts": 1}, "fischietto": {"sessions_anonymised": 0, "sessions": 1, "attempts": 3}}}`
- `403` chiave errata o assente
- `503` manca `credentials.php` o la chiave è troppo corta
- `500` la pulizia di almeno un gioco o un'app è fallita (il codice è nella risposta, i dettagli nel log degli errori di
  PHP) o il database non risponde. `SQLSTATE 42S02` significa che mancano le tabelle del gioco: va lanciato il suo
  `api/setup.php`

## Promemoria di Fucina

A parte dalla pulizia, pianifica ogni giorno alle 18:00 `https://www.saveriomorelli.com/projects/fucina/reminders.php?key=LA_TUA_CHIAVE`
(stessa chiave di `credentials.php`). Invia al massimo una notifica a persona: la serie a rischio o la sfida del giorno.
