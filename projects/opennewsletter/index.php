<?php
/**
 * OpenNewsletter — entry point: the app (sign-in, groups, editor), all in assets/app.js.
 * Public pages: subscribe.php, unsubscribe.php, terms.php, widget.js. API: api/. Cron: projects/cron/opennewsletter-*.php.
 */

declare(strict_types=1);

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'");
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>OpenNewsletter</title>
    <meta name="description" content="Write, approve and send newsletters as a team: drag-and-drop editor, approval workflow, subscribers in every language, a subscription page and a widget for your site.">
    <meta name="theme-color" content="#5B3DF5">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="stylesheet" href="assets/app.css?v=19">
</head>
<body>
<div id="app"><noscript>OpenNewsletter needs JavaScript.</noscript></div>
<script type="module" src="assets/app.js?v=18"></script>
</body>
</html>
