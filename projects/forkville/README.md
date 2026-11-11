# Forkville

An original 3D restaurant game in the browser, in the spirit of the old social restaurant games: build and decorate
your restaurant, hire chefs and waiters, grow ingredients, learn and improve dishes, visit friends.

## Stack

- PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+ (PDO), tables prefixed `fv_`. Same structure as ThinkHop and Fucina.
- Three.js r170, vendored in `vendor/three.module.min.js` (no CDN). Every model is built in code (`assets/models.js`).
- No build step: ES modules in `assets/`.

## Install

1. Upload the folder, open `install-db.php`, fill in the database and the first administrator. It writes
   `credentials.php` (git-ignored, blocked by `.htaccess`). Delete `install-db.php` afterwards.
   Alternatively import `install/forkville.sql` and copy `credentials-sample.php` to `credentials.php`.
2. Email: confirmation links use `$mail_config` in `credentials.php`, or the site's `/.mail-config.php`.
   **The confirmation is always required**: without SMTP nobody can enter with email and password.
3. Google sign-in (optional): set `$google_client_id` (OAuth "Web application" client, add the site origin).

## Accounts

- Email and password: signing up never signs in. The app shows "check your inbox"; the link (`?verify=`) confirms
  the address and signs in. An unconfirmed account that signs in only sees the confirmation screen, and the game API
  answers `email_unverified`.
- Google: the account is confirmed by Google; new players choose a username and accept the privacy notice.
- The username is the only public data. Friends also see the restaurant (its name, layout, menu, wish).

## Game rules (server-authoritative)

All prices and rules are in `lib/catalog.php`; the app reads them from `game.catalog`. Every purchase goes through
a locked transaction (`mutate()` in `api/index.php`). Live service runs in the browser (`assets/sim.js`) and is
reported in batches (`game.report`), capped by elapsed time, seats and the arrival rate for the restaurant's
popularity. Earnings while away: at most 4 hours, at a quarter of the live speed.

## Admin panel

`admin.html` (also linked from Account in the game, for administrators). Administrators are accounts with
`role` ≥ 9: the first one is created by `install-db.php`, others can be promoted from the panel.

- **Giocatori**: search, account (confirm email, disable, make admin, delete), restaurant name, add or remove
  coins with a reason, experience and level, pantry quantities, recipe levels, reset the furniture.
- **Prezzi e regole**: prices, levels, popularity and availability of every item, dish, crop and market
  ingredient; hiring and enlargement costs; rules (starting coins, earnings while away, visit bonuses, market
  limits, XP). Only the differences from the defaults in `lib/catalog.php` are stored (`settings` table) and
  they apply at once. An item taken off sale stays with those who already own it.
- **Registro**: every change made from the panel (`admin_log`).

## Local testing

Never against the real database. Point `FORKVILLE_CREDENTIALS` at a credentials file for a throwaway database, with
`$mail_config = false` (no email at all, not even through the site settings) and `$debug = true` (the confirmation
link is written to the PHP error log):

```
FORKVILLE_CREDENTIALS=/path/to/test-credentials.php php -S localhost:8097 -t .
```

## Tools

- `php tools/build-sql.php` regenerates `install/forkville.sql` from `lib/schema.php`.
- `python3 tools/make_icons.py` regenerates the icons, the social image and `/images/projects/forkville.png`.
