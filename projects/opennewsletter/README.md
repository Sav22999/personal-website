# OpenNewsletter

Newsletters written, approved and sent by a team. People create **groups**; each group has its own members (with
roles), its own subscribers (in one or more languages), its own outgoing address (SMTP) and its own subscription page
and widget. Moderators write with a drag-and-drop editor and propose a date and time; publishers approve; the cron
script sends at the chosen 30-minute slot. Listed among the personal projects (projects/index.php, about page in
projects/about/opennewsletter/, sitemap.xml, llms.txt) and on the status page (projects/status/services.php).

## Stack

- PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+ (PDO), tables prefixed `on_`. Account code from MyCurriculum/Forkville.
- No build step: ES modules in `assets/` (`app.js` the app, `editor.js` the editor, `ui.js` helpers, `i18n.js` EN/IT).
  **After changing them, bump the version everywhere**: `?v=N` of `app.js`/`app.css` in `index.php` and the same `?v=N` in
  every `import … from './x.js?v=N'` (all modules must use the same tag, or the browser loads two copies of `ui.js`).
- Fonts in `fonts/` (Inter, Poppins; Stack Sans Notch only for the "Saverio Morelli" credit).
- No libraries: the SMTP client is in `lib/mail.php` (same as the other apps, plus extra headers).

## Install

1. Upload the folder, open `install-db.php`: database, master admin emails, optional Google client ID. It writes
   `credentials.php` (git-ignored, blocked by `.htaccess`) with a new random `$secret_key`. Delete `install-db.php`
   afterwards. Alternatively import `install/opennewsletter.sql` and copy `credentials-sample.php` to `credentials.php`.
2. **Never change `$secret_key`** once groups exist: it encrypts their SMTP passwords (AES-256-GCM, `seal()`/`unseal()`).
3. Platform email (account confirmation, invitations, approval requests): `$mail_config`, or the site's `/.mail-config.php`.
4. Schedule the cron scripts every 30 minutes (see `projects/cron/README.md`):
   `opennewsletter-schedule.php` (sending, expiry) and `opennewsletter-reminders.php` (reminders to publishers).
   `projects/cron/cleanup.php` also cleans this app daily (sessions, attempts, unconfirmed subscriptions after 7 days).

## Accounts and roles

- Email + password (the address must be confirmed via `?verify=`) or Google (no check). The email is unique and never changes.
- Roles in a group, each including the ones before: `user` (sees newsletters and numbers), `moderator` (writes newsletters
  and templates, proposes a slot, sends tests to itself), `publisher` (approves/rejects/reschedules/cancels, sees
  subscribers, may schedule directly without approval), `admin` (outgoing address, subscription page, terms, languages,
  members up to publisher, erases/exports subscribers), `owner` (who created it: names admins, hands the group over, deletes it).
- Members are added by email: an existing confirmed account joins at once, anyone else gets an invitation applied when
  they confirm an account with that address (`on_invites`, `accept_invites()`).
- **Master admins** (`$master_emails` in `credentials.php`) see every group (members, newsletters, subscribers, reasons
  for leaving) and can suspend it. They never see the SMTP settings: only the sender address, read-only.

## Newsletters

- Document = JSON `{theme, blocks}`; block types: heading, text (rich: b/i/u/s/a/ul/ol), image, button, card, quote,
  divider, spacer, social. `lib/render.php` cleans it field by field (`clean_doc`) and renders the table-based email
  HTML + plain text (`render_email`). The editor's "Preview" and "Send me a test" use the server renderer.
- Every email ends with the footer: why you receive it, unsubscribe link, "Newsletter created with [logo] OpenNewsletter"
  (in the subscriber's language), plus `List-Unsubscribe` / `List-Unsubscribe-Post` headers (one click in Gmail etc.).
- Workflow: `draft → pending (proposed slot) → scheduled → sending → sent`; `pending → rejected` (note to the author);
  `pending → expired` when the slot passes unapproved; `pending/scheduled → draft` (withdraw) or `cancelled`.
  Content is editable only in draft/rejected/expired. Saves carry the revision (`rev`): a stale editor gets `nl_conflict`.
- Slots are every 30 minutes in the group's timezone, stored in UTC (`parse_slot`), at most ~13 months ahead.
- Audience: all subscribers, or only those of one language. Recipients are fixed when sending starts (`on_deliveries`).
- Sending is throttled so providers do not block the account: each group has an hourly limit (`groups.rate_per_hour`,
  schema v2), each cron run sends at most 400 messages / 4 minutes, 0.15 s apart. 4xx replies and connection errors
  leave the message queued (retried later, at the end of the queue); 5xx replies fail that address only.
- Actions: drafts (and rejected, expired, cancelled) can be deleted, from the list or the editor; scheduled ones can be
  rescheduled, put back to draft or cancelled; proposals can change time; sent ones have no actions.
- Built-in templates in `lib/templates.php`; groups save their own (`on_templates`, "Save as template" in the editor).
- Images are resized in the browser and stored in `on_images`, served publicly by `api/?r=img&id=…` (they appear in emails).

## Subscribers

- `subscribe.php?g=SLUG[&lang=xx]`: email, language (if the group has several), OpenNewsletter terms + group terms
  (own checkbox, revision stored per subscriber), honeypot. Records the browser's language and the browser's **name**
  only (`browser_name()`), never the whole User-Agent. Double opt-in: the link (`&confirm=`) asks for a button press,
  so mail scanners do nothing; it works 7 days.
- `unsubscribe.php?t=TOKEN` from any newsletter (optional reason + note), or `unsubscribe.php?g=SLUG` typing the email
  (a confirmation link is mailed: nobody can unsubscribe someone else). One-click POST supported.
- Subscriber-facing texts in six languages: `lib/public-i18n.php` (others fall back to English).
- Page customisation by admins (title, intro, button, success message, accent, logo, theme, button shape, language
  picker, subscriber count). The same page in compact form is the widget: `widget.js` puts it in an iframe
  (`subscribe.php?embed=1`, the only page allowed in frames) and sizes it via `postMessage`.

## Pitfall: ad blockers

Ad blockers (AdGuard, uBlock with "social widgets" lists) hide elements whose CSS class contains `social`. The social
links block therefore uses `net-*` classes in the editor (`net-list`, `net-row`…) and `blk-netlinks` / `tb-netlinks` for
the block itself: never name a class `social…` in the app.

## Security notes

- Sessions: HttpOnly cookie, SHA-256 of the token in DB, CSRF header on every POST. Rate limits in `on_attempts`.
- Group SMTP hosts must resolve to public addresses (`smtp_host_allowed`, no internal network scans); in `$debug`
  127.0.0.1 is allowed for local tests with a fake SMTP.
- Previews are shown in sandboxed iframes without scripts.

## Local test

Use a throwaway database only: the `opennewsletter-test` launch configuration sets `OPENNEWSLETTER_CREDENTIALS` to a
credentials file outside the project (MariaDB on 127.0.0.1:3399, fake SMTP sink on 127.0.0.1:2525, `$debug = true`).

## Tools

- `python3 tools/make_icons.py` — PNG icons from `icons/favicon.svg` (Pillow + Inkscape). `icon-64.png` is the logo in
  the footer of every email.
- `php tools/build-sql.php` — regenerates `install/opennewsletter.sql` from `lib/schema.php`.
