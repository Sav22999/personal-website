<?php
/**
 * SavComponents — template of credentials.php (git-ignored, blocked from the browser).
 * Copy it to credentials.php and fill it in: the tables (prefix below) are created on the first API request.
 * Only the API lives here (accounts, favourites, saved themes); the portal and the library are on savcomponents.com.
 */

$localhost_db  = "localhost";
$username_db   = "";
$password_db   = "";
$database_name = "";

$table_prefix  = "sc_";

// Master admins: they manage every account (list, disable, confirm, delete). Their account email, once confirmed.
$master_emails = [];

// SMTP that sends the "confirm your email" and "reset your password" links. Leave null to use the site's
// contact-form settings (/.mail-config.php in the site root). false switches email off entirely (local tests:
// with $debug on, the links go to the PHP error log).
$mail_config = null;
// $mail_config = [
//     'smtp_host' => 'smtp.example.com', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',
//     'smtp_user' => 'noreply@example.com', 'smtp_password' => '...',
//     'from_email' => 'noreply@example.com', 'from_name' => 'SavComponents',
// ];

// The portal (savcomponents.com): email links and theme links point there.
$portal_url = "https://savcomponents.com/";
// Origins allowed to call the API from a browser (CORS). The portal signs in with a token, not a cookie.
$allowed_origins = ["https://savcomponents.com", "https://www.savcomponents.com"];

// "Continue with SavAccount" (https://www.savaccount.eu). In the SavAccount panel create a service for SavComponents and add the
// redirect address https://www.saveriomorelli.com/projects/savcomponents/api/?r=auth.sav.callback (the one of this API).
// Empty client id or secret: the button does not show.
$savaccount_client_id = "";
$savaccount_client_secret = "";
// $savaccount_redirect_uri = ""; // only if the address above is not where this API answers
// $savaccount_issuer = "https://www.savaccount.eu"; // only for tests against another SavAccount

$debug = false;
