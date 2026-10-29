<?php
/**
 * Configuration of the "Fischietto" portal (FIPAV referee rulebook review).
 *
 * 1. Copy this file to  lib/config.php  (same folder)
 * 2. Fill in the database details from the Aruba control panel
 *    (Hosting Linux -> MySQL databases: host, database name, user, password)
 *
 * lib/config.php is excluded from git (.gitignore) and cannot be reached from the browser
 * (lib/.htaccess blocks any direct access).
 */
return [
    'db' => [
        // On Aruba the host is an IP address shown in the control panel (e.g. 89.46.111.xxx), not "localhost"
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'NOME_DATABASE',
        'user'    => 'UTENTE_DATABASE',
        'pass'    => 'PASSWORD_DATABASE',
        'charset' => 'utf8mb4',
    ],

    // Table prefix: useful when the database is shared with the rest of the site
    'table_prefix' => 'fr_',

    // Session cookie name
    'session_name' => 'FIPAVSESS',

    // Maximum session inactivity (seconds). 30 days.
    'session_lifetime' => 60 * 60 * 24 * 30,

    // Optional: address that receives an email when a new user registers.
    // On Aruba the sender must be a mailbox on your domain (e.g. noreply@saveriomorelli.com).
    'notify_email' => '',
    'mail_from'    => '',

    // Public URL of the portal, used in emails (e.g. https://www.saveriomorelli.com/projects/ripasso-fipav/)
    'public_url' => '',

    // Show error details in API responses. Keep it false in production.
    'debug' => false,
];
