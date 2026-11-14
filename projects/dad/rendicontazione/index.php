<?php
// Files get their modification time as version, so a new upload is never hidden by the browser cache
$v = function (string $file): string {
    return $file . '?v=' . (@filemtime(__DIR__ . '/' . $file) ?: 1);
};
header('X-Robots-Tag: noindex, nofollow, noarchive');
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Rendicontazione Ore</title>
    <link rel="manifest" href="<?= $v('assets/manifest.webmanifest') ?>">
    <link rel="icon" href="<?= $v('assets/icons/icon.svg') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= $v('assets/icons/apple-touch-icon.png') ?>">
    <meta name="theme-color" content="#0f6b5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Ore">
    <link rel="stylesheet" href="<?= $v('assets/app.css') ?>">
    <script>
        // Before the first paint, so the page never flashes in the wrong theme or size
        try {
            document.documentElement.dataset.theme = localStorage.getItem('rend.theme') || 'auto';
            document.documentElement.dataset.text = localStorage.getItem('rend.text') || 'normal';
        } catch (e) {}
    </script>
</head>
<body>
    <div id="app"><p class="boot">Caricamento…</p></div>
    <div id="toasts" role="status" aria-live="polite"></div>
    <div id="print"></div>
    <noscript><p class="boot">Per usare questa pagina serve JavaScript.</p></noscript>
    <script src="<?= $v('assets/ui.js') ?>"></script>
    <script src="<?= $v('assets/app.js') ?>"></script>
</body>
</html>
