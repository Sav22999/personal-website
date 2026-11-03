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

$debug = false;           // true only for local tests: shows error details in the API responses
