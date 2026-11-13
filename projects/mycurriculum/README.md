# MyCurriculum

Write a CV once, in as many languages as you need, pick a template, download each language as a PDF and share it as a
web page (`?user=NAME`). An existing CV can be imported from a PDF.

## Stack

- PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+ (PDO), tables prefixed `mc_`. Same account code as Forkville.
- No build step: ES modules in `assets/`. Vendored, no CDN: `vendor/pdfmake.min.js` (0.2.10, makes the PDFs),
  `vendor/pdf.min.mjs` + `vendor/pdf.worker.min.mjs` (pdf.js 4.4.168, reads PDFs to import).
- Fonts in `fonts/`: Roboto and PT Serif (the same files in the web page and in the PDF), Stack Sans Notch only for the
  "Saverio Morelli" credit.

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
- Section types: `timeline` (experience, education…), `skills` (with 0–5 level), `langs`, `list`, `text`.
- Adding a language copies another one; section titles and the consent footer that were never edited are translated.
- Everything the browser sends is rebuilt field by field in `lib/cv.php` (unknown keys dropped, strings cut, only
  http(s)/mailto links).
- The photo is cropped and resized in the browser (480 px JPEG) and stored in `mc_photos`; it is served by
  `api/?r=cv.photo` only if the CV is public and the photo is shown (or to its owner).

## Public page and PDF

- `index.php?user=NAME[&lang=xx]` (`lib/public-page.php`) embeds `public_cv()`: hidden contacts, fields and sections are
  removed on the server, so the page shows exactly what the PDF shows. A private CV is visible only to its owner, as a
  preview. The page is `noindex`. Visits are counted without storing anything about the visitor.
- `assets/cv.js` builds one model per language; `renderCV()` draws it in HTML (`assets/cv.css`), `assets/pdf.js` draws
  the same model with pdfmake (real, selectable text). Keep the two in step when changing a template.
- Templates: classic, modern (coloured sidebar), minimal, elegant. Any accent colour, sans or serif.

## PDF import

`assets/import.js`, entirely in the browser: pdf.js text → lines (two-column layouts read one column at a time when
both columns have headings) → contacts, name (largest text on page 1), headings (known words in ten languages or short
capitals), one timeline entry per date range. The editor replaces the sections of the current language, fills only
empty personal fields and offers Undo. Tested against PDFs made by all four templates.

## Tools

- `python3 tools/make_icons.py` — PNG icons, `og-image.png` and `/images/projects/mycurriculum.png` from `icons/favicon.svg`.
- `php tools/build-sql.php` — regenerates `install/mycurriculum.sql` from `lib/schema.php`.
