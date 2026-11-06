# Rendicontazione Ore &middot; Portale Privato

Applicazione web riservata per la gestione, la registrazione e il calcolo automatico delle ore lavorative (ordinarie e straordinarie), sviluppata specificamente per la rendicontazione del lavoro del papà a partire dal foglio Excel storico.

> **Nota di riservatezza:** Questo progetto è privato e non è incluso nell'elenco pubblico dei progetti né nella sitemap/portfolio.

---

## 🚀 Caratteristiche Principali

- **Accesso con Google (Sign in with Google)**:
  - Autenticazione sicura tramite Google Identity Services.
  - Verifica server-side del token crittografico ID di Google.
  - Ruoli gerarchici:
    - **Master Admin (Saverio)**: controllo totale, gestione ruoli admin/utente, approvazioni, impostazioni.
    - **Amministratore (Admin)**: approvazione manuale dei nuovi utenti registrati, consultazione e modifica delle ore dei lavoratori.
    - **Utente (Papà)**: registrazione e consultazione delle proprie ore, riepiloghi mensili e annuali, esportazione.
- **Workflow di Approvazione Manuale**:
  - Quando un nuovo utente accede con Google per la prima volta, il suo account viene creato con stato `pending` (*In attesa di approvazione*).
  - Un amministratore o il Master Admin riceve la notifica e approva manualmente l'accesso con un clic dalla scheda **Utenti**.
- **Funzione Cronologia & Ripristino (Audit Log)**:
  - Traccia in tempo reale ogni operazione: aggiunta di turni, modifiche a orari/note, eliminazioni, orari di timbratura e chi ha effettuato la modifica.
  - **Permessi differenziati**:
    - L'**utente standard (Papà)** può consultare la cronologia per verificare le attività sul proprio foglio ore, ma **non** può annullare le modifiche.
    - Solo gli **amministratori** (Admin e Master Admin) visualizzano il pulsante **"↩️ Annulla questa modifica"**: cliccandolo, il sistema ripristina automaticamente lo stato esatto precedente della giornata e delle fasce orarie, registrando l'annullamento nell'audit trail.
- **Replica Fedele & Potenziata dell'Excel**:
  - Gestione multi-fascia giornaliera (es. turno mattina 07:00-12:00, pomeriggio 13:00-16:00, straordinario 16:00-17:00).
  - Conteggio e separazione automatica delle **Ore Ordinarie** e **Ore Straordinarie**.
  - Calcolo del **Totale Parziale Progressivo** cumulativo lungo tutto il mese (corrispondente alla colonna G dell'Excel originale).
  - **Superamento del limite 24h di Excel**: il foglio di calcolo formattava le ore con rollover ogni 24 ore (es. 178 ore venivano mostrate come 10:00:00); il portale calcola e visualizza sia il totale reale cumulativo (es. 178h) che i formati decimali e standard.
  - Tag ed eventi specifici: ☔ Pioggia / maltempo con orario di timbratura, 🏥 Malattia, 🏖️ Ferie, ⏱️ Permesso orario, 🩺 Visite mediche.
- **Riepilogo Annuale Interattivo**:
  - Dashboard con metriche annuali: ore ordinarie totali, straordinari totali, totale ore lavorate, giorni lavorati, media ore/giorno, giorni con pioggia.
  - Schede interattive per ciascuno dei 12 mesi con passaggio immediato al dettaglio mensile.
- **Turni Predefiniti Rapidi**:
  - Un clic per applicare il turno standard della giornata:
    - *Invernale*: 07:00-12:00 (5h ord) + 13:00-16:00 (3h ord) + 16:00-17:00 (1h straord) = 9 ore.
    - *Estivo*: 06:00-14:00 (8h ord) + 14:00-15:00 (1h straord) = 9 ore.
    - *Sabato*: 07:00-12:00 (5h straord).
- **Esportazione & Stampa**:
  - Download in formato CSV compatibile con Microsoft Excel.
  - Foglio di stile ottimizzato per la stampa e il salvataggio in PDF.
- **Doppio Motore Database (Aruba MySQL + Fallback SQLite)**:
  - In produzione su Aruba si connette direttamente al database MySQL configurato (`dad_rend_*`).
  - In ambiente locale (sviluppo su Mac/offline) passa in modo trasparente e automatico a un database locale SQLite (`data/rendicontazione.sqlite`), consentendo test immediati senza bisogno di connessioni di rete esterne.

---

## 📁 Struttura della Cartella

```
projects/dad/rendicontazione/
├── .gitignore                    # Esclude credentials.php e database locali
├── .htaccess                     # Protezione sicurezza, noindex e blocco cartelle lib/ e data/
├── credentials-sample.php        # Modello credenziali per installazione
├── credentials.php               # Credenziali database reali e Google Client ID
├── index.php                     # Portale principale Single Page Application
├── install-db.php                # Pannello web di verifica e installazione database
├── README.md                     # Questa guida
├── api/
│   └── index.php                 # Endpoint REST JSON
├── assets/
│   ├── app.css                   # Stili responsive, dark/light theme, stampa
│   ├── app.js                    # Controller applicazione SPA in Vanilla JS
│   ├── favicon.svg               # Icona orologio vettoriale
│   └── manifest.webmanifest      # PWA manifest per installazione su smartphone
├── data/                         # Cartella protetta da .htaccess
│   ├── source-2026.csv           # Dati storici 2026 originali
│   └── rendicontazione.sqlite    # Database locale per sviluppo offline
└── lib/
    ├── bootstrap.php             # Connessione PDO, sessioni, Google Auth, helpers
    ├── schema.php                # Definizione tabelle MySQL e SQLite
    ├── models.php                # Logica applicativa, query, calcoli ore
    ├── importer.php              # Parser CSV e importatore orari
    └── seed-2026.php             # Caricamento iniziale dei dati 2026 del papà
```

---

## ⚙️ Configurazione & Credenziali

Il file `credentials.php` (protetto e ignorato da git) contiene:

```php
$localhost_db       = "89.46.111.125";
$username_db        = "Sql1362232";
$password_db        = "<DATABASE PASSWORD>";
$database_name      = "Sql1362232_4";
$table_prefix       = "dad_rend_";

$google_client_id   = "126260557873-mss9iuldd7uttsj9pqd0oqbtntvvo6hl.apps.googleusercontent.com";
$master_admin_email = "saverio.morelli@gmail.com";
$debug              = false;
```

---

## 🔒 Sicurezza & Riservatezza

1. Il file `.htaccess` impedisce l'indicizzazione da parte dei motori di ricerca (`X-Robots-Tag: noindex, nofollow`).
2. I file sensibili (`credentials.php`, file `.sqlite`, dump `.sql`, cartelle `lib/` e `data/`) sono bloccati dall'esterno con errore 404 o 403.
3. Il token di sessione è conservato in cookie con flag `HttpOnly`, `SameSite=Lax` e `Secure` quando in HTTPS.
4. L'applicazione **non** è linkata in `projects/index.php`, `portfolio/index.php`, `llms.txt` o `sitemap.xml`.
