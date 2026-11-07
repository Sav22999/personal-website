# Rendicontazione Ore

Registro privato delle ore di lavoro, nato per sostituire il foglio Excel annuale. Non è collegato
dal sito (pagina progetti, portfolio, sitemap, `llms.txt`) e non viene indicizzato.

PHP e JavaScript senza build e senza dipendenze: si carica la cartella sul server e funziona.

## Cosa fa

- **Settimana**: da lunedì a domenica, con i totali, il grafico dei giorni e un pulsante per segnare
  in un colpo i giorni mancanti con l'orario solito (è una modifica sola in cronologia).
- **Mese**: un tocco su «Giornata normale» segna la giornata di oggi. L'orario proposto si impara
  dagli ultimi giorni, quindi segue da solo i cambi di stagione. Sabati e festivi hanno le loro regole:
  in un festivo tutte le ore sono straordinarie, come nel foglio originale.
  Il mese si apre nella vista dettagliata (una riga per giorno); la panoramica è un calendario con una
  settimana per riga e il totale di ogni settimana.
- **Giorno**: fasce orarie, straordinario, tipo di giornata (lavoro, pioggia, permesso, ferie, malattia), note.
- **Anno**: l'equivalente del foglio «Panoramica». I totali sono in minuti interi, quindi sempre esatti.
- **Cronologia**: ogni modifica con il prima e il dopo.
- **Altro**: Excel, stampa, orari rapidi, dimensione del testo, colori. Per gli admin anche utenti e importazione.

La guida per chi usa l'app è in `guida.html`, collegata dal login e da **Altro**, e si può stampare.
Le immagini in `assets/guida/` mostrano dati inventati: se le rifai, non usare mai ore vere.

## Ruoli

| | Utente | Amministratore | Master Admin |
|---|:-:|:-:|:-:|
| Segna e corregge le proprie ore | ✓ | ✓ | ✓ |
| Legge la cronologia delle proprie ore | ✓ | ✓ | ✓ |
| Vede e modifica le ore di tutti | | ✓ | ✓ |
| **Annulla le modifiche dalla cronologia** | | ✓ | ✓ |
| Approva utenti e importa da Excel | | ✓ | ✓ |
| Nomina gli amministratori | | | ✓ |
| Corregge nome ed email degli account | | | ✓ |
| Elimina gli account senza ore | | | ✓ |

Un account con ore registrate non si elimina, si disattiva: così le ore restano. Cambiando l'email di una
persona, il suo vecchio account Google smette di funzionare e le sessioni aperte vengono chiuse.

Si possono annullare le modifiche alle ore (aggiunte, correzioni, cancellazioni, importazioni).
Le modifiche ad account e orari rapidi sono elencate per gli admin ma non si annullano. Se un giorno è
cambiato di nuovo dopo la modifica da annullare, l'app avvisa prima di procedere. Anche l'annullamento
resta in cronologia e si può a sua volta annullare.

## Installazione

1. Copia `credentials-sample.php` in `credentials.php` e compilalo. Il file non va mai su git.
2. Carica la cartella sul server e apri la pagina: le tabelle (`dad_rend_*`) si creano da sole.
3. Entra con Google usando l'email indicata in `$master_admin_email`: diventi Master Admin.
4. In **Altro → Utenti** aggiungi la persona con la sua email Google: potrà entrare senza attendere.
5. Scegli quella persona in alto («Ore di») e in **Altro → Importa da Excel** carica il foglio annuale:
   prima di importare vedi il riepilogo mese per mese e ciò che non torna.

Le tabelle `dad_rend_work_days`, `work_shifts`, `presets` e `audit_log` della prima versione, se esistono,
non vengono usate e si possono eliminare a mano.

## Sviluppo locale

```bash
php -S localhost:8080 -t ../../..
```

Poi apri `http://localhost:8080/projects/dad/rendicontazione/`. Con il server integrato di PHP l'app usa
sempre `data/dev.sqlite` e mai il database vero, e mostra i pulsanti di accesso di prova. Questi esistono
solo con `php -S` da questo stesso computer (`is_local_dev()` in `lib/bootstrap.php`): su Apache non
possono attivarsi in nessun caso.

## Dati personali

Il repository è pubblico: credenziali, database e fogli Excel non devono mai entrarci. `.gitignore`
esclude `credentials.php`, `data/` e `*.xlsx`; i dati stanno solo nel database e arrivano dall'importazione.

## Struttura

```
api/index.php       le azioni JSON (api/index.php?action=...)
assets/app.js       viste e logica dell'interfaccia
assets/ui.js        DOM, date e ore, finestre, messaggi
assets/app.css      stile: telefono prima di tutto, chiaro e scuro, stampa
assets/icons/       icona dell'app
lib/bootstrap.php   configurazione, database, risposte JSON
lib/auth.php        account, sessioni, accesso con Google
lib/timesheet.php   giorni, validazione, totali, orari rapidi
lib/history.php     cronologia e annullamento
lib/xlsx.php        lettura e scrittura Excel in PHP puro (serve solo zlib)
lib/schema.php      tabelle per MySQL/MariaDB e SQLite
```
