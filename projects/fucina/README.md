# Fucina

Gamified web app for learning to program, across progressively challenging levels: **43 courses and 234 levels**. Web (HTML, CSS, animations, SCSS, Sass, Tailwind CSS, JavaScript, TypeScript, Angular, Vue), backend & data (PHP, Python, Java, SQL, R), mobile & desktop (Flutter, Swift, Kotlin, Vala), and systems (Rust). Each course belongs to one or more categories (frontend, backend, mobile, data, systems, desktop): in the Courses section they can be searched by name and filtered by category chips, grouped by language. The original “Advanced PHP” course is now titled “Intermediate PHP” (migrated by schema version 12 with all learner progress and certificates intact); the newer “Advanced PHP” comes afterwards.

Web application in PHP + MySQL, bilingual in Italian and English, installable as a Progressive Web App (PWA) and ready for Google Play (`android/`).

## How It Works

- **Courses & Levels**: each course is a sequence of levels (theory, questions, code exercise) of increasing difficulty. A level unlocks upon completing the previous one, and a course unlocks by finishing the one before it or by passing its final exam.
- **Drills** (`lib/drills.php`, `assets/drills.js`): between theory and questions, six types of bite-sized drills appear: code tiles, line order, bug hunt, match pairs, timed true-or-false flashcards, and CSS copy shapes. These drills are also mixed together in the **Gym**. As with games, drills run client-side in the browser, are not part of final exams, and award rewards once.
- **Exercises**: learners write real code with a live preview for HTML and CSS. Validation executes in the browser within a script-less sandboxed iframe. SCSS, Sass, JavaScript, TypeScript, and PHP do not show live rendering: automated tests inspect the written code structure.
- **XP & Sparks**: each reward awards both XP and an equal number of Sparks. XP increases monotonically and determines rank: 14 ranks, from Spark (rank 1) to Eternal Flame (rank 40), defined in `RANKS` in `lib/progress.php` and listed in learner statistics. Sparks are spent on **hints**: on a question they eliminate half of the wrong options, on an exercise they show a working solution, and in games they provide assistance. Hints are unavailable during final exams.
- **Exam & Certificate**: an exam with questions drawn at random and a time limit (default 15 minutes, configurable per course and verified server-side), available for every course even without completing all intermediate levels. Those who already know the subject can pass the exam directly: subsequent courses unlock, unplayed levels are marked as completed with fires instead of stars (3 fires from 90%, 2 from 80%, 1 otherwise), without XP and without counting towards milestone badges. Once passed, the certificate can be **unlocked with Sparks** (price configured in Settings, default 200) and can be viewed or printed in A4 (`certificate.php?c=CODE`). The certificate is a server-rendered image, always in English, exportable as PNG or PDF (`lib/certificate.php`; certificate background can be rebuilt via `tools/build-certificate.py`). Certificates clearly state they carry no formal legal accreditation. Anyone can verify an earned certificate by its unique alphanumeric code at `verify.php` without an account. If score is 100%, the title displays “Certificate of completion with honours”; otherwise “Certificate of completion”. The score is displayed in points out of 10.
- **Games**: nine level-based mini-games focused on code: *Who is the tag?*, *Tagdoku* (4×4, 9×9, 16×16), *Four in a row* (Connect Four mechanics with questions: correct answers drop a green disc, incorrect answers drop a red disc), *CSS Chessboard* (from 3×3 up to 10×10), *Line hop* (drag-and-drop lines), *Right shade* (CSS colours), *Selector duel* (specificity), *Flex lab*, and *What does it print?* (JavaScript output). Games award bonus Sparks up to a daily cap.
- **Profile Picture**: custom characters, accessories, frames, and backgrounds rendered in SVG (`assets/avatars.js`), unlocked with Sparks from the learner profile and displayed in rankings and among friends. Prices, availability, and sales of each item are managed from the admin portal (*Profile pictures*, table `fc_avatar_items`); purchases are recorded in `fc_user_items` and user activity logs. The learner's initial character and empty slots are always free. Items are sorted in ascending order by price.
- **Badges**: every unlocked badge awards XP and Sparks (default 50, `xp_badge` in Settings).
- **Email Verification**: learners registering with email and password receive a single-use verification link (valid for 48 hours, stored only as a SHA-256 hash in `fc_email_tokens`); until confirmed, the account is restricted server-side (`must_verify()`), allowing only resending the link or account deletion. The link opens the app with a confirmation button to prevent automated email security scanners from consuming the token. Google accounts and administrators are verified automatically. If SMTP credentials are not configured, verification is disabled gracefully to avoid lockouts. Registered email addresses cannot be modified.
- **Admin Portal, Users**: user XP and Sparks are editable (every adjustment is recorded in activity history), read-only activity logs display timestamps and actions, and pending unverified accounts are flagged. Certificate names can be updated by administrators.
- **Username & Friends**: each learner selects a unique username, which is the only publicly visible detail. Used to search, add, and remove friends (unidirectional follow, no pending request queue) and in leaderboards (global or friends-only).
- **Daily Leaderboards**: the daily challenge leaderboard (highest score first; on ties, fastest completion verified by server timestamps) distributes prizes: 1st place earns Sparks based on participant count, 2nd earns half, 3rd earns one quarter (requiring at least one correct answer). No background cron is needed: the first request of the subsequent day settles rewards automatically. The XP leaderboard is purely recreational. Learners can opt out of leaderboards from their profile preferences.
- **Notifications**: daily challenge, streak at risk, prizes, and friends, each configurable from the profile (`lib/push.php`). Web Push carries no payload: it simply wakes the service worker, which fetches pending notifications from the API. Scheduled reminders (daily challenge and streak at risk) are delivered by `projects/cron/fucina-reminders.php` according to each user's chosen reminder hour in server timezone (Europe/Rome).
- **Daily Challenge**, day streaks, learning analytics, activity heatmaps, and 51 collectible badges (including one for every course).

