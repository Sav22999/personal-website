<?php
/**
 * OpenNewsletter — template of credentials.php (git-ignored, blocked from the browser).
 * install-db.php writes the real file; to do it by hand, copy this one to credentials.php and fill it in.
 * Without credentials.php the app cannot start.
 */

$localhost_db  = "localhost";
$username_db   = "";
$password_db   = "";
$database_name = "";

$table_prefix  = "on_";

// Encrypts the SMTP settings of every group (outgoing address). 64 hex characters:
//   php -r 'echo bin2hex(random_bytes(32));'
// NEVER change it once groups have configured their sending: their SMTP passwords could no longer be read.
$secret_key = "";

// Master admins of the whole OpenNewsletter (not of a single group): their account email, once confirmed.
// They see every group, but never the SMTP settings (only the sending address, read-only).
$master_emails = [];

// Sign in with Google (optional): OAuth client ID of type "Web application"
$google_client_id = "";

// SMTP of the platform itself: account confirmations, invitations, approval requests. Not the newsletters,
// which leave from each group's own address. Leave null to use the site's contact-form settings (/.mail-config.php).
$mail_config = null;   // false switches platform email off entirely (local tests; in $debug links go to the PHP log)
// $mail_config = [
//     'smtp_host' => 'smtp.example.com', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',
//     'smtp_user' => 'noreply@example.com', 'smtp_password' => '...',
//     'from_email' => 'noreply@example.com', 'from_name' => 'OpenNewsletter',
// ];

// Timezone of the groups created from now on (each group can change its own)
$default_timezone = "Europe/Rome";

$debug = false;
