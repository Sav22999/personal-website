<?php
include_once(__DIR__ . '/maintenance.php');
$base_url = 'https://www.saveriomorelli.com';
$page_title = isset($title) ? $title . ' – Saverio Morelli' : 'Saverio Morelli';
$page_description = isset($description) ? $description : 'Saverio Morelli – Frontend Developer & UX Designer. Open-source browser extensions, Android apps, and web tools.';
$page_url = isset($canonical) ? $base_url . $canonical : $base_url . '/';
$page_image = isset($og_image) ? $og_image : $base_url . '/images/opengraph/image.png';
$page_locale = isset($page_lang) ? $page_lang : 'en_US';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_description) ?>">
<meta name="author" content="Saverio Morelli">
<link rel="canonical" href="<?= htmlspecialchars($page_url) ?>">
<meta name="robots" content="<?= isset($robots) ? $robots : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' ?>">
<link rel="alternate" type="text/markdown" href="/llms.txt" title="Saverio Morelli for language models">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
<meta property="og:url" content="<?= htmlspecialchars($page_url) ?>">
<meta property="og:image" content="<?= htmlspecialchars($page_image) ?>">
<meta property="og:site_name" content="Saverio Morelli">
<meta property="og:locale" content="<?= $page_locale ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($page_image) ?>">
<link rel="icon" href="/images/icon-primary.svg" type="image/svg+xml">
<link rel="icon" href="/images/favicon-32x32.png" sizes="32x32" type="image/png">
<link rel="icon" href="/images/favicon-16x16.png" sizes="16x16" type="image/png">
<link rel="icon" href="/images/icon.png" sizes="192x192" type="image/png">
<link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
<meta name="theme-color" content="#222222">
<link rel="sitemap" type="application/xml" href="/sitemap.xml">
<link rel="stylesheet" href="/style.css">
