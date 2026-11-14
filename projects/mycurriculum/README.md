# MyCurriculum

Write a CV once, in as many languages as you need, pick a template, download each language as a PDF and share it as a
web page (`?user=NAME`). An existing CV can be imported from a PDF.

## Stack

- PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+ (PDO), tables prefixed `mc_`. Same account code as Forkville.
- No build step: ES modules in `assets/`. Vendored, no CDN: `vendor/pdfmake.min.js` (0.2.10, makes the PDFs),
  `vendor/pdf.min.mjs` + `vendor/pdf.worker.min.mjs` (pdf.js 4.4.168, reads PDFs to import).
- Typefaces in `fonts/` (the same TTF files in the web page and in the PDF; list in `FONTS`, `assets/cv.js`): Roboto and
  PT Serif (ids `sans` and `serif`), plus Inter, Open Sans, Source Sans 3, Montserrat, Poppins, Lora, Merriweather,
  EB Garamond, Libre Baskerville and Playfair Display in `fonts/<id>/` (static instances from @expo-google-fonts, SIL OFL,
  subset to the Latin alphabet without hinting). The PDF loads only the typeface the CV uses.
- Stack Sans Notch is only for the "Saverio Morelli" credit.

## Install

1. Upload the folder, open `install-db.php`, fill in the database (and optionally the Google client ID). It writes
   `credentials.php` (git-ignored, blocked by `.htaccess`). Delete `install-db.php` afterwards.
   Alternatively import `install/mycurriculum.sql` and copy `credentials-sample.php` to `credentials.php`.
2. Email: confirmation links use `$mail_config` in `credentials.php`, or the site's `/.mail-config.php`.
3. Google sign-in (optional): set `$google_client_id` (OAuth "Web application" client, add the site origin).

## Accounts

- Email and password: the address must be confirmed (`?verify=` link) before the editor opens.
- Google: confirmed by Google, no email check; a new account picks the address of its public CV and accepts the privacy notice.
- The account email is unique and can never be changed. The public address (`username`) can.

## The CV

- One CV per account, one JSON row (`mc_cvs`): `settings` (template, colour, font, languages, public, which contacts
  show), `profile` (shared by all languages: name, contacts, links, birth date) and `content` (a full copy of the texts
  per language: headline, location, summary, custom fields, sections, footer). See the comment at the top of `lib/cv.php`.
- Projects and volunteering saved by older versions as lists with one date are converted to timelines when the CV
  opens (`upgradeProjects` in app.js).
- Section types: `timeline` (experience, education, projects, volunteering), `skills` (with 0–5 level), `langs`, `list`, `text`.
  Timeline entries may also have a link (projects), an EQF level 1–8 and a grade (education).
- Adding a language copies another one; section titles and the consent footer that were never edited are translated.
- Besides the ten built-in languages, the user can add any other one (`settings.customLangs`: name, ISO 639 code that
  formats the dates, and the small CV words — "present", "Page X of Y"… — which fall back to English). It has a globe
  instead of a flag. The PDF fonts cover the Latin alphabet only.
- Everything the browser sends is rebuilt field by field in `lib/cv.php` (unknown keys dropped, strings cut, only
  http(s)/mailto links).
- The photo is cropped and resized in the browser (480 px JPEG) and stored in `mc_photos`; it is served by
  `api/?r=cv.photo` only if the CV is public and the photo is shown (or to its owner).

## Editor

- Drag and drop (mouse, touch, pen) for sections, entries, skills, languages, links and custom fields; the grip is
  also a keyboard control (↑/↓). Sections fold to their title bar while one is dragged. Sections and entries collapse.
- Activity history with undo/redo (buttons, Ctrl/⌘+Z, Ctrl/⌘+Shift+Z): every change is a snapshot of the document with
  a readable label; typing in one field within 2 s is one step. The photo (stored apart) is not part of it.
