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
3. Create `.mail-config.php` from the following template:

```php
<?php
return [
    'smtp_host' => 'your-smtp-host',
    'smtp_port' => 465,
    'smtp_user' => 'your-smtp-user',
    'smtp_password' => 'your-smtp-password',
    'from_name' => 'Saverio Morelli',
    'from_email' => 'noreply@saveriomorelli.com',
    'to_email' => 'your-email@example.com',
    'altcha_secret' => 'your-random-secret-key',
];
```

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
