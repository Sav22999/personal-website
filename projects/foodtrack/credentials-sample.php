<?php
/**
 * FoodTrack — database credentials (SAMPLE).
 *
 * 1. Copy this file to  credentials.php  (same folder), or let install-db.php create it for you.
 * 2. Fill in the details of the MySQL database (Aruba: Hosting Linux -> Database MySQL / phpMyAdmin).
 *
 * credentials.php is excluded from git (.gitignore) and blocked from the browser (.htaccess):
 * never commit it and never publish real credentials.
 */

$localhost_db  = "<YOUR DATABASE HOST>";      // e.g. 89.46.111.xxx (on Aruba it is an IP, not "localhost")
$username_db   = "<YOUR DATABASE USERNAME>";
$password_db   = "<YOUR DATABASE PASSWORD>";
$database_name = "<YOUR DATABASE NAME>";

$table_prefix  = "ft_";   // every FoodTrack table starts with this prefix

// Optional: "Sign in with Google". Client ID of a Google Cloud "OAuth client ID" of type "Web application"
// (e.g. 1234567890-abc123.apps.googleusercontent.com). Empty = Google login disabled. It is not a secret.
$google_client_id = "";

// Optional: emails (welcome message on sign-up, notice of new accounts to the master admin).
// Leave null to use the site's contact-form settings (/.mail-config.php in the site root), if present;
// with neither, no email is sent. The SMTP password is a secret: this file is never committed.
$mail_config = null;
// $mail_config = [
//     'smtp_host' => 'smtps.aruba.it', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',   // or 587 + 'tls'
//     'smtp_user' => 'noreply@example.com', 'smtp_password' => '<SMTP PASSWORD>',
//     'from_email' => 'noreply@example.com', 'from_name' => 'FoodTrack',
// ];
$mail_notify_admin = true;   // false = do not email the master admin when a new account is created

$debug = false;           // true only for local tests: shows error details in the API responses
