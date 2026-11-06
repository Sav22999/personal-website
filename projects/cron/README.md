# Pulizia giornaliera dei giochi online

`cleanup.php` cancella le stanze di Connect Four, Chess e FaceGuess senza modifiche e senza giocatori connessi da 24 ore, insieme a giocatori, chat e carte. Lanciato una volta al giorno, i dati restano al massimo circa 48 ore dall'ultima attività: è il tempo dichiarato nelle privacy e nelle schede store.

## Installazione

1. Carica sul server tutta la cartella `projects/cron/`, compreso `credentials.php` (è escluso da git). Se manca, copia `credentials-sample.php` in `credentials.php` e imposta una chiave lunga e casuale.
2. Pianifica il job ogni giorno alle 00:00 con uno dei due modi:
   - **URL:** `https://www.saveriomorelli.com/projects/cron/cleanup.php?key=LA_TUA_CHIAVE`
   - **Riga di comando:** `php /percorso/del/sito/projects/cron/cleanup.php` (senza chiave)

## Risposte

- `200` con il numero di stanze cancellate per gioco, per esempio `{"ok": true, "deleted_rooms": {"connect-four": 3, "chess": 0, "faceguess": 1}}`
- `403` chiave errata o assente
- `503` manca `credentials.php` o la chiave è troppo corta
- `500` la pulizia di almeno un gioco è fallita (il codice è nella risposta, i dettagli nel log degli errori di PHP) o il database non risponde. `SQLSTATE 42S02` significa che mancano le tabelle del gioco: va lanciato il suo `api/setup.php`
