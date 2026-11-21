# MyCurriculum — scheda Google Play

**Package:** `com.saverio.mycurriculum` · **Categoria:** Produttività · **Classificazione:** PEGI 3 / Per tutti ·
**Privacy:** https://www.saveriomorelli.com/projects/mycurriculum/privacy.html · **Contatto:** il modulo del sito.

## Testi

**Nome (≤ 30):** MyCurriculum

**Descrizione breve IT (≤ 80):** Il tuo CV in ogni lingua: modelli, PDF e pagina web da condividere.
**Short description EN (≤ 80):** Your CV in every language: templates, PDF and a web page to share.

**Descrizione completa (IT):**
Scrivi il curriculum una volta e tienine una versione per ogni lingua che ti serve: italiano, inglese, francese, tedesco,
spagnolo e altre ancora, anche una lingua tutta tua. Quando aggiungi una lingua, titoli delle sezioni, date e consenso
privacy si traducono da soli: a te restano solo i testi.

• Undici modelli (Classico, Moderno, Minimal, Elegante, Fascia, Timeline, Compatto, Studio, Monogramma, Contrasto, Svizzero), il tuo colore e dodici caratteri.
• Ogni lingua diventa un PDF vero, con il testo selezionabile, i link scritti per intero e «Pagina X di Y».
• Una pagina web pubblica con il menu delle lingue: mostra solo quello che scegli tu.
• Importa il CV che hai già, anche Europass, da un PDF: i campi si compilano da soli (il file resta sul telefono).
• Esperienze, istruzione con livello EQF e voto, progetti, competenze e lingue con i pallini, campi personalizzati.
• Trascina sezioni e voci per riordinarle, annulla e ripeti, cronologia di tutte le modifiche.
• Accesso con email o con Google. Nessuna pubblicità.

**Full description (EN):**
Write your CV once and keep a version for every language you need: English, Italian, French, German, Spanish and more,
even a language of your own. When you add a language, section titles, dates and the privacy consent translate by
themselves: only your own texts are left.

• Eleven templates (Classic, Modern, Minimal, Elegant, Banner, Timeline, Compact, Studio, Monogram, Contrast, Swiss), your colour and twelve typefaces.
• Every language becomes a real PDF, with selectable text, links written out in full and "Page X of Y".
• A public web page with a language menu: it shows only what you choose.
• Import the CV you already have, Europass included, from a PDF: the fields fill themselves (the file stays on your phone).
• Experience, education with EQF level and grade, projects, skills and languages with dots, custom fields.
• Drag sections and entries to reorder them, undo and redo, a history of every change.
• Sign in with email or with Google. No ads.

## Grafica

| Elemento | File |
|---|---|
| Icona 512×512 | `android/play-store-icon-512.png` |
| Grafica in primo piano 1024×500 | `android/store/it-IT/feature-graphic-1024x500.png`, `android/store/en-GB/feature-graphic-1024x500.png` |
| Screenshot telefono 1080×1920 (IT) | `store/screens/it/1-editor.png` … `5-public.png` |
| Phone screenshots 1080×1920 (EN) | `store/screens/en/1-editor.png` … `5-public.png` |

Gli screenshot vengono dall'app vera con un CV dimostrativo (`php -S` + database di prova):
`java tools/Screens.java <url app> <cookie della sessione demo> store/screens <utente pubblico>`; la grafica con
`python3 tools/make_icons.py`.

## Sicurezza dei dati (Data safety)

- **Dati raccolti:** nome, email, numero di telefono e gli altri contenuti che l'utente scrive nel CV, foto (facoltativa).
  Scopo: funzionalità dell'app e gestione dell'account. Non condivisi con terze parti, nessuna pubblicità né analisi.
- **Crittografia in transito:** sì (HTTPS). **Cancellazione:** sì, dall'app (*Condividi → Account → Elimina account*).
- Il PDF importato è letto solo sul dispositivo e non viene inviato.
- Accesso con Google: Google tratta i dati secondo la propria informativa.

## Pubblicazione

1. Android Studio → apri `android/` (oppure Gradle 8.13 + JDK 17 o più recente).
2. Copia `keystore.properties.sample` in `keystore.properties` e crea la chiave di caricamento (vedi il file).
3. `Build > Generate Signed App Bundle` → `app/release/app-release.aab`, da caricare su Play Console.
4. Dopo il primo caricamento copia la SHA-256 della **chiave di firma dell'app** (Play Console → Integrità dell'app) in
   `assetlinks.sample.json` e aggiungi la voce a `/.well-known/assetlinks.json` del sito: così i link
   `…/projects/mycurriculum/` (CV pubblici, conferma email) si aprono nell'app.
5. Google Cloud Console: per l'accesso con Google nell'app crea un client OAuth di tipo **Android** con il package e la
   SHA-1 della chiave di firma (il client "Applicazione web" del sito resta quello usato per verificare il token).
