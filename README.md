# saveriomorelli.com

Personal website of Saverio Morelli — frontend developer & UX designer.

## Structure

- `/` — Main site pages (home, about, projects, contact, donate)
- `/contact-me/` — Contact form with ALTCHA proof-of-work anti-spam
- `/htmlpertutti/` — *HTML per tutti* book page
- `/projects/` — Project showcase pages
- `/include/` — Shared PHP components (head, nav, footer, email template)
- `/images/` — Icons, project images, and OpenGraph assets
- `/old/` — Archived previous versions of the site

## Setup

1. Clone the repository
2. Run `composer install` to install dependencies (Symfony Mailer)
3. Create the local config files from the samples (the real files are git-ignored and blocked from the web by `.htaccess`):
   - `.mail-config.sample.php` → `.mail-config.php` (SMTP and ALTCHA secret for the contact form)
   - `old/include/variables-sample.php` → `old/include/variables.php` (database for the legacy `/old/` projects)
   - `old/api/emoticolor/credentials-sample.php` → `old/api/emoticolor/credentials.php`
   - `projects/ripasso-fipav/lib/config.example.php` → `projects/ripasso-fipav/lib/config.php`
   - `.maintenance-sample` → `.maintenance` (only when you want to enable maintenance mode)
4. Deploy to a PHP-capable web server

## Anti-spam

The contact form uses multiple layers of protection:

- **ALTCHA** proof-of-work challenge (SHA-256)
- Challenge expiration (5 min TTL)
- One-time challenge usage (replay prevention)
- IP-based rate limiting (5 messages/hour)
- Honeypot field
- Content heuristics

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.
