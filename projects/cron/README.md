# Daily Cleanup for Online Games and Apps

`cleanup.php` performs two tasks:

- **Games:** Deletes rooms for Connect Four, Chess, FaceGuess, Sea Battle and Prism Mine that have had no changes and no connected players for 24 hours, along with players, chat messages, and cards. Run once a day, data remains for at most ~48 hours since last activity: this matches the retention stated in privacy policies and store listings.
- **FoodTrack, Giftwise, Fucina, ThinkHop, Forkville, MyCurriculum, OpenNewsletter, SavComponents and Fischietto:** Cleans up expired sessions and login attempts older than 2 hours (IP addresses are only used for rate limiting over the past 60 minutes) and strips IP/browser data left on sessions from earlier versions. It never alters lists, items, wishes, stats, progress, or other user data.
- **OpenNewsletter only:** also deletes subscriptions never confirmed within 7 days (as its terms promise) and expired account-confirmation links.
- **SavComponents only:** also deletes expired email links (confirm / new password) and accounts never confirmed within 7 days.

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

## OpenNewsletter

Both scripts read `projects/opennewsletter/credentials.php`. Schedule them **every 30 minutes** (e.g. `0,30 * * * *`):
newsletters leave on 30-minute slots chosen in each group's timezone.

### opennewsletter-schedule.php

1. Proposals whose slot has passed without a publisher's approval become `expired` and go back to their author (email).
2. Approved newsletters whose slot has come start sending: recipients are fixed at that moment (active subscribers, of
   the newsletter's language if it has one).
3. The queue is sent from each group's own SMTP, at most 400 messages / 4 minutes per run and never more than the
   group's **emails per hour** (Settings → Outgoing address, default 200): larger lists continue at the next runs.
   Messages leave 0.15 s apart. A temporary refusal (4xx, e.g. "too many messages", or a connection problem) stops that
   newsletter for this run and the address is retried later, at the end of the queue; only permanent refusals (5xx)
   count as failed. After 3 days whatever is still queued is given up. When a newsletter is finished its author gets an
   email with the numbers. A lock file prevents two runs at once.

- **URL:** `https://www.saveriomorelli.com/projects/cron/opennewsletter-schedule.php?key=YOUR_KEY` (same key)
- **Command line:** `php /path/to/site/projects/cron/opennewsletter-schedule.php` (no key required)
- **Response:** `{"ok": true, "expired": 0, "started": 1, "messages": {"sent": 120, "failed": 0, "deferred": 0, "finished": 1}, "utc": "2026-11-16 18:30"}`

### opennewsletter-reminders.php

When a newsletter waiting for approval is less than 24 hours from its proposed slot, its group's publishers, admins
and owner get one reminder email (at most one per proposal).

- **URL:** `https://www.saveriomorelli.com/projects/cron/opennewsletter-reminders.php?key=YOUR_KEY` (same key)
- **Command line:** `php /path/to/site/projects/cron/opennewsletter-reminders.php` (no key required)
- **Response:** `{"ok": true, "reminded": 1}`
