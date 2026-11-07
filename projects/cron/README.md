# Pulizia giornaliera dei giochi online

`cleanup.php` fa due lavori:

- **Giochi:** cancella le stanze di Connect Four, Chess e FaceGuess senza modifiche e senza giocatori connessi da 24
  ore, insieme a giocatori, chat e carte. Lanciato una volta al giorno, i dati restano al massimo circa 48 ore
  dall'ultima attività: è il tempo dichiarato nelle privacy e nelle schede store.
- **FoodTrack, Giftwise e Fischietto:** cancella le sessioni scadute e i tentativi di accesso più vecchi di 2 ore (l'IP
  serve solo al limite anti-abuso, che guarda indietro al massimo 60 minuti) e svuota IP e browser rimasti sulle
  sessioni delle versioni precedenti. Non tocca mai liste, prodotti, desideri, statistiche o altri dati degli utenti.

## Installazione

1. Carica sul server tutta la cartella `projects/cron/`, compreso `credentials.php` (è escluso da git). Se manca, copia
   `credentials-sample.php` in `credentials.php` e imposta una chiave lunga e casuale.
2. Pianifica il job ogni giorno alle 00:00 con uno dei due modi:
    - **URL:** `https://www.saveriomorelli.com/projects/cron/cleanup.php?key=LA_TUA_CHIAVE`
    - **Riga di comando:** `php /percorso/del/sito/projects/cron/cleanup.php` (senza chiave)

## Risposte

- `200` con quanto è stato cancellato, per esempio
  `{"ok": true, "deleted_rooms": {"connect-four": 3, "chess": 0, "faceguess": 1}, "deleted_expired": {"foodtrack": {"sessions_anonymised": 0, "sessions": 2, "attempts": 14}, "giftwise": {"sessions_anonymised": 0, "sessions": 0, "attempts": 5}, "fischietto": {"sessions_anonymised": 0, "sessions": 1, "attempts": 3}}}`
- `403` chiave errata o assente
- `503` manca `credentials.php` o la chiave è troppo corta
- `500` la pulizia di almeno un gioco o un'app è fallita (il codice è nella risposta, i dettagli nel log degli errori di
  PHP) o il database non risponde. `SQLSTATE 42S02` significa che mancano le tabelle del gioco: va lanciato il suo
  `api/setup.php`
