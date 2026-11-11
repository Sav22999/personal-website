<?php
$description = 'Free online Sea Battle (Battaglia Navale): place your fleet and sink your opponent\'s. Play a friend with a room code, a random opponent, on the same device or against the computer. No sign-up.';
$v = 3;
$icon = fn($d, $size = 18, $sw = 2.2) => '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="' . $sw . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
$ICON_CLOSE = $icon('<path d="M6 6l12 12M18 6L6 18"/>', 20, 2.4);
$ICON_FLAG = '<path d="M4 22V4M4 4h13l-2 4 2 4H4"/>';
$ICON_WAND = '<path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8L19 13M17.8 6.2L19 5M3 21l9-9M12.2 6.2L11 5"/>';
$ICON_SETTINGS = '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 01-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 010-4h.1a1.7 1.7 0 001.5-1.1 1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 014 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 010 4h-.1a1.7 1.7 0 00-1.5 1z"/>';
$ICON_BOOK = '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>';
$ICON_LEAVE = '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>';
$ICON_LIST = '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>';
$ICON_TROPHY = '<path d="M8 4h8v7a4 4 0 01-8 0zM8 6H4a4 4 0 004 5M16 6h4a4 4 0 01-4 5M12 15v4M8 21h8"/>';
$ICON_CHAT = '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>';
$ICON_SOUND = '<path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/>';
$ICON_CHECK = $icon('<path d="M5 12l5 5L20 7"/>', 18, 3);
$ICON_HEART = '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>';
$ICON_ROTATE = '<path d="M21 12a9 9 0 11-3-6.7L21 8"/><path d="M21 3v5h-5"/>';
$ICON_SHUFFLE = '<path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/>';
$ICON_GLOBE = '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>';
$ICON_ROBOT = '<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 8V4M9 14h.01M15 14h.01M2 13v3M22 13v3"/><circle cx="12" cy="3" r="1"/>';
$ICON_PHONE = '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>';
$ICON_ARROW = $icon('<path d="M5 12h14M13 6l6 6-6 6"/>', 18, 2.4);

$timeChips = function ($name, $default) {
    $out = '<div class="chips" role="radiogroup">';
    foreach ([0, 3, 5, 10] as $m) {
        $label = $m === 0 ? '<span data-i18n="time_none">No clock</span>' : $m . ' min';
        $out .= '<label class="chip"><input type="radio" name="' . $name . '" value="' . $m . '"' . ($m === $default ? ' checked' : '') . '><span>' . $label . '</span></label>';
    }
    return $out . '</div>';
};
$bonusSwitch = fn($id) => '<label class="option-row compact"><span><span data-i18n="bonus_label">Hit, shoot again</span><small data-i18n="bonus_hint">A hit gives you another shot</small></span><input type="checkbox" class="switch" id="' . $id . '"></label>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <title>Sea Battle (Battaglia Navale) – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/sea-battle/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Sea Battle (Battaglia Navale) – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/sea-battle/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/sea-battle/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Sea Battle (Battaglia Navale) – free online naval battle game by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sea Battle (Battaglia Navale) – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/sea-battle/og-image.png">
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": ["VideoGame", "WebApplication"],
            "name": "Sea Battle",
            "alternateName": ["Battaglia Navale", "Batalla Naval", "Bataille Navale", "Schiffe versenken"],
            "url": "https://www.saveriomorelli.com/projects/sea-battle/",
            "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            "applicationCategory": "GameApplication",
            "genre": "Board game",
            "operatingSystem": "Any (web browser)",
            "playMode": ["MultiPlayer", "SinglePlayer"],
            "inLanguage": ["en", "it", "es", "fr", "de"],
            "isAccessibleForFree": true,
            "image": "https://www.saveriomorelli.com/projects/sea-battle/og-image.png",
            "author": {"@type": "Person", "name": "Saverio Morelli", "url": "https://www.saveriomorelli.com/"},
            "offers": {"@type": "Offer", "price": "0", "priceCurrency": "EUR"}
        }
    </script>
    <meta name="theme-color" content="#06182A">
    <meta name="color-scheme" content="dark light">
    <script src="theme.js?v=<?= $v ?>"></script>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Sea Battle">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preload" href="fonts/inter-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="style.css?v=<?= $v ?>">
</head>
<body data-v="<?= $v ?>">

