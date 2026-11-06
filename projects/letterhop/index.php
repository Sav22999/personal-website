<?php
$description = 'LetterHop – the free word ladder game: go from one word to another changing one letter at a time. Three levels, five languages, works offline. No sign-up.';
$v = '1.0.6';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <title>LetterHop: Word Ladder Game – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/letterhop/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="LetterHop: Word Ladder Game – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/letterhop/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/letterhop/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="LetterHop – word ladder game by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="LetterHop: Word Ladder Game – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/letterhop/og-image.png">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["VideoGame", "WebApplication"],
        "name": "LetterHop",
        "alternateName": ["Word Ladder", "Scala di parole"],
        "url": "https://www.saveriomorelli.com/projects/letterhop/",
        "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "applicationCategory": "GameApplication",
        "genre": "Word game",
        "operatingSystem": "Any (web browser)",
        "playMode": "SinglePlayer",
        "inLanguage": ["en", "it", "es", "fr", "de"],
        "isAccessibleForFree": true,
        "image": "https://www.saveriomorelli.com/projects/letterhop/og-image.png",
        "author": { "@type": "Person", "name": "Saverio Morelli", "url": "https://www.saveriomorelli.com/" },
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "EUR" }
    }
    </script>
    <meta name="theme-color" content="#FFF8F1" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#16100B" media="(prefers-color-scheme: dark)">
    <meta name="color-scheme" content="light dark">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="LetterHop">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="stylesheet" href="style.css?v=<?= $v ?>">
    <script>try{var t=JSON.parse(localStorage.getItem('letterhop.theme'));if(t==='light'||t==='dark')document.documentElement.setAttribute('data-theme',t)}catch(e){}</script>
</head>
<body>
<div id="app"><noscript><p style="padding:24px">LetterHop needs JavaScript.</p></noscript></div>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<script src="i18n.js?v=<?= $v ?>" defer></script>
<script src="releases.js?v=<?= $v ?>" defer></script>
<script src="engine.js?v=<?= $v ?>" defer></script>
<script src="game.js?v=<?= $v ?>" defer></script>
</body>
</html>
