<?php
/**
 * Rendicontazione Ore: credentials (SAMPLE).
 *
 * Copy this file to credentials.php (same folder) and fill it in. credentials.php is excluded
 * from git and blocked from the browser: never commit it and never paste its values in a doc.
 *
 * Running locally with `php -S` never reads the database below: it uses data/dev.sqlite instead.
 */

$localhost_db = "<YOUR DATABASE HOST>";      // on Aruba it is an IP, not "localhost"
$username_db = "<YOUR DATABASE USERNAME>";
$password_db = "<YOUR DATABASE PASSWORD>";
$database_name = "<YOUR DATABASE NAME>";

$table_prefix = "dad_rend_";   // every table starts with this prefix

// Client ID of a Google Cloud "OAuth client ID" of type "Web application". It is not a secret.
$google_client_id = "<YOUR GOOGLE CLIENT ID>";

// Google account that becomes Master Admin when it signs in. Nobody else can become one.
$master_admin_email = "<YOUR GOOGLE EMAIL>";

$debug = false;   // true shows error details in API responses: keep it false on the server
