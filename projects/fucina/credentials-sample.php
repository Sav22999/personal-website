<?php
/**
 * Fucina — database credentials (SAMPLE).
 *
 * 1. Copy this file to  credentials.php  (same folder), or let install-db.php create it for you.
 * 2. Fill in the details of the MySQL database (it can be the same database as the other apps: tables use the "fc_" prefix).
 *
 * credentials.php is excluded from git (.gitignore) and blocked from the browser (.htaccess):
 * never commit it and never publish real credentials.
 */

$localhost_db = "<YOUR DATABASE HOST>";      // e.g. 89.46.111.xxx (on Aruba it is an IP, not "localhost")
$username_db = "<YOUR DATABASE USERNAME>";
$password_db = "<YOUR DATABASE PASSWORD>";
$database_name = "<YOUR DATABASE NAME>";

$table_prefix = "fc_";   // every Fucina table starts with this prefix

// Optional: "Sign in with Google". Client ID of a Google Cloud "OAuth client ID" of type "Web application"
// (the same one used by the other apps works too). Empty = Google login disabled. It is not a secret.
$google_client_id = "";

// Optional: emails (welcome message, friend requests). Leave null to use the site's contact-form settings
// (/.mail-config.php in the site root), if present; with neither, no email is sent.
$mail_config = null;
// $mail_config = [
//     'smtp_host' => 'smtps.aruba.it', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',   // or 587 + 'tls'
//     'smtp_user' => 'noreply@example.com', 'smtp_password' => '<SMTP PASSWORD>',
//     'from_email' => 'noreply@example.com', 'from_name' => 'Fucina',
// ];

$debug = false;           // true only for local tests: shows error details in the API responses
