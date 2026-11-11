# Sea Battle (Battaglia Navale)

The classic naval battle game developed by Saverio Morelli: hide your fleet, then take turns firing at the enemy grid until one fleet is at the bottom of the sea.

## Features

- **Online rooms with code**: play with friends by sharing a 6-character room code or a direct invite link (`?join=CODE`).
- **Random matchmaking**: find an opponent who speaks your language (5 minutes per player).
- **On the same device**: two captains pass the device between turns; a screen hides each fleet from the other player.
- **Against the computer**: three levels (easy, medium, hard), with a hint button.
- **Fleet**: 5 ships (5, 4, 3, 3, 2) on a 10 × 10 grid; ships never overlap or touch, not even at the corners.
- **Optional rule**: "hit, shoot again" (a hit gives another shot).
- **No cheating online**: fleets stay on the server; the opponent sees only the ships they sink, and the whole fleet once the game is over. Every shot is checked by `api/engine.php`, the PHP twin of `engine.js`.
- **Placement timer online**: 2 minutes to deploy, then the fleet is placed at random so nobody is kept waiting.
- **Clock, chat, rematch**; an opponent who stays disconnected for 2 minutes loses automatically, with a countdown on screen.
- **Auto, Light or Dark theme** (`theme.js`), **five languages** (en, it, es, fr, de), hull colour and options in Settings.
- **Works offline** for local games (`sw.js`), **zero third-party requests** (bundled Inter font, SIL OFL, in `fonts/`).
- **Ephemeral data**: inactive online rooms are purged nightly by `projects/cron/cleanup.php` (table prefix `sb_`).

## Files

- `index.php` – page (lobby, placement, battle, sheets)
- `engine.js` – rules shared by the page and the computer player
- `ai.js` – computer player (hunt/target, parity, probability density)
- `game.js` – controller for all modes
- `i18n.js`, `rules.js`, `releases.js` – texts
- `api/index.php` – online rooms; `api/engine.php` – server-side rules; `api/setup.php` – creates the tables

## Setup

1. Copy `api/db.example.php` to `api/db.php` (ignored by git) and fill in the database credentials. The games share one database.
2. Open `api/setup.php` once to create the `sb_rooms`, `sb_players` and `sb_messages` tables.

## Android app (Google Play)

The `android/` directory is an Android Studio project (Java) with a full-screen WebView shell around the web app (package `com.saverio.seabattle`):

- Edge-to-edge layout; the system bars follow the theme of the page (`AndroidTheme` bridge called by `theme.js`)
- App Links: invite links (`/projects/sea-battle/?join=CODE`) open straight in the app
- Back closes the open sheets of the game first (`window.onAndroidBack` in `game.js`)
- Offline error screen with a retry button; the service worker lets the local games open without a connection
- Adaptive launcher icon with the Android 13+ themed (monochrome) layer
- Play Store assets: `play-store-icon-512.png`, `store/en-GB/` (feature graphic and 1080×1920 screenshots), `store/listing.md` (texts in English and Italian, data safety answers)

### Building and publishing

1. Open `projects/sea-battle/android` in Android Studio (File → Open) and wait for the Gradle sync.
2. Copy `keystore.properties.sample` to `keystore.properties` (ignored by git) and fill in the data of your upload key.
3. Build → Generate Signed App Bundle / APK → Android App Bundle; upload `app/release/app-release.aab` to the Play Console.
4. After the first upload, copy the SHA-256 of the **app signing key** (Play Console → Test and release → App integrity) into `assetlinks.sample.json` and add that entry to `/.well-known/assetlinks.json` on the site, so the invite links open in the app.
5. Copy the store texts from `android/store/listing.md`.
