<?php
$description = 'ThinkHop – the free logic game: liars, treasure chests, mini sudoku, absurd reasoning, traps and riddles. 100 worlds, 1,000 levels, play by difficulty, a daily ranking, five languages, works offline.';
$v = '1.8.52';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <title>ThinkHop: Logic and Maths Game – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/thinkhop/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="ThinkHop: Logic and Maths Game – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/thinkhop/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/thinkhop/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="ThinkHop – logic and maths game by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ThinkHop: Logic and Maths Game – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/thinkhop/og-image.png">
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": ["VideoGame", "WebApplication"],
            "name": "ThinkHop",
            "url": "https://www.saveriomorelli.com/projects/thinkhop/",
            "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            "applicationCategory": "GameApplication",
            "genre": "Puzzle game",
            "operatingSystem": "Any (web browser)",
            "playMode": "SinglePlayer",
            "inLanguage": ["en", "it", "es", "fr", "de"],
            "isAccessibleForFree": true,
            "image": "https://www.saveriomorelli.com/projects/thinkhop/og-image.png",
            "author": { "@type": "Person", "name": "Saverio Morelli", "url": "https://www.saveriomorelli.com/" },
            "offers": { "@type": "Offer", "price": "0", "priceCurrency": "EUR" }
        }
    </script>
    <meta name="theme-color" content="#FFFBF2" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#141127" media="(prefers-color-scheme: dark)">
    <meta name="color-scheme" content="light dark">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ThinkHop">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="stylesheet" href="style.css?v=<?= $v ?>">
    <script>try {
            var t = JSON.parse(localStorage.getItem('thinkhop.theme'));
            if (t === 'light' || t === 'dark') document.documentElement.setAttribute('data-theme', t)
        } catch (e) {
        }</script>
</head>
<body>
<div id="app">
    <div class="home skeleton-page" aria-busy="true">
        <div class="sk-hero"><span class="sk sk-logo"></span><span class="sk-hero-text"><span class="sk sk-title"></span><span class="sk" style="width:70%"></span></span></div>
        <div class="sk-card"><span class="sk" style="width:30%"></span><span class="sk sk-bar"></span><span class="sk sk-button"></span><span class="sk sk-button"></span></div>
        <div class="sk-pair"><span class="sk-card sk-short"></span><span class="sk-card sk-short"></span></div>
    </div>
    <noscript><p style="padding:24px">ThinkHop needs JavaScript.</p></noscript>
</div>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<script src="i18n.js?v=<?= $v ?>" defer></script>
<script src="i18n-puzzles.js?v=<?= $v ?>" defer></script>
<script src="releases.js?v=<?= $v ?>" defer></script>
<script src="riddles.js?v=<?= $v ?>" defer></script>
<script src="riddles-more.js?v=<?= $v ?>" defer></script>
<script src="engine.js?v=<?= $v ?>" defer></script>
<script src="puzzles.js?v=<?= $v ?>" defer></script>
<script src="twemoji.js?v=<?= $v ?>" defer></script>
<script src="avatars.js?v=<?= $v ?>" defer></script>
<script src="ui.js?v=<?= $v ?>" defer></script>
<script src="account.js?v=<?= $v ?>" defer></script>
<script src="social.js?v=<?= $v ?>" defer></script>
<script src="mascots.js?v=<?= $v ?>" defer></script>
<script src="solution.js?v=<?= $v ?>" defer></script>
<script src="help.js?v=<?= $v ?>" defer></script>
<script src="reports.js?v=<?= $v ?>" defer></script>
<script src="views.js?v=<?= $v ?>" defer></script>
<script src="game.js?v=<?= $v ?>" defer></script>
</body>
</html>
