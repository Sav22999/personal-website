# Work Timesheet (Rendicontazione Ore)

Private work hour timesheet system, designed to replace the annual Excel spreadsheet. Not linked
from the public website (projects showcase, portfolio, sitemap, `llms.txt`) and not indexed.

PHP and vanilla JavaScript with zero build steps and zero dependencies: upload the folder to the server and it works.

## Features

- **Week view**: Monday to Sunday, with totals, day charts, and a quick fill button to populate
  missing work days with standard hours in a single action (logged as a single history change).
- **Month view**: a single tap on "Normal day" logs today. Suggested hours adapt dynamically based on
  recent days, adjusting automatically across seasonal shift changes. Saturdays and public holidays follow dedicated rules:
  on holidays all worked hours count as overtime, matching the original spreadsheet specification.
  The month defaults to detailed view (one row per day); the overview provides a calendar layout with one week per row and weekly totals.
- **Day view**: time slots, overtime calculation, day category (regular work, rain, permit, vacation, sickness), notes.
- **Year view**: equivalent to the spreadsheet "Overview". Totals are computed in integer minutes, guaranteeing precision.
- **Audit history**: full audit trail recording state before and after each modification.
- **Utilities**: Excel export/import, printable views, quick shift presets, font size scaling, color themes. Administrative user management and batch import.

The user guide is located in `guida.html`, linked from the login view and from **More**, and can be printed.
Images in `assets/guida/` display mock placeholder data: never use actual private work hours when updating screenshots.

## Roles

| | User | Administrator | Master Admin |
|---|:-:|:-:|:-:|
| Log and modify personal hours | ✓ | ✓ | ✓ |
| View personal hour audit log | ✓ | ✓ | ✓ |
| View and edit all team members' hours | | ✓ | ✓ |
| **Revert changes from audit history** | | ✓ | ✓ |
| Approve users and import from Excel | | ✓ | ✓ |
| Appoint and demote administrators | | | ✓ |
| Update user names and emails | | | ✓ |
| Delete user accounts without hour logs | | | ✓ |

An account with existing logged hours cannot be deleted, only deactivated: preserving historical hour records. Changing an account's email immediately revokes access via the old Google account and terminates all active sessions.

Hour log edits (additions, modifications, deletions, imports) can be reverted via history. Account and shift preset modifications are logged for admins but cannot be auto-reverted. If a day has been modified again after the change to be reverted, a warning prompt is displayed. The reversion action itself is recorded in history and can also be undone.

## Installation

1. Copy `credentials-sample.php` to `credentials.php` and fill in credentials. This file is gitignored.
2. Upload the folder to the server and navigate to the page: tables (`dad_rend_*`) are provisioned automatically.
3. Sign in with Google using the email configured in `$master_admin_email` to initialise Master Admin status.
4. Under **More → Users**, invite team members using their Google email addresses: allowing instant access.
5. Select a user in the header switcher ("Hours of") and upload their annual spreadsheet via **More → Import from Excel**:
   a month-by-month reconciliation summary highlights any discrepancies prior to confirmation.

Legacy tables `dad_rend_work_days`, `work_shifts`, `presets`, and `audit_log` from version 1, if present, are unused and can be safely dropped.

## Local Development

```bash
php -S localhost:8080 -t ../../..
```

Then visit `http://localhost:8080/projects/dad/rendicontazione/`. Running via the built-in PHP web server automatically uses `data/dev.sqlite` instead of MySQL, rendering development login shortcuts. These test shortcuts are active exclusively when served via `php -S` from localhost (`is_local_dev()` in `lib/bootstrap.php`): they cannot be triggered on Apache.

## Privacy & Sensitive Data

This repository is public: production credentials, live databases, and Excel workbooks must never be committed. `.gitignore` excludes `credentials.php`, `data/`, and `*.xlsx`; all runtime records reside securely in the database or are transiently processed during imports.

## Structure

```
api/index.php       JSON API endpoints (api/index.php?action=...)
assets/app.js       application views and client logic
assets/ui.js        DOM utilities, date/time formatting, modals, notifications
assets/app.css      styling: mobile-first, light/dark themes, print stylesheets
assets/icons/       app launcher icons
lib/bootstrap.php   configuration, database connection, JSON output helpers
lib/auth.php        user accounts, sessions, Google OAuth verification
lib/timesheet.php   daily records, input validation, totals, shift presets
lib/history.php     audit history logging and state rollback
lib/xlsx.php        pure PHP Excel reader and writer (requires zlib extension only)
lib/schema.php      database schema definitions for MySQL/MariaDB and SQLite
```
