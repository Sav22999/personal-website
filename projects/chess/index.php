<?php
$description = 'Free online chess: play a friend with a room code, a random opponent, on the same device or against the computer. Tips for every piece, clock and chat. No sign-up.';
$v = 21;
$icon = fn($d, $size = 18, $sw = 2.2) => '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="' . $sw . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
$ICON_CLOSE = $icon('<path d="M6 6l12 12M18 6L6 18"/>', 20, 2.4);
$ICON_BULB = '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0012 2z"/>';
$ICON_WAND = '<path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8L19 13M17.8 6.2L19 5M3 21l9-9M12.2 6.2L11 5"/>';
$ICON_FLAG = '<path d="M4 22V4M4 4h13l-2 4 2 4H4"/>';
$ICON_HANDSHAKE = '<path d="M11 17l2 2a1.4 1.4 0 002-2M14 14l2.5 2.5a1.4 1.4 0 002-2l-3.9-3.9a3 3 0 00-4.2 0l-.9.9a1.4 1.4 0 01-2-2l2.8-2.8a5 5 0 016 0l.3.3a2 2 0 001.4.6H21M3 13h2l5 5a1.4 1.4 0 002-2M3 4h8"/>';
$ICON_UNDO = '<path d="M9 14L4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 010 11H11"/>';
$ICON_FLIP = '<path d="M7 16V4M3 8l4-4 4 4M17 8v12M21 16l-4 4-4-4"/>';
$ICON_PALETTE = '<circle cx="13.5" cy="6.5" r="1.3"/><circle cx="17.5" cy="10.5" r="1.3"/><circle cx="8.5" cy="7.5" r="1.3"/><circle cx="6.5" cy="12.5" r="1.3"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.6-.7 1.6-1.7 0-.4-.2-.8-.4-1.1-.3-.3-.4-.7-.4-1.1 0-.9.8-1.7 1.7-1.7H16c3.1 0 5.6-2.5 5.6-5.6C21.6 6 17.3 2 12 2z"/>';
$ICON_CHAT = '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>';
$ICON_BOOK = '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>';
$ICON_LEAVE = '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>';
$ICON_CHECK = $icon('<path d="M5 12l5 5L20 7"/>', 18, 3);
$timeChips = function ($name, $default) {
    $out = '<div class="chips" role="radiogroup">';
    foreach ([0, 5, 10, 15] as $m) {
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
    <title>Chess – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/chess/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Chess – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/chess/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/chess/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Chess – free online chess with tips, clock and chat, by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Chess – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/chess/og-image.png">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": [
            "VideoGame",
            "WebApplication"
        ],
        "name": "Chess",
        "url": "https://www.saveriomorelli.com/projects/chess/",
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
        "image": "https://www.saveriomorelli.com/projects/chess/og-image.png",
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
    <meta name="apple-mobile-web-app-title" content="Chess">
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
                    <div class="logo-faces" id="logo-faces" aria-hidden="true"></div>
                    <h1 class="logo-title"><img class="logo-mark" src="icons/favicon.svg" alt="" width="44" height="44"><span>Chess</span></h1>
                    <p class="logo-subtitle" data-i18n="lobby_subtitle">Classic chess, with a little help when you want it.</p>
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
                            <span data-i18n="appearance">Pieces &amp; board</span>
                        </button>
                    </div>
                </div>
                <div class="howto howto-inline">
                    <h2 data-i18n="howto_title">How to play</h2>
                    <ol class="howto-list" id="howto-list">
                        <li><span class="howto-num">1</span><div><strong data-i18n="howto1_title">Pick how to play</strong><p data-i18n="howto1_text">A friend online with a code, a random opponent, two players on one device, or the computer.</p></div></li>
                        <li><span class="howto-num">2</span><div><strong data-i18n="howto2_title">Move your pieces</strong><p data-i18n="howto2_text">Tap a piece, then tap where it goes — or drag it. Only legal moves are allowed.</p></div></li>
                        <li><span class="howto-num">3</span><div><strong data-i18n="howto3_title">Use the tips</strong><p data-i18n="howto3_text">Turn on Tips to see how each piece moves, what it can capture and which moves give check.</p></div></li>
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
                                    <?= $timeChips('time-online', 10) ?>
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
                                    <p id="random-desc"></p>
                                </div>
                                <button type="button" class="btn btn-secondary" id="btn-random" data-i18n="random_btn">Find a player</button>
                            </div>
                        </div>
                    </div>

                    <div class="mode-panel" data-panel="local">
                        <div class="mode-card solo-card">
                            <div class="mode-icon alt"><?= $icon('<circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 20c0-3.3 2.7-6 6-6s6 2.7 6 6M12 20c0-3.3 1.8-6 4-6 3.3 0 6 2.7 6 6"/>', 22) ?></div>
                            <div class="mode-body">
                                <h3 data-i18n="local_title">Two players, one device</h3>
                                <p data-i18n="local_desc">Take turns on this screen. The board can turn around after every move.</p>
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
                                <span class="field-label" data-i18n="color_label">You play</span>
                                <div class="chips" role="radiogroup">
                                    <label class="chip"><input type="radio" name="ai-color" value="w" checked><span><i class="chip-dot w"></i><span data-i18n="white">White</span></span></label>
                                    <label class="chip"><input type="radio" name="ai-color" value="b"><span><i class="chip-dot b"></i><span data-i18n="black">Black</span></span></label>
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

    <!-- Waiting -->
    <div id="screen-waiting" class="screen">
        <div class="waiting-container">
            <div class="brand-mark"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>Chess</span></div>
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
                <p class="mode-note" id="waiting-time-note"></p>
            </div>
            <div class="waiting-actions">
                <button type="button" class="btn btn-primary hidden" id="btn-native-share">
                    <?= $icon('<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>', 18, 2) ?>
                    <span data-i18n="share_invite">Share invite</span>
                </button>
                <button type="button" class="btn btn-secondary" id="btn-share-link">
                    <?= $icon('<path d="M10 13a5 5 0 007.5.5l3-3a5 5 0 00-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 00-7.5-.5l-3 3a5 5 0 007 7l1.7-1.7"/>', 18, 2) ?>
                    <span data-i18n="share_link">Copy invite link</span>
                </button>
                <button type="button" class="btn btn-ghost" id="btn-leave-waiting" data-i18n="leave">Leave</button>
            </div>
        </div>
    </div>

    <!-- Game -->
    <div id="screen-game" class="screen">
        <header class="game-header">
            <div class="game-header-left">
                <h2 class="game-title"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>Chess</span></h2>
                <div class="turn-indicator" id="turn-indicator"></div>
            </div>
            <div class="game-header-right">
                <span class="mode-badge" id="mode-badge"></span>
                <button type="button" class="btn-icon" data-open-style data-i18n-title="appearance"><?= $icon($ICON_PALETTE, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-rules data-i18n-title="rules_title"><?= $icon($ICON_BOOK, 20, 2) ?></button>
                <button type="button" class="btn-icon" id="btn-leave-game" data-i18n-title="leave"><?= $icon($ICON_LEAVE, 20, 2) ?></button>
            </div>
        </header>

        <div class="game-body">
            <div class="board-area" id="board-area">
                <div id="banner" class="banner hidden" role="status"></div>
                <div class="board-column" id="board-column">
                    <div class="player-bar" id="bar-top"></div>
                    <div class="board-wrap" id="board-wrap">
                        <div class="board" id="board"></div>
                        <svg class="board-arrows" id="board-arrows" viewBox="0 0 800 800" aria-hidden="true"></svg>
                        <div class="promo-picker hidden" id="promo-picker"></div>
                    </div>
                    <div class="player-bar" id="bar-bottom"></div>
                </div>
            </div>

            <div class="chat-backdrop" id="chat-backdrop"></div>
            <aside class="side-panel" id="side-panel">
                <div class="side-inner">
                    <div class="tip-card" id="tip-card"></div>
                    <div class="moves-wrap">
                        <h3 class="side-title" data-i18n="moves_title">Moves</h3>
                        <ol class="move-list" id="move-list"></ol>
                    </div>
                    <section class="chat hidden" id="chat" aria-labelledby="chat-title">
                        <div class="chat-head">
                            <h3 class="side-title chat-title" id="chat-title" data-i18n="chat_title">Chat</h3>
                            <button type="button" class="btn-icon chat-close" id="btn-chat-close" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
                        </div>
                        <ol class="chat-list" id="chat-list" aria-live="polite"></ol>
                        <form class="chat-form" id="chat-form" autocomplete="off">
                            <input type="text" id="chat-input" maxlength="200" enterkeyhint="send" data-i18n-placeholder="chat_placeholder" placeholder="Write a message…">
                            <button type="submit" class="chat-send" id="btn-chat-send" data-i18n-title="chat_send"><?= $icon('<path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>', 18, 2.2) ?></button>
                        </form>
                    </section>
                    <div class="game-actions" id="game-actions">
                        <button type="button" class="action-btn tip-toggle" id="btn-tips" aria-pressed="false">
                            <?= $icon($ICON_BULB, 20, 2) ?><span data-i18n="tips">Tips</span>
                        </button>
                        <button type="button" class="action-btn" id="btn-hint"><?= $icon($ICON_WAND, 20, 2) ?><span data-i18n="hint">Hint</span></button>
                        <button type="button" class="action-btn" id="btn-undo"><?= $icon($ICON_UNDO, 20, 2) ?><span data-i18n="undo">Undo</span></button>
                        <button type="button" class="action-btn hidden" id="btn-chat"><?= $icon($ICON_CHAT, 20, 2) ?><span data-i18n="chat_title">Chat</span><span class="chat-badge hidden" id="chat-badge"></span></button>
                        <button type="button" class="action-btn" id="btn-flip"><?= $icon($ICON_FLIP, 20, 2) ?><span data-i18n="flip">Flip</span></button>
                        <button type="button" class="action-btn" id="btn-draw"><?= $icon($ICON_HANDSHAKE, 20, 2) ?><span data-i18n="draw">Draw</span></button>
                        <button type="button" class="action-btn danger" id="btn-resign"><?= $icon($ICON_FLAG, 20, 2) ?><span data-i18n="resign">Resign</span></button>
                        <button type="button" class="action-btn primary hidden" id="btn-new-game"><?= $icon('<path d="M21 12a9 9 0 11-9-9c2.5 0 4.9 1 6.7 2.7L21 8"/><path d="M21 3v5h-5"/>', 20, 2) ?><span data-i18n="new_game">New game</span></button>
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

    <!-- Pieces & board -->
    <div id="style-overlay" class="modal hidden">
        <div class="modal-content style-content">
            <button type="button" class="zoom-close" id="btn-close-style" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="appearance">Pieces &amp; board</h3>
            <div class="custom-layout">
                <div class="custom-main">
                    <h4 class="style-section" data-i18n="pieces_label">Pieces</h4>
                    <div class="style-grid" id="style-grid"></div>
                    <h4 class="style-section" data-i18n="board_label">Board</h4>
                    <div class="board-grid" id="board-grid"></div>
                </div>
                <div class="custom-side">
                    <!-- Wide screens: a live preview of every choice -->
                    <div class="custom-preview" aria-hidden="true"><div class="board" id="custom-preview"></div></div>
                    <h4 class="style-section" data-i18n="options_label">Options</h4>
                    <div class="option-list">
                <label class="option-row"><span data-i18n="opt_coords">Show coordinates</span><input type="checkbox" class="switch" data-opt="coords"></label>
                <label class="option-row"><span data-i18n="opt_last">Highlight the last move</span><input type="checkbox" class="switch" data-opt="lastMove"></label>
                <label class="option-row"><span><span data-i18n="opt_dots">Show possible moves</span><small data-i18n="opt_dots_hint">With Tips on they are always shown.</small></span><input type="checkbox" class="switch" data-opt="dots"></label>
                <label class="option-row"><span data-i18n="opt_queen">Always promote to a queen</span><input type="checkbox" class="switch" data-opt="autoQueen"></label>
                <div class="option-row"><span data-i18n="opt_anim">Animations</span>
                    <div class="seg" role="radiogroup">
                        <button type="button" role="radio" data-anim-opt="normal" data-i18n="anim_normal">Normal</button>
                        <button type="button" role="radio" data-anim-opt="fast" data-i18n="anim_fast">Fast</button>
                        <button type="button" role="radio" data-anim-opt="off" data-i18n="anim_off">Off</button>
                    </div>
                </div>
                    </div>
                </div>
            </div>
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

    <!-- Confirmation (resign, draw, leave) -->
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
            <p class="rematch-status" id="rematch-status"></p>
            <div class="gameover-actions">
                <button type="button" class="btn btn-primary btn-block" id="btn-rematch"></button>
                <button type="button" class="btn btn-secondary btn-block" id="btn-review" data-i18n="review_board">Look at the board</button>
                <button type="button" class="btn btn-ghost btn-block" id="btn-gameover-home" data-i18n="back_home">Back to start</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
</div>

<script src="i18n.js?v=<?= $v ?>"></script>
<script src="engine.js?v=<?= $v ?>"></script>
<script src="ai.js?v=<?= $v ?>"></script>
<script src="pieces.js?v=<?= $v ?>"></script>
<script src="tips.js?v=<?= $v ?>"></script>
<script src="releases.js?v=<?= $v ?>"></script>
<script src="rules.js?v=<?= $v ?>"></script>
<script src="game.js?v=<?= $v ?>"></script>
</body>
</html>
