<?php
/**
 * SavAccount — template of credentials.php (git-ignored, blocked from the browser).
 * Copy it to credentials.php and fill it in. The tables are created on the first request.
 */

$enabled = false;          // true when everything below is filled in: until then the API answers 503

$localhost_db  = "";       // on Aruba it is an IP, not "localhost"
$username_db   = "";
$password_db   = "";
$database_name = "";
$table_prefix  = "sva_";

// Two different random keys of 64 characters each:  php -r 'echo bin2hex(random_bytes(32));'
// secret_key mixes into the passwords and encrypts the authenticator secrets and the signing key. KEEP A BACKUP of it
// and never change it: without it nobody can sign in any more.
$secret_key  = "";
// gateway_key is the same as 'gateway_key' in gateway-config.php on savaccount.eu. It can be changed (on both) any time.
$gateway_key = "";

$site_url = "https://www.savaccount.eu";   // also the OpenID Connect issuer: never change it

// Who manages the services: a confirmed address of their account. They also need two-step verification.
$master_emails = [];

// The emails: confirmation codes, two-step codes, security notices
$mail_config = [
    'smtp_host' => 'smtps.aruba.it', 'smtp_port' => 465, 'smtp_encryption' => 'ssl',
    'smtp_user' => 'noreply@savaccount.eu', 'smtp_password' => '',
    'from_email' => 'noreply@savaccount.eu', 'from_name' => 'SavAccount',
];

$check_pwned_passwords = true;   // refuse passwords found in known leaks (Have I Been Pwned, k-anonymity)
$debug = false;                  // true only for local tests
