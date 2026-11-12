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

## Game systems

- **Calendar**: each restaurant has its own (year 0 = opening, months of 10 days, time passes only while playing,
  speed 1×–3×); seasons, day/night and weather follow it. Saved on the server (`game_minutes`).
- **Staff**: hire candidates with a CV (skills 0–10 per role), assign and change roles, traits, experience,
  uniforms, hairstyles and accessories, managers (from level 6) who run the restaurant while you are away, daily wages
  (not paid while closed).
- **Restaurant**: kinds (generic, fast food, café, inn, luxury), open / no new guests (queue outside) / closed,
  outdoor terrace and yard, equipment tiers, dirt and cleaning, reviews (reputation = last 50), levels with rewards.
- **Room size**: width (`size`) and depth (`depth`) grow one column or row at a time (`game.expand`), up to
  `max_side(level)`; a new column goes on the left when the door (in the middle) would move, so nothing shifts.
- **Upper floors** (`floors`, from level 15, up to 4): each starts 6×6 and grows inside the floor below; stairs lead
  one floor up, lifts reach every floor they fit on. The simulation keeps floors `FLOOR_STRIDE` apart along z
  (one flat grid, with stair/lift links); the scene stacks them and shows one floor at a time.
- **Car park** (`parking`, 3 levels): guests who come by car pay +80% and tip more (server-capped in `apply_report`).
- **Neighbourhood**: houses multiply and grow taller with the level; cars and passers-by follow the hour (`assets/town.js`).
- **Door**: movable along the front wall (`game.door`, the tile inside and the path outside must be free) and 8 styles.
- **Walls**: adaptive (Sims style), always low or always high, chosen with a camera button (stored on the device).
- **Seasonal pieces**: on sale only during their event on the real calendar (Easter computed each year), kept once bought.
- **Cold service**: with a drinks fridge or a gelato freezer the waiter serves cold drinks and ice creams himself
  (`DISH_KINDS`), no chef needed; without them the chef prepares them.
- **Cashier** (from level 9, needs a cash register): guests pay at the till, bills +10% and a tip of at least 8%
  (server: `CASHIER_BONUS` in `apply_report`).
- **Tips**: customers tip on good service; players can tip a friend while visiting (`social.tip`, daily cap).
- **One copy at a time**: each open copy sends an `X-Instance` id; opening the game elsewhere pauses the older copy.
- **Statement**: every coin movement is in `ledger` (30 days), shown by tapping the coins.
- **Music and sounds**: synthesized live with Web Audio (`assets/audio.js`), no audio files.

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
