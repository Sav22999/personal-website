# Daily Cleanup for Online Games and Apps

`cleanup.php` performs two tasks:

- **Games:** Deletes rooms for Connect Four, Chess, FaceGuess, Sea Battle and Prism Mine that have had no changes and no connected players for 24 hours, along with players, chat messages, and cards. Run once a day, data remains for at most ~48 hours since last activity: this matches the retention stated in privacy policies and store listings.
- **FoodTrack, Giftwise, Fucina, ThinkHop, and Fischietto:** Cleans up expired sessions and login attempts older than 2 hours (IP addresses are only used for rate limiting over the past 60 minutes) and strips IP/browser data left on sessions from earlier versions. It never alters lists, items, wishes, stats, progress, or other user data.

## ThinkHop Reminders

`thinkhop-reminders.php` sends non-live reminders (daily challenge or streak at risk) to players who have not played today, respecting the custom reminder hour chosen by each player (default 18:00 in server timezone `Europe/Rome`).
It can be scheduled **hourly** (e.g. `0 * * * *`) or once a day at 18:00. At most one reminder per player per day.
It also deletes unread notifications older than 3 days. Passing `?force=1` (or `--force` via CLI) immediately sends reminders to all pending players.

- **URL:** `https://www.saveriomorelli.com/projects/cron/thinkhop-reminders.php?key=YOUR_KEY` (same key)
- **Command line:** `php /path/to/site/projects/cron/thinkhop-reminders.php` (no key required)

## Installation

1. Upload the entire `projects/cron/` directory to the server, including `credentials.php` (excluded from git). If missing, copy `credentials-sample.php` to `credentials.php` and set a long random key.
2. Schedule the cleanup job every day at 00:00 using either:
    - **URL:** `https://www.saveriomorelli.com/projects/cron/cleanup.php?key=YOUR_KEY`
    - **Command line:** `php /path/to/site/projects/cron/cleanup.php` (no key required)

## Responses

- `200` with cleanup summary, for example:
  `{"ok": true, "deleted_rooms": {"connect-four": 3, "chess": 0, "faceguess": 1, "sea-battle": 0, "prism-mine": 0}, "deleted_expired": {"foodtrack": {"sessions_anonymised": 0, "sessions": 2, "attempts": 14}, "giftwise": {"sessions_anonymised": 0, "sessions": 0, "attempts": 5}, "fucina": {"sessions_anonymised": 0, "sessions": 1, "attempts": 2}, "thinkhop": {"sessions_anonymised": 0, "sessions": 0, "attempts": 1}, "fischietto": {"sessions_anonymised": 0, "sessions": 1, "attempts": 3}}}`
- `403` Invalid or missing key
- `503` Missing `credentials.php` or key is too short
- `500` Cleanup of at least one game or app failed (code in response, details in PHP error log) or database did not respond. `SQLSTATE 42S02` indicates missing tables: run that game's `api/setup.php`.

## Fucina Reminders

`fucina-reminders.php` sends non-live reminders (streak at risk first, otherwise daily challenge) to learners who have not practiced today, respecting the custom reminder hour chosen by each learner (default 18:00 in server timezone `Europe/Rome`).
It can be scheduled **hourly** (e.g. `0 * * * *`) or once a day at 18:00. At most one reminder per learner per day. Passing `?force=1` (or `--force` via CLI) immediately sends reminders regardless of the hour.

- **URL:** `https://www.saveriomorelli.com/projects/cron/fucina-reminders.php?key=YOUR_KEY` (same key)
- **Command line:** `php /path/to/site/projects/cron/fucina-reminders.php` (no key required)
