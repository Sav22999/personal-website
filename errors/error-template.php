<?php
function render_error_page($code, $title, $message)
{
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $code; ?> &ndash; <?php echo $title; ?></title>
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
                min-height: 100vh;
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

            .error-code {
                font-family: 'Stack Sans Notch', 'Inter', sans-serif;
                font-size: clamp(4rem, 12vw, 7rem);
                font-weight: 400;
                color: #3a3a3a;
                line-height: 1;
                margin-bottom: 8px;
                letter-spacing: -0.04em;
            }

            h1 {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 12px;
                letter-spacing: -0.02em;
            }

            p {
                font-size: 0.95rem;
                color: #aaaaaa;
                line-height: 1.6;
                margin-bottom: 32px;
            }

            .back-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 0.875rem;
                font-weight: 500;
                color: #66CBFF;
                text-decoration: none;
                padding: 10px 20px;
                border: 1px solid #3a3a3a;
                border-radius: 10px;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .back-link:hover {
                border-color: #66CBFF;
                background: rgba(102, 203, 255, 0.12);
            }

            .back-link svg {
                width: 16px;
                height: 16px;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <div class="error-code"><?php echo $code; ?></div>
        <h1><?php echo $title; ?></h1>
        <p><?php echo $message; ?></p>
        <a href="/" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
            Back to home
        </a>
    </div>
    </body>
    </html>
    <?php
}

?>
