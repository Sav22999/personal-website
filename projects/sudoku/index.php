<?php
$description = 'Sudoku – classic number puzzle game. Play against the computer with different difficulty levels. No sign-up needed.';
$v = 1;
$icon = fn($d, $size = 18, $sw = 2.2) => '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="' . $sw . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
$ICON_CLOSE = $icon('<path d="M6 6l12 12M18 6L6 18"/>', 20, 2.4);
$ICON_BULB = '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0012 2z"/>';
$ICON_BOOK = '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>';
$ICON_LEAVE = '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>';
$ICON_CHECK = $icon('<path d="M5 12l5 5L20 7"/>', 18, 3);
$ICON_UNDO = '<path d="M9 14L4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 010 11H11"/>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <title>Sudoku – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/sudoku/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Sudoku – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/sudoku/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/sudoku/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Sudoku – classic number puzzle game by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sudoku – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/sudoku/og-image.png">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": [
            "VideoGame",
            "WebApplication"
        ],
        "name": "Sudoku",
        "url": "https://www.saveriomorelli.com/projects/sudoku/",
        "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "applicationCategory": "GameApplication",
        "genre": "Puzzle game",
        "operatingSystem": "Any (web browser)",
        "playMode": "SinglePlayer",
        "inLanguage": [
            "en",
            "it",
            "es",
            "fr",
            "de"
        ],
        "isAccessibleForFree": true,
        "image": "https://www.saveriomorelli.com/projects/sudoku/og-image.png",
        "author": {
            "@type": "Person",
            "name": "Saverio Morelli",
            "url": "https://www.saveriomorelli.com/"
        },
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "EUR"
        }
    }
    </script>
    <meta name="theme-color" content="#0C110E">
    <meta name="color-scheme" content="dark">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Sudoku">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= $v ?>">
</head>
<body>

