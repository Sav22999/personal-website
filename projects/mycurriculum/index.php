<?php
/**
 * MyCurriculum — entry point.
 *   ./              the app (sign-in and CV editor, assets/app.js)
 *   ./?user=NAME    the public web page of a CV (lib/public-page.php)
 */

declare(strict_types=1);

if (isset($_GET['user'])) {
    require __DIR__ . '/lib/public-page.php';
    exit;
}

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>MyCurriculum — your CV in every language</title>
    <meta name="description" content="Create your CV in minutes, in as many languages as you need: choose a template, download each version as a PDF and share it as a web page. Import your existing CV from a PDF.">
    <meta name="theme-color" content="#3D52D5">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <meta property="og:title" content="MyCurriculum">
    <meta property="og:description" content="Your CV in every language you speak: templates, PDF download and a public web page.">
    <meta property="og:image" content="icons/og-image.png">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="assets/app.css?v=2">
    <link rel="stylesheet" href="assets/cv.css?v=2">
</head>
<body>
<div id="app"><noscript>MyCurriculum needs JavaScript.</noscript></div>
<script type="module" src="assets/app.js?v=2"></script>
</body>
</html>
