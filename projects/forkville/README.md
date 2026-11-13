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
popularity. Earnings while away: managers first (the best one's hours in full, a quarter of the others', at most
`MANAGER_MAX_HOURS` = 8, at 40% of the live speed), then at most 3 hours at a fifth of the live speed; guests served
while away give half the XP (`OFFLINE_XP`).

## Game systems

- **Calendar**: each restaurant has its own (year 0 = opening, months of 10 days, time passes only while playing,
  speed 1×, 2×, 5×); seasons, day/night and weather follow it. Saved on the server (`game_minutes`).
- **Staff**: hire candidates with a CV (skills 0–10 per role), assign and change roles, traits, experience,
  uniforms, hairstyles and accessories, managers (from level 7) who run the restaurant while you are away, daily wages
  (not paid while closed).
- **Stations**: every worker has one of their own — chef → a stove, waiter → their tables, cleaner → a cleaning cart or
  station (restocks there), manager → a host stand, cashier → a till. Without one they stand idle under a thought
  bubble (the missing piece with a question mark) and the status lists the warning. Cleaners do restrooms and floor
  first, then help clear any dirty table, then water the plants.
- **Requirements**: a to-do list (status bar chip "To do: N" and the status menu) with what guests strictly need to
  open (table and chair, stove, counter, chef, waiter, a dish), what keeps them happy (WC, washbasin, sink, cleaner with
  a station, trash bin, lamps) and a station for every worker hired; each line jumps to where it is fixed.
- **Manager**: welcomes guests at the host stand; takes an order only when a guest has waited over 14 s unclaimed.
- **Personal space**: every worker waits on a tile of their own (station fronts and counter pick-up spots are kept
  free), and people standing still step apart instead of overlapping.
- **Underground car park** (floor −1, `garage`, `GARAGE` in catalog.php, `game.garage`): from level 18, five levels
  from 16 to 80 spaces, beside the street car park. Cars drive down a ramp (a hole is cut in the grass) and the guest
  comes up a glass stairs kiosk. Server caps on car guests use `parking_spaces()` (street + underground).
- **Street life**: passers-by and cars never stop; when the restaurant is closed (or not taking guests) the people who
  would have come in just walk past on the near sidewalk.
- **Junctions**: where the side streets cross the main road, zebra crossings on every arm and traffic lights (passing
  cars stop at red); where they cross the second road, roundabouts; where the back street meets them, zebra crossings.
- **Neighbourhood** (`assets/town.js`): a whole quarter from level 1. A street on each side of the plot, a back street,
  a second road across the main one; houses, little shops (bakery, florist, café, grocer, bookshop, pharmacy, ice
  cream, with pictogram signs) and, as the level grows, blocks of flats; a square with a fountain across from the door
  (only low buildings there, so nothing hides the restaurant). Each building's pieces are merged by material.
- **Holidays** (game calendar, `holidayAt()`): Christmas, Halloween, Easter, Carnival, Valentine's, summer festival.
  Lights or bunting on every house and shop, wreaths / pumpkins / eggs / hearts at the doors, decorated trees, ribbons
  on the lamps, garlands across the main road and a centrepiece in the square (a big Christmas tree, a pile of
  pumpkins…). `World.setHoliday()` applies them; a toast announces the start.
- **Storage** ("Archivio", `storage` column, schema 15): in build mode "Put away" removes a piece without selling it;
  the Storage tab places it again for free, or sells it (`game.storage_sell`). `game.layout` takes `store` (ids put
  away) and `take` ({key: n} placed from storage).
- **Dirt**: litter grows with the guests in the room (each guest adds crumbs and spills over time; trash bins slow it).
  Guests count as "inside" from the door bell until they walk out of the door again.
- **Restaurant**: kinds (generic, fast food, café, inn, luxury), open / no new guests (queue outside) / closed,
  outdoor terrace and yard, equipment tiers, dirt and cleaning, reviews (reputation = last 50), levels with rewards.
- **Room size**: width (`size`) and depth (`depth`) grow one column or row at a time (`game.expand`), up to
  `max_side(level)`; a new column goes on the left when the door (in the middle) would move, so nothing shifts.
- **Upper floors** (`floors`, from level 15, up to 4): each starts 6×6 and grows inside the floor below; stairs lead
  one floor up, lifts reach every floor they fit on. The simulation keeps floors `FLOOR_STRIDE` apart along z
  (one flat grid, with stair/lift links); the scene stacks them and shows one floor at a time.
- **Car park** (`parking`, 3 levels): guests who come by car pay +80% and tip more (server-capped in `apply_report`).
- **Neighbourhood**: houses multiply and grow taller with the level; cars and passers-by follow the hour (`assets/town.js`).
- **Door**: movable along the front wall (`game.door`, the tile inside and the path outside must be free) and 12 styles;
  the `wide_*` ones (`DOOR_WIDE`) are two tiles wide (both tiles and both paths kept free).
- **Walls**: adaptive (Sims style), always low or always high, chosen from a small panel by the camera buttons
  (stored on the device).
- **Windows** (category `window`, own layer like rugs): pieces placed along the inner edge of an outer wall, the
  rotation says which wall (`window_tile()`); 1 to 4 tiles wide, plain, round, tall, arched, stained, full-height
  glass walls and panoramic glazing. The scene cuts them into the walls (`windowUnits()` / `windowPanes()` in
  scene.js); in build mode a marker on the floor picks them. Rooms from before get their old windows as pieces
  (`ensure_windows`, marker `win1` in finishes), a new storey gets the usual ones, and windows move with their wall
  when the room grows (`shift_windows`).
- **Outdoor area**: `OUTDOOR` = 5 tiles in front, at the east side and behind. **Paving** (category `ground`, own
  layer): grass, gravel, stone, decking, brick, terracotta, flowers, mosaic, marble; pieces stand on it, people walk on
  it, also on the path to the door.
- **Build arrows**: in build mode a small yellow arrow on the floor shows the side a piece is used from (chairs, stoves,
  counters, lounge, restroom, stairs…), on the placed pieces and on the one being placed.
- **Stairs**: the tile just past their top end, on the floor above, must be inside that floor and stay free
  (`stairs_exit`), or nobody could step off.
- **Dish lift** (`dish_lift`, from level 14): a counter that sends a dish to the lift on the guest's floor (3 s ride),
  so a chef can cook upstairs and the waiters pick the dish up downstairs (`pickSlot` / `sendByLift` in sim.js).
- **Cooking time**: every dish has its own base time (`DISHES`, from 2 s for a drink to 26 s for ribs), shortened by
  the stove's speed, the fridges, the kitchen equipment, a better counter, the chef's skill and trait
  (`cookTime()` in sim.js); the menu shows the base and the time in this kitchen.
- **Levels**: up to 40 (`xp_for_level` = 60·L·(L−1)·(1 + L/15)); a level never goes down when the curve changes.
- **Wages**: (role base + 7 per skill point [+ 25 per manager rank]) × (1 + 0.09 per restaurant level above 1).
- **Chefs**: limited by the level (`max_chefs`: 1, then +1 every 4 levels, up to 8) and one stove each.
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
