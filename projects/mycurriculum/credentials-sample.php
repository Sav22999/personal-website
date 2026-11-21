<?php
/**
 * MyCurriculum — template of credentials.php (git-ignored, blocked from the browser).
 * install-db.php writes the real file; to do it by hand, copy this one to credentials.php and fill it in.
 * Without credentials.php the app cannot start.
 */

$localhost_db  = "localhost";
$username_db   = "";
$password_db   = "";
$database_name = "";

$table_prefix  = "mc_";

// Sign in with Google (optional): OAuth client ID of type "Web application" 
$google_client_id = "";

// Optional: the SMTP server that sends the link confirming new accounts. Leave null to use the site's contact-form
// settings (/.mail-config.php in the site root). The check is always required: with neither, nobody can confirm an account (in $debug the link goes to the PHP error log).
$mail_config = null;   // false switches email off entirely (local tests)
// $mail_config = [
//     'smtp_host' => 'smtp.example.com', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',
//     'smtp_user' => 'noreply@example.com', 'smtp_password' => '...',
//     'from_email' => 'noreply@example.com', 'from_name' => 'MyCurriculum',
// ];

$debug = false;
