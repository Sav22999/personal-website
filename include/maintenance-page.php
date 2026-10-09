<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance &ndash; Saverio Morelli</title>
    <link rel="icon" href="/images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Stack Sans Notch';
            src: url('/fonts/StackSansNotch.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #222222;
            color: #f0f0f0;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 480px;
        }

        .gear {
            width: 72px;
            height: 72px;
            margin: 0 auto 36px;
            color: #66CBFF;
            animation: spin 60s linear infinite;
        }

        .gear svg {
            width: 100%;
            height: 100%;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        h1 {
            font-family: 'Stack Sans Notch', 'Inter', sans-serif;
            font-size: 2rem;
            font-weight: 400;
            color: #66CBFF;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        p {
            font-size: 1rem;
            color: #aaaaaa;
            line-height: 1.7;
        }

        .brand {
            margin-top: 48px;
            font-family: 'Stack Sans Notch', 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #555555;
            letter-spacing: 0.01em;
        }

        .gear,
        h1,
        p,
        .brand {
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .gear {
            animation: spin 60s linear infinite, fadeInUp 0.6s ease forwards 0.1s;
        }

        h1 {
            animation: fadeInUp 0.6s ease forwards 0.25s;
        }

        p {
            animation: fadeInUp 0.6s ease forwards 0.4s;
        }

        .brand {
            animation: fadeInUp 0.6s ease forwards 0.55s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="gear">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
    </div>
    <h1>Maintenance</h1>
    <p>We're doing a little maintenance. The site will be back shortly &mdash; try again soon.</p>
    <p class="brand">Saverio Morelli</p>
</div>
</body>
</html>
