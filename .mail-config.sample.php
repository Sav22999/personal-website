<?php
/*
 * Sample file for the contact form (/contact-me/).
 * Copy it to .mail-config.php (site root) and fill in the real values.
 * .mail-config.php is excluded from git (.gitignore): never commit it.
 * altcha_secret: a long random string, e.g. generated with  php -r 'echo bin2hex(random_bytes(32));'
 */

return [
    'smtp_host' => 'smtps.example.com',
    'smtp_port' => 465,
    'smtp_user' => 'noreply@example.com',
    'smtp_password' => 'SMTP_PASSWORD',
    'smtp_encryption' => 'ssl',
    'from_email' => 'noreply@example.com',
    'from_name' => 'Nome mittente',
    'to_email' => 'tu@example.com',
    'altcha_secret' => 'stringa-casuale-lunga',
];
