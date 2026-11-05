<?php
$description = 'Free online Connect Four (Forza Quattro): play a friend with a room code, a random opponent, on the same device or against the computer. Customise disc colors, clock and chat. No sign-up.';
$v = 2;
$icon = fn($d, $size = 18, $sw = 2.2) => '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="' . $sw . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
$ICON_CLOSE = $icon('<path d="M6 6l12 12M18 6L6 18"/>', 20, 2.4);
$ICON_FLAG = '<path d="M4 22V4M4 4h13l-2 4 2 4H4"/>';
$ICON_HANDSHAKE = '<path d="M11 17l2 2a1.4 1.4 0 002-2M14 14l2.5 2.5a1.4 1.4 0 002-2l-3.9-3.9a3 3 0 00-4.2 0l-.9.9a1.4 1.4 0 01-2-2l2.8-2.8a5 5 0 016 0l.3.3a2 2 0 001.4.6H21M3 13h2l5 5a1.4 1.4 0 002-2M3 4h8"/>';
$ICON_UNDO = '<path d="M9 14L4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 010 11H11"/>';
$ICON_WAND = '<path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8L19 13M17.8 6.2L19 5M3 21l9-9M12.2 6.2L11 5"/>';
$ICON_PALETTE = '<circle cx="13.5" cy="6.5" r="1.3"/><circle cx="17.5" cy="10.5" r="1.3"/><circle cx="8.5" cy="7.5" r="1.3"/><circle cx="6.5" cy="12.5" r="1.3"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.6-.7 1.6-1.7 0-.4-.2-.8-.4-1.1-.3-.3-.4-.7-.4-1.1 0-.9.8-1.7 1.7-1.7H16c3.1 0 5.6-2.5 5.6-5.6C21.6 6 17.3 2 12 2z"/>';
$ICON_BOOK = '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>';
$ICON_LEAVE = '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>';
$ICON_CHAT = '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>';
$ICON_SOUND = '<path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/>';
$ICON_CHECK = $icon('<path d="M5 12l5 5L20 7"/>', 18, 3);

