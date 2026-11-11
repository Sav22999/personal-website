# Prism Mine

A deduction game developed by Saverio Morelli, inspired by light-and-mirror puzzles: send beams of light into a dark 8 × 8 mine, watch where they come out and in which colour, and work out where the gems are hidden.

Inspired by the classic deduction games with beams of light, such as *Black Box* (Eric Solomon, 1977) and *Orapa Mine* (Junghee Choi and Wanjin Gill, 2024). Prism Mine has its own rules, gems and graphics, and is not affiliated with those games or their publishers. Keep other games' names out of the app title, store listing and keywords.

## How it works

- 32 entrances around the mine. A beam travels straight, bounces straight back off a flat side and turns 90° off a slanted side.
- Ten gem shapes (shard, cube, wedge, slab, pyramid, diamond, rod, peak, corner, bridge) built from square and triangular cells; the rules show how each one bounces the beam.
- Colours mix: red, yellow and blue like paint, white makes the mix paler; a clear gem bounces the beam without colour, a black one swallows it.
- You know which gems are hidden. Any layout that gives the same answer at all 32 entrances is right, so the answer is never ambiguous.
- Every generated mine is fair: each gem changes at least one answer.

## Modes

- **Solo**: three levels (3, 4 or 5 gems; hard adds a clear or black gem). Score = beams + 3 per wrong answer + 3 per revealed gem; best score kept per level.
- **Mine of the day**: the same mine for everyone (seeded by the date), one counted try, a shareable result; an unfinished try survives a reload.
- **Example game**: a fixed mine explained in 9 steps (Back/Next, arrow keys), showing the real path of each beam; it ends by starting a real easy mine.
- **Online duel**: like Sea Battle, both players get the same gems and hide them in their own mine (2 minutes, then at random); then they take turns on the other mine: one beam or one answer. Each player can switch between the other mine (own beams and sketch) and the own mine (the opponent's beams). The first right answer wins; three wrong answers lose. Room codes, invite links (`?join=CODE`), random opponents, clock, chat, rematch. An opponent who stays disconnected for 2 minutes loses automatically, with a countdown on screen.

## Helpers

- **Sketch**: place the known gems on the board as you reason.
- **Check my sketch**: marks which beams the sketch already explains.
- **Beam paths**: point at an entrance to see the path a beam would take through the sketch.

## Files

- `engine.js` – rules (beam tracing, colours, mine generation, answer check), mirrored by `api/engine.php`
- `game.js` – controller for all modes; `i18n.js`, `rules.js`, `releases.js` – texts in en, it, es, fr, de
- `api/index.php` – online duels; `api/setup.php` – creates the tables (prefix `pm_`)
- Bundled fonts (Fredoka, Nunito, SIL OFL) in `fonts/`; works offline for solo games (`sw.js`)

## Setup

1. Copy `api/db.example.php` to `api/db.php` (ignored by git) and fill in the database credentials. The games share one database.
2. Open `api/setup.php` once to create the `pm_rooms`, `pm_players` and `pm_messages` tables.
3. Inactive rooms are purged nightly by `projects/cron/cleanup.php`.

## Android app (Google Play)

The `android/` directory is an Android Studio project (Java) with a full-screen WebView shell around the web app (package `com.saverio.prismmine`):

- Edge-to-edge layout; the system bars follow the theme of the page (`AndroidTheme` bridge called by `theme.js`)
- App Links: invite links (`/projects/prism-mine/?join=CODE`) open straight in the app
- Back closes the open sheets of the game first (`window.onAndroidBack` in `game.js`)
- Offline error screen with a retry button; the service worker lets the local games open without a connection
- Adaptive launcher icon with the Android 13+ themed (monochrome) layer
- Play Store assets: `play-store-icon-512.png`, `store/en-GB/` (feature graphic and 1080×1920 screenshots), `store/listing.md` (texts in English and Italian, data safety answers)

### Building and publishing

1. Open `projects/prism-mine/android` in Android Studio (File → Open) and wait for the Gradle sync.
2. Copy `keystore.properties.sample` to `keystore.properties` (ignored by git) and fill in the data of your upload key.
3. Build → Generate Signed App Bundle / APK → Android App Bundle; upload `app/release/app-release.aab` to the Play Console.
4. After the first upload, copy the SHA-256 of the **app signing key** (Play Console → Test and release → App integrity) into `assetlinks.sample.json` and add that entry to `/.well-known/assetlinks.json` on the site, so the invite links open in the app.
5. Copy the store texts from `android/store/listing.md`.
