<?php
/**
 * ThinkHop — template of credentials.php (git-ignored, blocked from the browser).
 * install-db.php writes the real file; to do it by hand, copy this one to credentials.php and fill it in.
 * Without credentials.php the game works the same, but the accounts are hidden.
 */

$localhost_db  = "localhost";
$username_db   = "";
$password_db   = "";
$database_name = "";

$table_prefix  = "th_";

// Sign in with Google (optional): OAuth client ID of type "Web application" (the same as FoodTrack and Giftwise)
$google_client_id = "";

// Optional: the SMTP server that sends the link confirming new accounts. Leave null to use the site's contact-form
// settings (/.mail-config.php in the site root). With neither, no email is sent and the check is not required.
$mail_config = null;
// $mail_config = [
//     'smtp_host' => 'smtp.example.com', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',
//     'smtp_user' => 'noreply@example.com', 'smtp_password' => '...',
//     'from_email' => 'noreply@example.com', 'from_name' => 'ThinkHop',
// ];

$debug = false;