- The CV preview is hidden by default (the form gets the whole width) and remembered per device.

- On wide screens with the preview hidden, an outline of the content sits beside the form (jump to a section,
  collapse/expand all). Sections and entries also have ↑/↓ buttons.
- Saves carry the revision of the CV (`cvs.rev`, schema v2): if another window or device saved meanwhile, the server
  answers `cv_conflict` and the editor stops and offers to reload, so nothing is overwritten silently.

## Web app and Android

- `manifest.webmanifest` + `sw.js`: installable; the shell, fonts and icons are cached, the API never is. Without a
  connection pages fall back to `offline.html`. Bump `VERSION` in `sw.js` when the shell changes.
- `android/`: the app for Google Play, a full-screen WebView like Fucina (package `com.saverio.mycurriculum`). Native
  parts through `window.MyCurriculumAndroid`: Google sign-in with the system account picker (Google does not allow it in
  a WebView), saving the PDF with the system "Save as" (a WebView cannot download), sharing the public link. Links to
  `/projects/mycurriculum/` open in the app once `/.well-known/assetlinks.json` lists it.
  Build: Android Studio, or `gradle assembleDebug` (Gradle 8.13, JDK 17+); release steps in `android/store/listing.md`.
- Store material: `android/store/listing.md` (texts IT/EN, data safety), `android/play-store-icon-512.png`, feature
  graphics in `android/store/*/`, phone screenshots in `store/screens/{it,en}/` (also used by the manifest).

## Public page and PDF

- `index.php?user=NAME[&lang=xx]` (`lib/public-page.php`) embeds `public_cv()`: hidden contacts, fields and sections are
  removed on the server, so the page shows exactly what the PDF shows. A private CV is visible only to its owner, as a
  preview. The page is `noindex`. Visits are counted without storing anything about the visitor.
- `assets/cv.js` builds one model per language; `renderCV()` draws it in HTML (`assets/cv.css`), `assets/pdf.js` draws
  the same model with pdfmake (real, selectable text). Keep the two in step when changing a template.
- Templates: classic, modern (coloured sidebar), minimal, elegant, banner (coloured band, entries as "TITLE – ORGANIZATION –
  dates"), timeline (vertical line with dots), compact (dense, two columns), studio (tinted column on the right), monogram
  (initials in a circle, headings with a bar), contrast (dark header, dates on the right), swiss (title column on the
  left, fine rules). Any accent colour, twelve typefaces.
- Contact separators ("|", "·") never start a line: in the PDF the lines are laid out by measuring the text
  (`contactLines` in pdf.js), on the web the marks sit in the gap and are hidden at line starts (`fixContactSeparators`).

## PDF import

`assets/import.js`, entirely in the browser: pdf.js text → lines (two-column layouts read one column at a time when
both columns have headings) → contacts, name (largest text on page 1), headings (known words in ten languages or short
capitals that look like the known headings), one timeline entry per date range or per bold title, bold text from the
font names. It understands Europass CVs: "TITLE – ORGANIZATION – dates – PLACE" headers, dd/mm/yyyy dates, "EQF level",
the language block (mother tongue, CEFR grid), "Label: value" personal data and links hidden behind redirects. The editor
replaces the sections of the current language, fills only empty personal fields and offers Undo. Tested against PDFs
made by all eleven templates (two different CVs) and a real Europass CV.

## Tools

- `python3 tools/make_icons.py` — PNG icons, `og-image.png` and `/images/projects/mycurriculum.png` from `icons/favicon.svg`.
- `php tools/build-sql.php` — regenerates `install/mycurriculum.sql` from `lib/schema.php`.
- `java tools/Screens.java <app url> <session cookie> store/screens <public user>` — store and manifest screenshots from
  the running app (Chrome headless over the DevTools protocol; use a demo account on a test database).
- `python3 tools/make_icons.py` also writes the Android launcher icons, the Play icon and the feature graphics.
