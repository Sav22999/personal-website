<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Rendicontazione Ore</title>
    <link rel="manifest" href="assets/manifest.webmanifest">
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <meta name="theme-color" content="#1c1c1e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="assets/app.css">
    <script>
        // Set theme before render to avoid flash
        try {
            var saved = localStorage.getItem('dad_rend_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', saved);
        } catch(e) {}
    </script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div id="app">
        <!-- Skeleton loader while app loads -->
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 16px; color: var(--text-muted);">
            <div style="width: 48px; height: 48px; border: 3px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
            <p style="font-size: 0.95rem;">Caricamento portale...</p>
        </div>
        <style>
            @keyframes spin { to { transform: rotate(360deg); } }
        </style>
    </div>

    <script src="assets/app.js"></script>
</body>
</html>
