# Giftwise

Wishlists to share with friends, coordinating among each other **without spoiling the surprise**.

- Each user creates their **wishlists** (birthday, Christmas, wedding, baby shower, graduation, anniversary, housewarming,
  other), with an event date and a message for friends.
- In each list, they add **wishes**: name, product link, image, price, quantity, notes, and "most wanted".
  Pasting (or sharing from a shopping app) a link automatically extracts name, image, and price.
- Each user has a **public username** (3–20 characters: letters, numbers, `.` and `_`), suggested from their name upon
  registration and editable in their Profile. People can be **found and followed** by username (or exact email); you can
  unfollow or remove someone from your followers at any time.
- **View lists of people you follow.** By default, new followers must be approved (wishlists are personal); this can be
  changed in Profile to "Anyone, without asking".
- **Permanent invite links**: opening an invite link (even when registering on the spot) makes both users follow each other
  immediately.
- On lists of people you follow, you can mark a gift as **"Thinking about it"** or **"I'll get this"**, and **chat** with
  fellow followers per wish or for the entire list.
- **The list creator never sees any of this.** This rule is strictly enforced by the server: the API never returns
  reservations or messages to the list owner and rejects attempts to read or create them (see header comment in
  `api/index.php`).
- A list can be visible to all followers or only selected ones; it can be archived (hidden from friends) or deleted.

Web app in PHP + MySQL sharing the FoodTrack design system (raspberry pink, light and dark, mobile and desktop), in
Italian and English, installable as a Progressive Web App (PWA).

## Tutorial

Upon first login on a device (and whenever a new major version is launched, i.e. when the major number of `VERSION` in
`assets/app.js` changes), the app presents an illustrated onboarding tutorial: creating lists, adding wishes with link
previews, finding and following friends, secret gift reservations, and group chat. It can be revisited at any time via
Profile → Preferences → "App tutorial" (or "How it works" in the desktop sidebar). The language can be chosen directly in
the tutorial; illustrations are animated (disabled when reduced motion is preferred).

## Database Updates

On first access after an update, the database schema migrates automatically (`migrate_schema` in `lib/schema.php`). Version
2: public usernames for all existing accounts (generated from names, editable) and legacy friendships convert into mutual
follows.

## Structure

```
giftwise/
├── index.html, privacy.html, manifest.webmanifest, sw.js
├── install-db.php          guided installer (creates gw_ tables and credentials.php)
├── credentials-sample.php  credentials template (credentials.php is gitignored)
├── api/index.php           JSON API
├── lib/                    bootstrap (sessions, security, Google), schema, link preview, email, translations
├── assets/                 app.js, ui.js, api.js, i18n.js, app.css + giftwise.css, icons, fonts
├── install/giftwise.sql    matching tables for phpMyAdmin (regenerate with php tools/build-sql.php)
└── tools/build-sql.php
```

## Server Installation

1. Upload the `projects/giftwise/` folder (via FTP or site deployment).
2. Open `https://www.saveriomorelli.com/projects/giftwise/install-db.php`, enter MySQL database credentials (can share the
   same database as FoodTrack: tables use prefix `gw_`), and optionally the Google Client ID.
3. The page creates tables and writes `credentials.php`, then locks itself (re-executable only with the DB password). For
   security, delete it from the server afterwards.
4. Open the app and create the first account via "Create account".

Alternatively: import `install/giftwise.sql` in phpMyAdmin, copy `credentials-sample.php` to `credentials.php`, and fill in
details.

## Sign in with Google (Optional)

Can share the **same "Web application" OAuth client as FoodTrack**: the origin `https://www.saveriomorelli.com` is already
authorised. Copy the Client ID into `credentials.php` (`$google_client_id`) or into the installer field.

## Emails

Transactional emails: welcome, "X wants to follow you" (when approval is required), and "X opened your invite", in the
recipient's language. Uses `$mail_config` in `credentials.php` if present, falling back to website contact form settings
(`/.mail-config.php`). Without mail configuration, no emails are dispatched and the app works normally.

## Adding Wishes from Other Apps

- **Android (installed from browser)**: Giftwise appears in the system "Share" menu in Chrome and shopping apps (Web
  Share Target): the link is delivered to "Add a wish" pre-filled.
- **Everywhere else**: copy the product link and paste it into the "Product link" input field.

Link previews are resolved by the server (`lib/preview.php`): extracts Open Graph, Twitter cards, JSON-LD, and
microdata; includes SSRF protection against internal network requests (ports 80/443 only, public IPs only, connection
pinned to resolved IP, strict redirect validation, short timeouts, max 1.5 MB). If a merchant (e.g. Amazon) serves a CAPTCHA
verification page, the title is extracted from the URL and remaining details can be entered manually.

## Android App (Google Play)

The `android/` directory is an **Android Studio** project (Java) with a native app wrapping the web app in a full-screen
WebView, like FoodTrack: identical experience across devices and self-updating with web releases.

Native features (`MainActivity.java`, exposed to the page as `window.GiftwiseAndroid`, only on Giftwise pages):

- **"Continue with Google"** using the system account picker (Credential Manager), since Google login is blocked inside
  standard WebViews;
- **"Share" from any app** (shopping apps, Amazon, browser): Giftwise appears in Android's Share sheet as "Add to
  Giftwise" and opens "Add a wish" with the URL pre-populated;
- Back navigation button, invite links opening the app, external links (store pages, contact form) opening in the browser,
  offline screen, system light/dark theme matching, free rotation on tablets.

Steps:

1. Open `android/` in Android Studio (File → Open) and wait for Gradle sync.
2. **Package name**: `com.saverio.giftwise` (in `app/build.gradle.kts`): cannot be altered after first store publication.
3. Signing key: Build → Generate Signed App Bundle / APK → Android App Bundle → Create new… (store `.jks` outside repo), or
   copy `keystore.properties.sample` to `keystore.properties` (gitignored).
4. **Google Sign-In in app**: in Google Cloud Console, under the **same project** as the "Web application" client, create an
   **Android** OAuth client ID with package `com.saverio.giftwise` and the **SHA-1** fingerprint of every signing key (debug
   and Google Play App Signing). The code remains unchanged: the app uses the web Client ID returned by the server.
5. Upload `.aab` to Play Console. Store icon: `android/play-store-icon-512.png`; privacy policy:
   `https://www.saveriomorelli.com/projects/giftwise/privacy.html`. Copy, screenshots (1080×1920), and feature graphic
   (1024×500) are located in `android/store/` (`listing.md`).
6. **Digital Asset Links** (to open invite links in the app): add a second entry to `/.well-known/assetlinks.json` with
   `package_name` `com.saverio.giftwise` and the **SHA-256** fingerprints of its keys (see `android/assetlinks.sample.json`).
7. Bump `versionCode` and `versionName` on each Android app update.

## Security

Passwords hashed with `password_hash`, sessions tracked via random tokens in HttpOnly/Secure/SameSite cookies (only SHA-256
hash stored in DB), CSRF token on every state change, rate limiting (login, registration, invites, previews, chat messages),
`credentials.php`, `lib/`, `install/`, and `tools/` forbidden from direct browser access.
