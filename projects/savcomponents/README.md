# SavComponents — accounts API

The library and the documentation live in their own repository and site:
**https://savcomponents.com** (GitHub: Sav22999/savcomponents).
This folder of saveriomorelli.com only hosts what needs a database:

- `api/index.php` — JSON API: accounts (email + password, confirmation and password reset by email),
  favourite components, saved themes and components, the master admin functions, and the public CSS of a saved theme
  (`api/?r=theme.css&id=…`, proxied by savcomponents.com as `/theme/<id>.css`).
- `lib/` — bootstrap (sessions, CORS, rate limits), database schema (prefix `sc_`), emails (SMTP client shared with OpenNewsletter).
  `lib/sav.css` is a copy of the library stylesheet, used to build the theme CSS: `tools/release.sh` in the library repo refreshes it.
- `credentials.php` — git-ignored (template: `credentials-sample.php`); the tables are created on the first request. The site's shared MySQL database (the same one as Forkville and MyCurriculum, tables prefixed `sc_`), master admins,
  `$portal_url` and `$allowed_origins` (CORS).

The portal signs in with a session token sent in the `X-Sav-Token` header (cookies do not travel between
savcomponents.com and saveriomorelli.com). Only the origins in `$allowed_origins` can call the API from a browser.

Maintenance: `projects/cron/cleanup.php` removes expired sessions and links, old rate-limit attempts and accounts never
confirmed within 7 days; `projects/status/` checks the API, the database and the email server.