Rules and validation remain strictly on the server: question answers reach the browser only after submission (`quiz.check`), exams are graded server-side, and game XP is subject to per-game and daily limits.

## Admin Portal (`admin.html`)

Restricted to administrators (role 5) and master administrators (role 10), available in Italian and English. Manages courses, levels (theory, exercises, validation checks), quizzes, drills (content authored in JSON with reference templates), games and items, users, certificates, avatar items, and system settings (XP rates, hint pricing, exam timers). All content supports side-by-side editing across languages; missing translations are highlighted in red and tallied in “To translate”.

To add a new language: provide a locale dictionary in `assets/i18n.js` and register its language code in `LANGS` within `lib/i18n.php`.

## Project Structure

```
fucina/
├── index.html, admin.html, privacy.html, manifest.webmanifest, sw.js
├── certificate.php         public certificate verification and display
├── reminders.php           legacy wrapper (points to projects/cron/fucina-reminders.php)
├── install-db.php          installation wizard (tables, initial seed, master admin)
├── update.php              database schema migration using saved credentials (CLI or signed-in admin only)
├── credentials-sample.php  configuration template (credentials.php is gitignored)
├── api/index.php           JSON API endpoints
├── lib/                    bootstrap, schema, courses, progress, push, admin API, email
│   ├── seed.php, seed/     initial content catalogue: loaded once on install/upgrade
├── assets/                 app.js, games.js, admin.js, ui.js, api.js, avatars.js, i18n.js, css, icons
├── install/fucina.sql      complete SQL schema and seed data for phpMyAdmin (tools/build-sql.php)
├── tools/                  build-sql.php, build-icons.py, check-drills.php
└── android/                native Android Studio WebView wrapper
```

## Server Installation

The database is typically the 5th database on shared hosting (`…_5`), using the `fc_` table prefix.

1. Upload `projects/fucina/` (the `android/` directory is not needed on the server), without `credentials.php`.
2. Navigate to `https://www.saveriomorelli.com/projects/fucina/install-db.php` and enter the database host, user, password, database name (`…_5`), followed by the master admin name, email, and password.
3. The wizard provisions all database tables, seed content, master admin account, and writes `credentials.php`.
4. Delete `install-db.php` from the server once complete.

Alternative installation via phpMyAdmin: import `install/fucina.sql`, register an account in the app, and elevate it to master admin:

```sql
UPDATE fc_users SET role = 10 WHERE email = 'your@email.com';
```

## Google Sign-In

Shares the same OAuth 2.0 Web Client as FoodTrack and GiftWise. The Client ID is stored in `credentials.php`.

## Android Application (Google Play)

`android/` is a native WebView container (`com.saverio.fucina`): Google sign-in via native credential picker (`window.FucinaAndroid`), deep links for certificates opening directly in the app, system back button navigation, and an offline fallback page.

1. Open `android/` in Android Studio and let Gradle sync.
2. Generate an upload keystore or copy `keystore.properties.sample` to `keystore.properties`.
3. In Google Cloud Console, create an **Android** OAuth Client ID with package name `com.saverio.fucina` and your signing certificate SHA-1 fingerprint.
4. Upload the built `.aab` bundle to Google Play Console. App icon: `android/play-store-icon-512.png`, store text: `android/store/listing.md`.
5. For deep link handling, add the Digital Asset Links definition to `/.well-known/assetlinks.json` as shown in `android/assetlinks.sample.json`.

## Security

Passwords hashed with `password_hash` (bcrypt/argon2), session authentication via cryptographically random tokens stored in HttpOnly/Secure/SameSite cookies (database holds only the SHA-256 hash), CSRF token validation on all mutating POST endpoints, rate limiting on sensitive actions, and directory access to `credentials.php`, `lib/`, `install/`, and `tools/` denied by server configuration. User-submitted exercise code executes in an isolated sandbox iframe without script execution privileges.

## Upgrading the Catalogue

Initial seed content resides in `lib/seed/`. To apply updates to an existing installation, update the seed files and increment `SCHEMA_VERSION` in `lib/bootstrap.php`: upon the next request, `ensure_schema()` inserts only missing courses and levels, preserving any edits made via the admin portal. Verify catalogue integrity beforehand via `php tools/check-drills.php`, then rebuild `install/fucina.sql` via `php tools/build-sql.php`.