<div id="app">
    <!-- Lobby -->
    <div id="screen-lobby" class="screen active">
        <div class="top-tools">
            <div class="theme-selector" id="theme-selector" role="radiogroup" data-i18n-title="theme_title">
                <button type="button" class="theme-tab" data-theme-mode="auto" role="radio" data-i18n="theme_auto">Auto</button>
                <button type="button" class="theme-tab" data-theme-mode="light" role="radio" data-i18n="theme_light">Light</button>
                <button type="button" class="theme-tab" data-theme-mode="dark" role="radio" data-i18n="theme_dark">Dark</button>
            </div>
            <button type="button" class="theme-toggle" id="btn-theme" data-i18n-title="theme_title">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 3a9 9 0 010 18z" fill="currentColor" stroke="none"/>
                </svg>
                <span data-theme-label="auto" data-i18n="theme_auto">Auto</span>
                <span data-theme-label="light" data-i18n="theme_light" hidden>Light</span>
                <span data-theme-label="dark" data-i18n="theme_dark" hidden>Dark</span>
            </button>
            <div class="lang-selector" id="lang-selector">
                <?php foreach (['en', 'it', 'es', 'fr', 'de'] as $code): ?>
                    <button type="button" class="lang-btn" data-lang="<?= $code ?>"><?= strtoupper($code) ?></button>
                <?php endforeach; ?>
            </div>
            <button type="button" class="lang-toggle" id="btn-lang-menu" aria-haspopup="dialog" data-i18n-title="language_title">
                <?= $icon($ICON_GLOBE, 18, 2) ?>
                <span id="lang-current">EN</span>
                <?= $icon('<path d="M6 9l6 6 6-6"/>', 14, 2.6) ?>
            </button>
        </div>

        <div class="lobby-container">
            <section class="hero">
                <div class="radar" aria-hidden="true">
                    <div class="radar-sweep"></div>
                    <span class="radar-blip b1"></span>
                    <span class="radar-blip b2"></span>
                    <span class="radar-blip b3"></span>
                </div>
                <h1 class="logo-title">
                    <img class="logo-mark" src="icons/favicon.svg" alt="" width="40" height="40">
                    <span data-i18n="app_title">Sea Battle</span>
                </h1>
                <p class="logo-subtitle" data-i18n="lobby_subtitle">Hide your fleet, hunt down theirs. Online or on your device.</p>
                <div class="hero-links">
                    <button type="button" class="link-btn" id="btn-howto">
                        <?= $icon('<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>', 16) ?>
                        <span data-i18n="howto_title">How to play</span>
                    </button>
                    <button type="button" class="link-btn" data-open-rules>
                        <?= $icon($ICON_BOOK, 16) ?>
                        <span data-i18n="rules_title">Game rules</span>
                    </button>
                    <button type="button" class="link-btn" data-open-settings>
                        <?= $icon($ICON_SETTINGS, 16) ?>
                        <span data-i18n="settings_title">Settings</span>
                    </button>
                </div>

                <ol class="howto-list" id="howto-list">
                    <li><span class="howto-num">1</span>
                        <div><strong data-i18n="howto1_title">Deploy your fleet</strong>
                            <p data-i18n="howto1_text">Place 5 ships on your grid. They can't touch each other, not even at the corners.</p></div>
                    </li>
                    <li><span class="howto-num">2</span>
                        <div><strong data-i18n="howto2_title">Take turns firing</strong>
                            <p data-i18n="howto2_text">Pick a cell on the enemy grid: it's a splash, a hit, or a ship sunk.</p></div>
                    </li>
                    <li><span class="howto-num">3</span>
                        <div><strong data-i18n="howto3_title">Sink them all</strong>
                            <p data-i18n="howto3_text">The first to sink the whole enemy fleet wins.</p></div>
                    </li>
                </ol>
            </section>

            <section class="lobby-panel">
                <form id="step-name" class="lobby-step active" autocomplete="off">
                    <label class="step-title" for="player-name" data-i18n="step1_title">What's your name, captain?</label>
                    <p class="step-hint" data-i18n="step1_hint">Your opponent will see it during the game.</p>
                    <input type="text" id="player-name" maxlength="30" data-i18n-placeholder="name_placeholder" placeholder="e.g. Alex" required>
                    <button type="submit" class="btn btn-primary btn-block" id="btn-continue" disabled>
                        <span data-i18n="continue">Continue</span>
                        <?= $ICON_ARROW ?>
                    </button>
                </form>

                <div id="step-mode" class="lobby-step">
                    <div class="greeting">
                        <span class="step-title" id="greeting-text"></span>
                        <button type="button" class="link-btn" id="btn-change-name" data-i18n="change_name">Change name</button>
                    </div>

                    <div class="mode-tabs" role="tablist">
                        <button type="button" class="mode-tab active" role="tab" data-tab="online"><?= $icon($ICON_GLOBE, 18, 2) ?><span data-i18n="tab_online">Online</span></button>
                        <button type="button" class="mode-tab" role="tab" data-tab="local"><?= $icon($ICON_PHONE, 18, 2) ?><span data-i18n="tab_local">Same device</span></button>
                        <button type="button" class="mode-tab" role="tab" data-tab="ai"><?= $icon($ICON_ROBOT, 18, 2) ?><span data-i18n="tab_ai">Computer</span></button>
                    </div>

                    <div class="mode-panel active" data-panel="online">
                        <div class="mode-card" id="card-create">
                            <div class="mode-head">
                                <div class="mode-icon"><?= $icon('<path d="M12 5v14M5 12h14"/>', 22) ?></div>
                                <div>
                                    <h3 data-i18n="create_title">Play a friend</h3>
                                    <p data-i18n="create_desc">We'll give you a code to send to your friend.</p>
                                </div>
                            </div>
                            <div class="field">
                                <span class="field-label" data-i18n="time_label">Time per player</span>
                                <?= $timeChips('time-online', 5) ?>
                            </div>
                            <?= $bonusSwitch('bonus-online') ?>
                            <button type="button" class="btn btn-primary btn-block" id="btn-create" data-i18n="create_room">Create game</button>
                        </div>

                        <form class="mode-card" id="card-join" autocomplete="off">
                            <div class="mode-head">
                                <div class="mode-icon alt"><?= $icon('<circle cx="8" cy="15" r="4"/><path d="M10.8 12.2L20 3M16 7l3 3M14 9l2 2"/>', 22) ?></div>
                                <div>
                                    <h3 data-i18n="join_title">I have a code</h3>
                                    <p data-i18n="join_desc">Enter the 6-character code your friend sent you.</p>
                                </div>
                            </div>
                            <input type="text" id="room-code" maxlength="6" placeholder="ABC123" class="code-input" autocapitalize="characters" spellcheck="false" data-i18n-aria="room_code">
                            <button type="submit" class="btn btn-secondary btn-block" id="btn-join" disabled data-i18n="join_room">Join game</button>
                        </form>

                        <div class="mode-card" id="card-random">
                            <div class="mode-head">
                                <div class="mode-icon warm"><?= $icon($ICON_SHUFFLE, 22) ?></div>
                                <div>
                                    <h3 data-i18n="random_title">Random opponent</h3>
                                    <p id="random-desc">Play someone who speaks your language. 5 minutes per player.</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-block" id="btn-random" data-i18n="random_btn">Find a player</button>
                        </div>
                    </div>

                    <div class="mode-panel" data-panel="local">
                        <div class="mode-card">
                            <div class="mode-head">
                                <div class="mode-icon alt"><?= $icon('<circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 20c0-3.3 2.7-6 6-6s6 2.7 6 6M12 20c0-3.3 1.8-6 4-6 3.3 0 6 2.7 6 6"/>', 22) ?></div>
                                <div>
                                    <h3 data-i18n="local_title">Two captains, one device</h3>
                                    <p data-i18n="local_desc">Pass the device between turns: a screen hides each fleet from the other player.</p>
                                </div>
                            </div>
                            <label class="field">
                                <span class="field-label" data-i18n="opponent_name">Opponent's name</span>
                                <input type="text" id="local-opponent" maxlength="30" data-i18n-placeholder="opponent_placeholder" placeholder="e.g. Sam">
                            </label>
                            <?= $bonusSwitch('bonus-local') ?>
                            <button type="button" class="btn btn-primary btn-block" id="btn-start-local" data-i18n="start_game">Start game</button>
                        </div>
                    </div>

                    <div class="mode-panel" data-panel="ai">
                        <div class="mode-card">
                            <div class="mode-head">
                                <div class="mode-icon warm"><?= $icon($ICON_ROBOT, 22) ?></div>
                                <div>
                                    <h3 data-i18n="ai_title">Play the computer</h3>
                                    <p data-i18n="ai_desc">Practise at your own pace, with a hint when you need one.</p>
                                </div>
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
                                <span class="field-label" data-i18n="first_label">Who fires first</span>
                                <div class="chips" role="radiogroup">
                                    <label class="chip"><input type="radio" name="ai-first" value="me" checked><span data-i18n="first_me">Me</span></label>
                                    <label class="chip"><input type="radio" name="ai-first" value="ai"><span data-i18n="computer">Computer</span></label>
                                    <label class="chip"><input type="radio" name="ai-first" value="r"><span data-i18n="random_first">Random</span></label>
                                </div>
                            </div>
                            <?= $bonusSwitch('bonus-ai') ?>
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
            <div class="footer-actions">
                <a class="foot-btn" href="privacy.html">Privacy</a>
                <button type="button" class="foot-btn donate-btn" id="btn-donate"><?= $icon($ICON_HEART, 13, 2.2) ?><span data-i18n="donate">Donate</span></button>
                <button type="button" class="foot-btn" id="btn-version" data-i18n-title="release_notes"><span id="app-version"></span><?= $icon('<path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/>', 13, 2.4) ?></button>
            </div>
        </footer>
    </div>

    <!-- Waiting for the second player -->
    <div id="screen-waiting" class="screen">
        <div class="waiting-container">
            <div class="brand-mark"><img src="icons/favicon.svg" alt="" width="28" height="28"><span data-i18n="app_title">Sea Battle</span></div>
            <div class="radar small" aria-hidden="true">
                <div class="radar-sweep"></div>
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

    <!-- Game: placement and battle -->
    <div id="screen-game" class="screen">
        <header class="game-header">
            <a class="game-title" href="./">
                <img src="icons/favicon.svg" alt="" width="28" height="28">
                <span data-i18n="app_title">Sea Battle</span>
            </a>
            <div class="game-header-right">
                <span class="mode-badge" id="mode-badge"></span>
                <button type="button" class="btn-icon" id="btn-audio-toggle" data-i18n-title="sound"><?= $icon($ICON_SOUND, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-settings data-i18n-title="settings_title"><?= $icon($ICON_SETTINGS, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-rules data-i18n-title="rules_title"><?= $icon($ICON_BOOK, 20, 2) ?></button>
                <button type="button" class="btn-icon" id="btn-leave-game" data-i18n-title="leave"><?= $icon($ICON_LEAVE, 20, 2) ?></button>
            </div>
        </header>

        <div class="scoreboard" id="scoreboard">
            <div class="captain" id="cap-me">
                <span class="captain-dot"></span>
                <div class="captain-info">
                    <span class="captain-name" id="name-me"></span>
                    <span class="captain-sub"><span class="online-dot hidden" id="dot-me"></span><span id="wins-me"></span></span>
                </div>
                <span class="captain-clock hidden" id="clock-me">0:00</span>
            </div>
            <div class="turn-indicator" id="turn-indicator" role="status" aria-live="polite"></div>
            <div class="captain right" id="cap-opp">
                <span class="captain-clock hidden" id="clock-opp">0:00</span>
                <div class="captain-info">
                    <span class="captain-name" id="name-opp"></span>
                    <span class="captain-sub"><span class="online-dot hidden" id="dot-opp"></span><span id="wins-opp"></span></span>
                </div>
                <span class="captain-dot"></span>
            </div>
        </div>


        <div class="game-body">
            <!-- Placement -->
            <section class="placement hidden" id="placement">
                <div class="placement-board">
                    <h2 class="board-title" data-i18n="place_title">Deploy your fleet</h2>
                    <p class="board-hint" id="place-hint" data-i18n="place_hint">Pick a ship, then tap the grid to place it. Tap a placed ship to move it.</p>
                    <div class="grid-wrap">
                        <div class="sea-grid" id="grid-place"></div>
                    </div>
                </div>
                <div class="placement-side">
                    <ul class="dock" id="dock"></ul>
                    <div class="placement-tools">
                        <button type="button" class="btn btn-secondary" id="btn-rotate"><?= $icon($ICON_ROTATE, 18, 2.2) ?><span data-i18n="rotate">Rotate</span></button>
                        <button type="button" class="btn btn-secondary" id="btn-random-fleet"><?= $icon($ICON_SHUFFLE, 18, 2.2) ?><span data-i18n="random_fleet">Random</span></button>
                        <button type="button" class="btn btn-ghost" id="btn-clear-fleet" data-i18n="clear_fleet">Clear</button>
                    </div>
                    <p class="place-timer muted" id="place-timer"></p>
                    <button type="button" class="btn btn-primary btn-block" id="btn-ready" disabled><span data-i18n="ready">Ready to battle</span><?= $ICON_ARROW ?></button>
                    <p class="muted center" id="place-waiting"></p>
                </div>
            </section>

            <!-- Battle -->
            <section class="battle hidden" id="battle">
                <div class="board-card enemy">
                    <div class="board-head">
                        <h2 class="board-title" id="title-enemy" data-i18n="enemy_waters">Enemy waters</h2>
                        <ul class="fleet-pips" id="pips-enemy" data-i18n-aria="enemy_fleet"></ul>
                    </div>
                    <div class="grid-wrap">
                        <div class="sea-grid target" id="grid-enemy"></div>
                    </div>
                </div>
                <div class="board-card own">
                    <div class="board-head">
                        <h2 class="board-title" id="title-own" data-i18n="your_fleet">Your fleet</h2>
                        <ul class="fleet-pips" id="pips-own" data-i18n-aria="your_fleet"></ul>
                    </div>
                    <div class="grid-wrap">
                        <div class="sea-grid" id="grid-own"></div>
                    </div>
                </div>
            </section>

            <div class="panel-backdrop" id="panel-backdrop"></div>
            <aside class="side-panel" id="side-panel">
                <div class="side-inner">
                    <div class="log-wrap">
                        <h3 class="side-title" data-i18n="log_title">Battle log</h3>
                        <ol class="log-list" id="log-list"></ol>
                    </div>
                    <section class="chat hidden" id="chat">
                        <div class="chat-head">
                            <h3 class="side-title" data-i18n="chat_title">Chat</h3>
                            <button type="button" class="btn-icon chat-close" id="btn-panel-close" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
                        </div>
                        <ol class="chat-list" id="chat-list"></ol>
                        <form class="chat-form" id="chat-form" autocomplete="off">
                            <input type="text" id="chat-input" maxlength="200" data-i18n-placeholder="chat_placeholder" placeholder="Write a message…">
                            <button type="submit" class="chat-send" data-i18n-title="chat_send"><?= $icon('<path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>', 16, 2) ?></button>
                        </form>
                    </section>
                </div>
            </aside>
        </div>

        <div class="game-actions" id="game-actions">
            <button type="button" class="action-btn" id="btn-hint"><?= $icon($ICON_WAND, 18, 2) ?><span data-i18n="hint">Hint</span></button>
            <button type="button" class="action-btn panel-toggle" id="btn-panel-toggle"><?= $icon($ICON_LIST, 18, 2) ?><span data-i18n="log_short">Log</span></button>
            <button type="button" class="action-btn panel-toggle hidden" id="btn-chat-toggle"><?= $icon($ICON_CHAT, 18, 2) ?><span data-i18n="chat_title">Chat</span></button>
            <button type="button" class="action-btn danger" id="btn-resign"><?= $icon($ICON_FLAG, 18, 2) ?><span data-i18n="resign">Resign</span></button>
            <button type="button" class="action-btn primary hidden" id="btn-result"><?= $icon($ICON_TROPHY, 18, 2) ?><span data-i18n="game_over">Game over</span></button>
        </div>

        <!-- Same device: hides the fleets while the device changes hands -->
        <div class="curtain hidden" id="curtain">
            <div class="curtain-inner">
                <div class="radar small" aria-hidden="true">
                    <div class="radar-sweep"></div>
                </div>
                <h2 id="curtain-title"></h2>
                <p class="muted" id="curtain-text"></p>
                <button type="button" class="btn btn-primary" id="btn-curtain"></button>
            </div>
        </div>
    </div>

    <!-- Language (phones) -->
    <div id="lang-overlay" class="modal hidden">
        <div class="modal-content lang-sheet">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="language_title">Language</h3>
            <div class="lang-options" role="listbox">
                <?php foreach (['en' => 'English', 'it' => 'Italiano', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch'] as $code => $label): ?>
                    <button type="button" class="lang-option" data-lang="<?= $code ?>" lang="<?= $code ?>"><span class="lang-code"><?= strtoupper($code) ?></span><?= $label ?><span class="lang-check"><?= $ICON_CHECK ?></span></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="howto-overlay" class="modal hidden">
        <div class="modal-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="howto_title">How to play</h3>
            <div id="howto-sheet-body"></div>
            <button type="button" class="btn btn-primary btn-block" data-close data-i18n="got_it">Got it!</button>
        </div>
    </div>

    <div id="rules-overlay" class="modal hidden">
        <div class="modal-content rules-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="rules_title">Game rules</h3>
            <div class="rules-body" id="rules-body"></div>
        </div>
    </div>

    <div id="settings-overlay" class="modal hidden">
        <div class="modal-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="settings_title">Settings</h3>
            <div class="custom-section">
                <h4 class="custom-title" data-i18n="hull_title">Hull colour</h4>
                <div class="hull-options" id="hull-options" role="radiogroup"></div>
            </div>
            <div class="custom-section">
                <h4 class="custom-title" data-i18n="options_title">Options</h4>
                <div class="option-list">
                    <label class="option-row"><span data-i18n="opt_sound">Sounds</span><input type="checkbox" class="switch" id="opt-sound"></label>
                    <label class="option-row"><span><span data-i18n="opt_automark">Mark the water around sunk ships</span><small data-i18n="opt_automark_hint">Ships never touch, so those cells must be empty</small></span><input type="checkbox" class="switch" id="opt-automark"></label>
                    <label class="option-row"><span data-i18n="opt_coords">Show coordinates</span><input type="checkbox" class="switch" id="opt-coords"></label>
                </div>
            </div>
        </div>
    </div>

    <div id="release-overlay" class="modal hidden">
        <div class="modal-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="release_notes">Release notes</h3>
            <div class="release-list" id="release-list"></div>
        </div>
    </div>

    <div id="donate-overlay" class="modal hidden">
        <div class="modal-content donate-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <div class="donate-icon"><?= $icon($ICON_HEART, 28, 2.2) ?></div>
            <h3 data-i18n="donate_modal_title">Support the project</h3>
            <p class="muted" data-i18n="donate_modal_intro">All my projects are free and open-source — no ads, no tracking, no subscriptions. A small donation helps cover server and maintenance costs.</p>
            <a class="btn btn-primary btn-block" href="https://www.saveriomorelli.com/donate/" target="_blank" rel="noopener">
                <?= $icon($ICON_HEART, 18, 2.2) ?>
                <span data-i18n="donate_cta">Make a donation</span>
            </a>
        </div>
    </div>

    <!-- The opponent lost the connection -->
    <div id="offline-overlay" class="modal hidden" role="alertdialog" aria-labelledby="offline-title" aria-describedby="offline-text">
        <div class="modal-content offline-content">
            <div class="offline-clock">
                <svg viewBox="0 0 100 100" aria-hidden="true">
                    <circle cx="50" cy="50" r="45" class="offline-track"/>
                    <circle cx="50" cy="50" r="45" class="offline-ring" id="offline-ring"/>
                </svg>
                <span id="offline-count" aria-live="off">2:00</span>
            </div>
            <h3 id="offline-title"></h3>
            <p class="muted" id="offline-text"></p>
        </div>
    </div>

    <div id="confirm-overlay" class="modal hidden">
        <div class="modal-content">
            <h3 id="confirm-title"></h3>
            <p class="muted" id="confirm-text"></p>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="btn-confirm-no" data-i18n="cancel">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-yes"></button>
            </div>
        </div>
    </div>

    <div id="gameover-overlay" class="modal hidden">
        <div class="modal-content gameover-content">
            <div id="gameover-icon" class="gameover-icon"></div>
            <h2 id="gameover-title"></h2>
            <p class="muted" id="gameover-text"></p>
            <dl class="stats" id="gameover-stats"></dl>
            <p class="score-line" id="score-line"></p>
            <p class="rematch-status muted" id="rematch-status"></p>
            <div class="gameover-actions">
                <button type="button" class="btn btn-primary btn-block" id="btn-rematch" data-i18n="rematch">Rematch</button>
                <button type="button" class="btn btn-secondary btn-block" id="btn-review" data-i18n="review_board">Look at the boards</button>
                <button type="button" class="btn btn-ghost btn-block" id="btn-gameover-home" data-i18n="back_home">Back to start</button>
            </div>
        </div>
    </div>

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