$timeChips = function ($name, $default) {
    $out = '<div class="chips" role="radiogroup">';
    foreach ([0, 3, 5, 10] as $m) {
        $label = $m === 0 ? '<span data-i18n="time_none">No clock</span>' : $m . ' min';
        $out .= '<label class="chip"><input type="radio" name="' . $name . '" value="' . $m . '"' . ($m === $default ? ' checked' : '') . '><span>' . $label . '</span></label>';
    }
    return $out . '</div>';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <title>Connect Four (Forza Quattro) – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/connect-four/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Connect Four (Forza Quattro) – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/connect-four/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/connect-four/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Connect Four (Forza Quattro) – free online game with custom disc colors and chat, by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Connect Four (Forza Quattro) – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/connect-four/og-image.png">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": [
            "VideoGame",
            "WebApplication"
        ],
        "name": "Connect Four",
        "alternateName": "Forza Quattro",
        "url": "https://www.saveriomorelli.com/projects/connect-four/",
        "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "applicationCategory": "GameApplication",
        "genre": "Board game",
        "operatingSystem": "Any (web browser)",
        "playMode": [
            "MultiPlayer",
            "SinglePlayer"
        ],
        "inLanguage": [
            "en",
            "it",
            "es",
            "fr",
            "de"
        ],
        "isAccessibleForFree": true,
        "image": "https://www.saveriomorelli.com/projects/connect-four/og-image.png",
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
    <meta name="theme-color" content="#12061A">
    <meta name="color-scheme" content="dark">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Connect Four">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= $v ?>">
</head>
<body data-board="plum">

<div id="app">
    <!-- Lobby Screen -->
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
                    <div class="logo-faces" id="logo-faces" aria-hidden="true"></div>
                    <h1 class="logo-title">
                        <img class="logo-mark" src="icons/favicon.svg" alt="" width="44" height="44">
                        <span data-i18n="app_title">Connect Four</span>
                    </h1>
                    <p class="logo-subtitle" data-i18n="lobby_subtitle">The classic four-in-a-row game, online or on your device.</p>
                    <div class="hero-links">
                        <button type="button" class="link-btn howto-link" id="btn-howto">
                            <?= $icon('<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>', 16) ?>
                            <span data-i18n="howto_title">How to play</span>
                        </button>
                        <button type="button" class="link-btn howto-link" data-open-rules>
                            <?= $icon($ICON_BOOK, 16) ?>
                            <span data-i18n="rules_title">Game rules</span>
                        </button>
                        <button type="button" class="link-btn howto-link" data-open-style>
                            <?= $icon($ICON_PALETTE, 16) ?>
                            <span data-i18n="appearance">Customise</span>
                        </button>
                    </div>
                </div>

                <div class="howto howto-inline">
                    <h2 data-i18n="howto_title">How to play</h2>
                    <ol class="howto-list" id="howto-list">
                        <li><span class="howto-num">1</span><div><strong data-i18n="howto1_title">Pick how to play</strong><p data-i18n="howto1_text">A friend online with a room code, a random opponent, on the same device or against the computer.</p></div></li>
                        <li><span class="howto-num">2</span><div><strong data-i18n="howto2_title">Drop your discs</strong><p data-i18n="howto2_text">Take turns dropping one disc into any of the 7 columns. Gravity pulls it down to the lowest free slot.</p></div></li>
                        <li><span class="howto-num">3</span><div><strong data-i18n="howto3_title">Connect 4 to win</strong><p data-i18n="howto3_text">Be the first to connect four of your discs horizontally, vertically, or diagonally.</p></div></li>
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
                        <span class="step-btn"><span class="step-num"><span class="step-n">2</span></span><span class="step-label" data-i18n="step_label_game">Game</span></span>
                    </li>
                </ol>

                <form id="step-name" class="lobby-step active" autocomplete="off">
                    <label class="step-title" for="player-name" data-i18n="step1_title">What's your name?</label>
                    <p class="step-hint" data-i18n="step1_hint">Your opponent will see it during the game.</p>
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
                    <p class="step-hint" data-i18n="step2_hint">How do you want to play?</p>

                    <div class="mode-tabs" role="tablist">
                        <button type="button" class="mode-tab active" role="tab" data-tab="online">
                            <?= $icon('<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>', 18, 2) ?>
                            <span data-i18n="tab_online">Online</span>
                        </button>
                        <button type="button" class="mode-tab" role="tab" data-tab="local">
                            <?= $icon('<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>', 18, 2) ?>
                            <span data-i18n="tab_local">Same device</span>
                        </button>
                        <button type="button" class="mode-tab" role="tab" data-tab="ai">
                            <?= $icon('<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 8V4M9 14h.01M15 14h.01M2 13v3M22 13v3"/><circle cx="12" cy="3" r="1"/>', 18, 2) ?>
                            <span data-i18n="tab_ai">Computer</span>
                        </button>
                    </div>

                    <!-- Online Mode Panel -->
                    <div class="mode-panel active" data-panel="online">
                        <div class="mode-options">
                            <div class="mode-card" id="card-create">
                                <div class="mode-icon"><?= $icon('<path d="M12 5v14M5 12h14"/>', 22) ?></div>
                                <div class="mode-body">
                                    <h3 data-i18n="create_title">Play a friend</h3>
                                    <p data-i18n="create_desc">We'll give you a code to send to your friend.</p>
                                </div>
                                <div class="field">
                                    <span class="field-label" data-i18n="time_label">Time per player</span>
                                    <?= $timeChips('time-online', 5) ?>
                                </div>
                                <button type="button" class="btn btn-primary btn-block" id="btn-create" data-i18n="create_room">Create game</button>
                            </div>

                            <form class="mode-card" id="card-join" autocomplete="off">
                                <div class="mode-icon alt"><?= $icon('<circle cx="8" cy="15" r="4"/><path d="M10.8 12.2L20 3M16 7l3 3M14 9l2 2"/>', 22) ?></div>
                                <div class="mode-body">
                                    <h3 data-i18n="join_title">I have a code</h3>
                                    <p data-i18n="join_desc">Enter the 6-character code your friend sent you.</p>
                                </div>
                                <input type="text" id="room-code" maxlength="6" placeholder="ABC123" class="code-input" autocapitalize="characters" spellcheck="false" aria-label="Room code">
                                <button type="submit" class="btn btn-secondary btn-block" id="btn-join" disabled data-i18n="join_room">Join game</button>
                            </form>

                            <div class="mode-card mode-card-wide" id="card-random">
                                <div class="mode-icon random"><?= $icon('<path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/>', 22) ?></div>
                                <div class="mode-body">
                                    <h3 data-i18n="random_title">Random opponent</h3>
                                    <p id="random-desc" data-i18n="random_desc">Play someone who speaks your language. 5 minutes per player.</p>
                                </div>
                                <button type="button" class="btn btn-secondary" id="btn-random" data-i18n="random_btn">Find a player</button>
                            </div>
                        </div>
                    </div>

                    <!-- Local Same Device Panel -->
                    <div class="mode-panel" data-panel="local">
                        <div class="mode-card solo-card">
                            <div class="mode-icon alt"><?= $icon('<circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 20c0-3.3 2.7-6 6-6s6 2.7 6 6M12 20c0-3.3 1.8-6 4-6 3.3 0 6 2.7 6 6"/>', 22) ?></div>
                            <div class="mode-body">
                                <h3 data-i18n="local_title">Two players, one device</h3>
                                <p data-i18n="local_desc">Take turns on this screen.</p>
                            </div>
                            <label class="field">
                                <span class="field-label" data-i18n="opponent_name">Opponent's name</span>
                                <input type="text" id="local-opponent" maxlength="30" data-i18n-placeholder="opponent_placeholder" placeholder="e.g. Sam">
                            </label>
                            <div class="field">
                                <span class="field-label" data-i18n="time_label">Time per player</span>
                                <?= $timeChips('time-local', 0) ?>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" id="btn-start-local" data-i18n="start_game">Start game</button>
                        </div>
                    </div>

                    <!-- Computer AI Panel -->
                    <div class="mode-panel" data-panel="ai">
                        <div class="mode-card solo-card">
                            <div class="mode-icon random"><?= $icon('<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 8V4M9 14h.01M15 14h.01M2 13v3M22 13v3"/><circle cx="12" cy="3" r="1"/>', 22) ?></div>
                            <div class="mode-body">
                                <h3 data-i18n="ai_title">Play the computer</h3>
                                <p data-i18n="ai_desc">Practise at your own pace. You can take back moves and ask for a hint.</p>
                            </div>
                            <div class="field">
                                <span class="field-label" data-i18n="level_label">Level</span>
                                <div class="chips" role="radiogroup">
                                    <label class="chip"><input type="radio" name="ai-level" value="easy"><span data-i18n="level_easy">Easy</span></label>
                                    <label class="chip"><input type="radio" name="ai-level" value="medium" checked><span data-i18n="level_medium">Medium</span></label>
                                    <label class="chip"><input type="radio" name="ai-level" value="hard"><span data-i18n="level_hard">Hard</span></label>
                                </div>
                            </div>
                            <div class="field">
                                <span class="field-label" data-i18n="color_label">You play as</span>
                                <div class="chips" role="radiogroup">
                                    <label class="chip"><input type="radio" name="ai-color" value="1" checked><span><i class="chip-dot p1"></i><span data-i18n="player_disc_1">First player</span></span></label>
                                    <label class="chip"><input type="radio" name="ai-color" value="2"><span><i class="chip-dot p2"></i><span data-i18n="player_disc_2">Second player</span></span></label>
                                    <label class="chip"><input type="radio" name="ai-color" value="r"><span><i class="chip-dot r"></i><span data-i18n="random_color">Random</span></span></label>
                                </div>
                            </div>
                            <div class="field">
                                <span class="field-label" data-i18n="time_label">Time per player</span>
                                <?= $timeChips('time-ai', 0) ?>
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
                <span class="credit-ai"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.2 6.6 6.8 2.4-6.8 2.4L12 20.5l-2.2-6.6L3 11.5l6.8-2.4z"/></svg><span data-i18n="credit_ai">Built with Claude AI</span></span>
            </div>
            <button type="button" class="version-btn" id="btn-version" data-i18n-title="release_notes">
                <span id="app-version"></span>
                <?= $icon('<path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/>', 13, 2.4) ?>
            </button>
        </footer>
    </div>

    <!-- Waiting Screen -->
    <div id="screen-waiting" class="screen">
        <div class="waiting-container">
            <div class="brand-mark"><img src="icons/favicon.svg" alt="" width="28" height="28"><span data-i18n="app_title">Connect Four</span></div>
            <div class="waiting-animation">
                <div class="pulse-ring"></div>
                <div class="pulse-ring delay-1"></div>
                <div class="pulse-ring delay-2"></div>
                <div class="waiting-dots"><span></span><span></span><span></span></div>
            </div>
            <h2 id="waiting-title"></h2>
            <p class="muted" id="waiting-public-desc"></p>
            <div id="waiting-private">
                <p class="muted" data-i18n="share_code">Send this code to your friend:</p>
                <button type="button" class="room-code-display" id="btn-copy-code">
                    <span id="display-code"></span>
                    <?= $icon('<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>', 18, 2) ?>
                </button>
            </div>
            <div class="waiting-actions">
                <button type="button" class="btn btn-secondary" id="btn-share-link">
                    <?= $icon('<path d="M10 13a5 5 0 007.5.5l3-3a5 5 0 00-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 00-7.5-.5l-3 3a5 5 0 007 7l1.7-1.7"/>', 18, 2) ?>
                    <span data-i18n="share_link">Copy invite link</span>
                </button>
                <button type="button" class="btn btn-ghost" id="btn-leave-waiting" data-i18n="leave">Leave</button>
            </div>
        </div>
    </div>

    <!-- Game Screen -->
    <div id="screen-game" class="screen">
        <header class="game-header">
            <div class="game-header-left">
                <a class="game-title" href="./">
                    <img src="icons/favicon.svg" alt="" width="28" height="28">
                    <span data-i18n="app_title">Connect Four</span>
                </a>
                <div class="turn-indicator" id="turn-indicator"></div>
            </div>
            <div class="game-header-right">
                <span class="mode-badge" id="mode-badge"></span>
                <button type="button" class="btn-icon" id="btn-audio-toggle" data-i18n-title="sound"><?= $icon($ICON_SOUND, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-style data-i18n-title="appearance"><?= $icon($ICON_PALETTE, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-rules data-i18n-title="rules_title"><?= $icon($ICON_BOOK, 20, 2) ?></button>
                <button type="button" class="btn-icon" id="btn-leave-game" data-i18n-title="leave"><?= $icon($ICON_LEAVE, 20, 2) ?></button>
            </div>
        </header>

        <div class="game-body">
            <div class="board-area" id="board-area">
                <div id="banner" class="banner hidden" role="status"></div>
                <div class="board-column">
                    <!-- Top Player Bar -->
                    <div class="player-bar" id="bar-top">
                        <div class="player-bar-info">
                            <div class="player-avatar" id="avatar-top"></div>
                            <div class="player-name-wrap">
                                <span class="player-name" id="name-top"></span>
                                <span class="player-sub"><span class="online-dot" id="dot-online-top"></span> <span id="score-top">0 pt</span></span>
                            </div>
                        </div>
                        <div class="player-clock hidden" id="clock-top">0:00</div>
                    </div>

                    <!-- Connect Four Board -->
                    <div class="board-wrap" id="board-wrap">
                        <div class="column-guides" id="column-guides"></div>
                        <div class="c4-board" id="c4-board"></div>
                        <svg class="c4-winning-line" id="c4-winning-line" aria-hidden="true"></svg>
                    </div>

                    <!-- Bottom Player Bar -->
                    <div class="player-bar" id="bar-bottom">
                        <div class="player-bar-info">
                            <div class="player-avatar" id="avatar-bottom"></div>
                            <div class="player-name-wrap">
                                <span class="player-name" id="name-bottom"></span>
                                <span class="player-sub"><span class="online-dot" id="dot-online-bottom"></span> <span id="score-bottom">0 pt</span></span>
                            </div>
                        </div>
                        <div class="player-clock hidden" id="clock-bottom">0:00</div>
                    </div>
                </div>
            </div>

            <!-- Mobile Chat Backdrop -->
            <div class="chat-backdrop" id="chat-backdrop"></div>

            <!-- Side Panel (Moves & Chat) -->
            <aside class="side-panel" id="side-panel">
                <div class="side-inner">
                    <div class="moves-wrap">
                        <h3 class="side-title" data-i18n="moves_title">Moves</h3>
                        <ol class="move-list" id="move-list"></ol>
                    </div>

                    <section class="chat hidden" id="chat">
                        <div class="chat-head">
                            <h3 class="side-title" data-i18n="chat_title">Chat</h3>
                            <button type="button" class="btn-icon chat-close" id="btn-chat-close" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
                        </div>
                        <ol class="chat-list" id="chat-list"></ol>
                        <form class="chat-form" id="chat-form" autocomplete="off">
                            <input type="text" id="chat-input" maxlength="200" data-i18n-placeholder="chat_placeholder" placeholder="Write a message…">
                            <button type="submit" class="chat-send" data-i18n-title="chat_send"><?= $icon('<path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>', 16, 2) ?></button>
                        </form>
                    </section>

                    <div class="game-actions" id="game-actions">
                        <button type="button" class="action-btn" id="btn-hint"><?= $icon($ICON_WAND, 18, 2) ?><span data-i18n="hint">Hint</span></button>
                        <button type="button" class="action-btn" id="btn-undo"><?= $icon($ICON_UNDO, 18, 2) ?><span data-i18n="undo">Undo</span></button>
                        <button type="button" class="action-btn hidden" id="btn-chat-toggle"><?= $icon($ICON_CHAT, 18, 2) ?><span data-i18n="chat_title">Chat</span></button>
                        <button type="button" class="action-btn" id="btn-draw"><?= $icon($ICON_HANDSHAKE, 18, 2) ?><span data-i18n="draw">Draw</span></button>
                        <button type="button" class="action-btn danger" id="btn-resign"><?= $icon($ICON_FLAG, 18, 2) ?><span data-i18n="resign">Resign</span></button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Mobile Language Modal -->
    <div id="lang-overlay" class="modal hidden">
        <div class="modal-content lang-sheet">
            <button type="button" class="zoom-close" id="btn-close-lang" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="language_title">Language</h3>
            <div class="lang-options" role="listbox">
                <?php foreach (['en' => 'English', 'it' => 'Italiano', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch'] as $code => $label): ?>
                <button type="button" class="lang-option" data-lang="<?= $code ?>" lang="<?= $code ?>"><span class="lang-code"><?= strtoupper($code) ?></span><?= $label ?><span class="lang-check"><?= $ICON_CHECK ?></span></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- How to play Modal -->
    <div id="howto-overlay" class="modal hidden">
        <div class="modal-content howto">
            <button type="button" class="zoom-close" id="btn-close-howto-x" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="howto_title">How to play</h3>
            <div id="howto-sheet-body"></div>
            <button type="button" class="btn btn-primary btn-block" style="margin-top:16px;" id="btn-close-howto" data-i18n="got_it">Got it!</button>
        </div>
    </div>

    <!-- Rules Modal -->
    <div id="rules-overlay" class="modal hidden">
        <div class="modal-content rules-content">
            <button type="button" class="zoom-close" id="btn-close-rules" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="rules_title">Game rules</h3>
            <div class="rules-body" id="rules-body"></div>
        </div>
    </div>

    <!-- Customise Modal -->
    <div id="style-overlay" class="modal hidden">
        <div class="modal-content style-content">
            <button type="button" class="zoom-close" id="btn-close-style" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="custom_title">Customise</h3>

            <!-- Live 4x3 Mini Preview -->
            <div class="custom-preview-wrap">
                <div class="preview-board" id="custom-preview-board">
                    <!-- 12 cells generated by renderCustomiseModal -->
                </div>
            </div>

            <!-- Curated Palettes -->
            <div class="custom-section">
                <h4 class="custom-section-title" data-i18n="custom_presets">Curated Palettes</h4>
                <div class="palette-grid" id="palette-grid"></div>
            </div>

            <!-- Disc Finish -->
            <div class="custom-section">
                <h4 class="custom-section-title" data-i18n="custom_style">Disc Finish</h4>
                <div class="seg" role="radiogroup">
                    <button type="button" role="radio" data-disc-style="glossy" data-i18n="style_glossy">Glossy 3D</button>
                    <button type="button" role="radio" data-disc-style="flat" data-i18n="style_flat">Flat Modern</button>
                    <button type="button" role="radio" data-disc-style="ring" data-i18n="style_ring">Ring Inset</button>
                    <button type="button" role="radio" data-disc-style="star" data-i18n="style_star">Star Emblem</button>
                </div>
            </div>

            <!-- Board Hue -->
            <div class="custom-section">
                <h4 class="custom-section-title" data-i18n="custom_board">Board Hue</h4>
                <div class="seg" role="radiogroup">
                    <button type="button" role="radio" data-board-hue="plum" data-i18n="board_plum">Deep Plum</button>
                    <button type="button" role="radio" data-board-hue="lilac" data-i18n="board_lilac">Royal Lilac</button>
                    <button type="button" role="radio" data-board-hue="navy" data-i18n="board_navy">Midnight Navy</button>
                    <button type="button" role="radio" data-board-hue="slate" data-i18n="board_slate">Dark Slate</button>
                </div>
            </div>

            <!-- Options & Sound -->
            <div class="custom-section">
                <h4 class="custom-section-title" data-i18n="custom_options">Game Settings</h4>
                <div class="option-list">
                    <label class="option-row">
                        <span data-i18n="opt_sound">Disc drop sounds</span>
                        <input type="checkbox" class="switch" id="opt-sound" checked>
                    </label>
                    <label class="option-row">
                        <span data-i18n="opt_guides">Highlight column on hover</span>
                        <input type="checkbox" class="switch" id="opt-guides" checked>
                    </label>
                    <div class="option-row">
                        <span data-i18n="opt_anim">Drop animation speed</span>
                        <div class="seg" role="radiogroup">
                            <button type="button" role="radio" data-anim-speed="normal" data-i18n="anim_normal">Normal</button>
                            <button type="button" role="radio" data-anim-speed="fast" data-i18n="anim_fast">Fast</button>
                            <button type="button" role="radio" data-anim-speed="instant" data-i18n="anim_instant">Instant</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Release notes Modal -->
    <div id="release-overlay" class="modal hidden">
        <div class="modal-content release-content">
            <button type="button" class="zoom-close" id="btn-close-release" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="release_notes">Release notes</h3>
            <div class="release-list" id="release-list"></div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-overlay" class="modal hidden">
        <div class="modal-content">
            <h3 id="confirm-title"></h3>
            <p class="muted" id="confirm-text"></p>
            <div class="modal-actions" style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" class="btn btn-ghost" id="btn-confirm-no" data-i18n="cancel">Cancel</button>
                <button type="button" class="btn btn-accent" id="btn-confirm-yes"></button>
            </div>
        </div>
    </div>

    <!-- Game over Modal -->
    <div id="gameover-overlay" class="modal hidden">
        <div class="modal-content gameover-content">
            <div id="gameover-icon" class="gameover-icon"></div>
            <h2 id="gameover-title"></h2>
            <p class="muted" id="gameover-text"></p>
            <p class="score-line" id="score-line"></p>
            <p class="rematch-status" id="rematch-status" style="font-size:13px; color:var(--text-muted);"></p>
            <div class="gameover-actions">
                <button type="button" class="btn btn-primary btn-block" id="btn-rematch" data-i18n="rematch">Rematch</button>
                <button type="button" class="btn btn-secondary btn-block" id="btn-review" data-i18n="review_board">Look at the board</button>
                <button type="button" class="btn btn-ghost btn-block" id="btn-gameover-home" data-i18n="back_home">Back to start</button>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div id="toast" class="toast" role="status" aria-live="polite"></div>
</div>

<script src="i18n.js?v=<?= $v ?>"></script>
<script src="engine.js?v=<?= $v ?>"></script>
<script src="ai.js?v=<?= $v ?>"></script>
<script src="rules.js?v=<?= $v ?>"></script>
<script src="releases.js?v=<?= $v ?>"></script>
<script src="game.js?v=<?= $v ?>"></script>
</body>
</html>
