<?php
/**
 * Rendicontazione Ore — database credentials (SAMPLE).
 *
 * 1. Copy this file to  credentials.php  (same folder), or let install-db.php create it for you.
 * 2. Fill in the details of the MySQL database (it can be the same database as FoodTrack/Giftwise: tables use the "dad_rend_" prefix).
 *
 * credentials.php is excluded from git (.gitignore) and blocked from the browser (.htaccess):
 * never commit it and never publish real credentials.
 */

$localhost_db = "<YOUR DATABASE HOST>";      // e.g. 89.46.111.xxx (on Aruba it is an IP, not "localhost")
$username_db = "<YOUR DATABASE USERNAME>";
$password_db = "<YOUR DATABASE PASSWORD>";
$database_name = "<YOUR DATABASE NAME>";

$table_prefix = "dad_rend_";   // every table starts with this prefix

// "Sign in with Google": Client ID of a Google Cloud "OAuth client ID" of type "Web application"
$google_client_id = "126260557873-mss9iuldd7uttsj9pqd0oqbtntvvo6hl.apps.googleusercontent.com";

// Email of the designated Master Admin. When this user logs in with Google,
// they are automatically set as active Master Admin (role 10).
$master_admin_email = "saverio.morelli@gmail.com";

// Debug mode: true allows local testing and detailed error messages
$debug = false;
