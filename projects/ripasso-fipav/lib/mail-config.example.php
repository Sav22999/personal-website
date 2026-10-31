<?php
/**
 * Mailbox used by Fischietto to send emails (account activated, registration rejected, new registration notice).
 *
 * 1. Copy this file to  lib/mail-config.php  (same folder)
 * 2. Fill in the details of the noreply mailbox (the same one used by the site's contact form)
 *
 * lib/mail-config.php is excluded from git (.gitignore) and cannot be reached from the browser
 * (lib/.htaccess blocks any direct access). Without it the portal works, but sends no emails.
 */
return [
    'smtp_host'       => 'smtps.example.com',
    'smtp_port'       => 465,
    'smtp_user'       => 'noreply@example.com',
    'smtp_password'   => 'SMTP_PASSWORD',
    'smtp_encryption' => 'ssl',            // ssl (port 465) or tls (STARTTLS, port 587)
    'from_email'      => 'noreply@example.com',
    'from_name'       => 'Fischietto',
];