<div id="app">
    <!-- Lobby -->
    <div id="screen-lobby" class="screen active">
        <div class="lang-selector" id="lang-selector">
            <button type="button" class="lang-btn" data-lang="en">EN</button>
            <button type="button" class="lang-btn" data-lang="it">IT</button>
            <button type="button" class="lang-btn" data-lang="es">ES</button>
            <button type="button" class="lang-btn" data-lang="fr">FR</button>
            <button type="button" class="lang-btn" data-lang="de">DE</button>
        </div>
        <button type="button" class="lang-toggle" id="btn-lang-menu" aria-haspopup="dialog" data-i18n-title="language_title">
            <?= $icon('<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>', 18, 2) ?>
            <span id="lang-current">EN</span>
            <?= $icon('<path d="M6 9l6 6 6-6"/>', 14, 2.6) ?>
        </button>

        <div class="lobby-container">
            <section class="hero">
                <div class="logo-area">
                    <h1 class="logo-title"><img class="logo-mark" src="icons/favicon.svg" alt="" width="44" height="44"><span>Sudoku</span></h1>
                    <p class="logo-subtitle" data-i18n="lobby_subtitle">Classic number puzzle. Fill the grid with logic.</p>
                    <div class="hero-links">
                        <button type="button" class="link-btn howto-link" id="btn-howto">
                            <?= $icon('<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>', 16) ?>
                            <span data-i18n="howto_title">How to play</span>
                        </button>
                        <button type="button" class="link-btn howto-link" data-open-rules>
                            <?= $icon($ICON_BOOK, 16) ?>
                            <span data-i18n="rules_title">Game rules</span>
                        </button>
                    </div>
                </div>
                <div class="howto howto-inline">
                    <h2 data-i18n="howto_title">How to play</h2>
                    <ol class="howto-list" id="howto-list">
                        <li><span class="howto-num">1</span><div><strong data-i18n="howto1_title">Fill the grid</strong><p data-i18n="howto1_text">Each row, column, and 3x3 box must contain all numbers from 1 to 9.</p></div></li>
                        <li><span class="howto-num">2</span><div><strong data-i18n="howto2_title">No repeats</strong><p data-i18n="howto2_text">A number cannot appear twice in the same row, column, or 3x3 box.</p></div></li>
                        <li><span class="howto-num">3</span><div><strong data-i18n="howto3_title">Use logic</strong><p data-i18n="howto3_text">Use the given numbers to deduce the missing ones. Try different difficulties!</p></div></li>
                    </ol>
                    <button type="button" class="link-btn rules-more" data-open-rules>
                        <span data-i18n="rules_title">Game rules</span>
                        <?= $icon('<path d="M5 12h14M13 6l6 6-6 6"/>', 16, 2.4) ?>
                    </button>
                </div>
            </section>

            <section class="lobby-panel">
                <ol class="steps-indicator">
                    <li class="step active" id="dot-1">
                        <button type="button" class="step-btn" id="btn-step-1" disabled>
                            <span class="step-num"><span class="step-n">1</span><svg class="step-check" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg></span>
                            <span class="step-label" data-i18n="step_label_name">Your name</span>
                        </button>
                    </li>
                    <li class="step-line" aria-hidden="true"></li>
                    <li class="step" id="dot-2">
                        <span class="step-btn"><span class="step-num"><span class="step-n">2</span></span><span class="step-label" data-i18n="step_label_difficulty">Difficulty</span></span>
                    </li>
                </ol>

                <form id="step-name" class="lobby-step active" autocomplete="off">
                    <label class="step-title" for="player-name" data-i18n="step1_title">What's your name?</label>
                    <p class="step-hint" data-i18n="step1_hint">Your name will be shown during the game.</p>
                    <input type="text" id="player-name" maxlength="30" data-i18n-placeholder="name_placeholder" placeholder="e.g. Alex" required>
                    <button type="submit" class="btn btn-primary btn-block" id="btn-continue" disabled>
                        <span data-i18n="continue">Continue</span>
                        <?= $icon('<path d="M5 12h14M13 6l6 6-6 6"/>', 18, 2.4) ?>
                    </button>
                </form>

                <div id="step-mode" class="lobby-step">
                    <div class="greeting">
                        <span class="step-title" id="greeting-text"></span>
                        <button type="button" class="link-btn" id="btn-change-name" data-i18n="change_name">Change name</button>
                    </div>
                    <p class="step-hint" data-i18n="step2_hint">Choose your difficulty level.</p>

                    <div class="mode-panel active" data-panel="ai">
                        <div class="mode-card solo-card">
                            <div class="mode-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 8V4M9 14h.01M15 14h.01M2 13v3M22 13v3"/><circle cx="12" cy="3" r="1"/></svg></div>
                            <div class="mode-body">
                                <h3 data-i18n="ai_title">Play Sudoku</h3>
                                <p data-i18n="ai_desc">Solve the puzzle at your own pace. Choose from three difficulty levels.</p>
                            </div>
                            <div class="field">
                                <span class="field-label" data-i18n="level_label">Difficulty</span>
                                <div class="chips" role="radiogroup">
                                    <label class="chip"><input type="radio" name="ai-level" value="easy"><span data-i18n="level_easy">Facile</span></label>
                                    <label class="chip"><input type="radio" name="ai-level" value="medium" checked><span data-i18n="level_medium">Media</span></label>
                                    <label class="chip"><input type="radio" name="ai-level" value="hard"><span data-i18n="level_hard">Difficile</span></label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" id="btn-start-ai" data-i18n="start_game">Start game</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="site-footer">
            <div class="credit-block">
                <a class="credit" href="https://www.saveriomorelli.com/" target="_blank" rel="noopener"><span data-i18n="credit_by">A project by</span> <span class="credit-name">Saverio Morelli</span></a>
                <span class="credit-ai"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.2 6.6 6.8 2.4-6.8 2.4L12 20.5l-2.2-6.6L3 11.5l6.8-2.4z"/></svg><span data-i18n="credit_ai">Built with AI</span></span>
            </div>
            <button type="button" class="version-btn" id="btn-version" data-i18n-title="release_notes">
                <span id="app-version"></span>
                <?= $icon('<path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/>', 13, 2.4) ?>
            </button>
        </footer>
    </div>

    <!-- Game -->
    <div id="screen-game" class="screen">
        <header class="game-header">
            <div class="game-header-left">
                <h2 class="game-title"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>Sudoku</span></h2>
                <div class="difficulty-badge" id="difficulty-badge"></div>
            </div>
            <div class="game-header-right">
                <span class="mode-badge" id="mode-badge"></span>
                <button type="button" class="btn-icon" data-open-rules data-i18n-title="rules_title"><?= $icon($ICON_BOOK, 20, 2) ?></button>
                <button type="button" class="btn-icon" id="btn-leave-game" data-i18n-title="leave"><?= $icon($ICON_LEAVE, 20, 2) ?></button>
            </div>
        </header>

        <div class="game-body">
            <div class="board-area" id="board-area">
                <div id="banner" class="banner hidden" role="status"></div>
                <div class="board-column" id="board-column">
                    <div class="board-wrap" id="board-wrap">
                        <div class="sudoku-board" id="sudoku-board"></div>
                    </div>
                </div>
            </div>

            <aside class="side-panel" id="side-panel">
                <div class="side-inner">
                    <div class="tip-card" id="tip-card"></div>
                    <div class="game-actions" id="game-actions">
                        <button type="button" class="action-btn" id="btn-check"><?= $icon($ICON_CHECK, 20, 2) ?><span data-i18n="check">Check</span></button>
                        <button type="button" class="action-btn" id="btn-hint"><?= $icon($ICON_BULB, 20, 2) ?><span data-i18n="hint">Hint</span></button>
                        <button type="button" class="action-btn" id="btn-undo"><?= $icon($ICON_UNDO, 20, 2) ?><span data-i18n="undo">Undo</span></button>
                        <button type="button" class="action-btn primary" id="btn-new-game"><?= $icon('<path d="M21 12a9 9 0 11-9-9c2.5 0 4.9 1 6.7 2.7L21 8"/><path d="M21 3v5h-5"/>', 20, 2) ?><span data-i18n="new_game">New game</span></button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Language (phones) -->
    <div id="lang-overlay" class="modal hidden">
        <div class="modal-content lang-sheet">
            <h3 data-i18n="language_title">Language</h3>
            <div class="lang-options" role="listbox">
                <?php foreach (['en' => 'English', 'it' => 'Italiano', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch'] as $code => $label): ?>
                <button type="button" class="lang-option" data-lang="<?= $code ?>" lang="<?= $code ?>"><span class="lang-code"><?= strtoupper($code) ?></span><?= $label ?><span class="lang-check"><?= $ICON_CHECK ?></span></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- How to play (phones and tablets) -->
    <div id="howto-overlay" class="modal hidden">
        <div class="modal-content howto">
            <h3 data-i18n="howto_title">How to play</h3>
            <div id="howto-sheet-body"></div>
            <button type="button" class="btn btn-primary" id="btn-close-howto" data-i18n="got_it">Got it!</button>
            <button type="button" class="btn btn-ghost rules-sheet-btn" data-open-rules><span data-i18n="rules_title">Game rules</span></button>
        </div>
    </div>

    <!-- Game rules -->
    <div id="rules-overlay" class="modal hidden">
        <div class="modal-content rules-content">
            <button type="button" class="zoom-close" id="btn-close-rules" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="rules_title">Game rules</h3>
            <div class="rules-body" id="rules-body"></div>
        </div>
    </div>

    <!-- Release notes -->
    <div id="release-overlay" class="modal hidden">
        <div class="modal-content release-content">
            <button type="button" class="zoom-close" id="btn-close-release" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="release_notes">Release notes</h3>
            <div class="release-list" id="release-list"></div>
        </div>
    </div>

    <!-- Confirmation -->
    <div id="confirm-overlay" class="modal hidden">
        <div class="modal-content">
            <h3 id="confirm-title"></h3>
            <p class="muted" id="confirm-text"></p>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="btn-confirm-no" data-i18n="cancel">Cancel</button>
                <button type="button" class="btn btn-accent" id="btn-confirm-yes"></button>
            </div>
        </div>
    </div>

    <!-- Game over -->
    <div id="gameover-overlay" class="modal hidden">
        <div class="modal-content gameover-content">
            <div id="gameover-icon"></div>
            <h2 id="gameover-title"></h2>
            <p class="muted" id="gameover-text"></p>
            <p class="score-line" id="score-line"></p>
            <div class="gameover-actions">
                <button type="button" class="btn btn-primary btn-block" id="btn-new-game-over" data-i18n="new_game">New game</button>
                <button type="button" class="btn btn-ghost btn-block" id="btn-gameover-home" data-i18n="back_home">Back to start</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
</div>

<script src="i18n.js?v=<?= $v ?>" defer></script>
<script src="releases.js?v=<?= $v ?>" defer></script>
<script src="rules.js?v=<?= $v ?>" defer></script>
<script src="game.js?v=<?= $v ?>" defer></script>
</body>
</html>
