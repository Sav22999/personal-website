<?php
$description = 'Prism Mine: a free deduction game. Send beams of light into a dark mine, watch where they come out and in which colour, and work out where the gems are hidden. Solo, daily mine and online duels. No sign-up.';
$v = 9;
$icon = fn($d, $size = 18, $sw = 2.2) => '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="' . $sw . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
$ICON_CLOSE = $icon('<path d="M6 6l12 12M18 6L6 18"/>', 20, 2.4);
$ICON_FLAG = '<path d="M4 22V4M4 4h13l-2 4 2 4H4"/>';
$ICON_BULB = '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7V16h8v-1.3A7 7 0 0012 2z"/>';
$ICON_BOOK = '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>';
$ICON_LEAVE = '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>';
$ICON_LIST = '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>';
$ICON_TROPHY = '<path d="M8 4h8v7a4 4 0 01-8 0zM8 6H4a4 4 0 004 5M16 6h4a4 4 0 01-4 5M12 15v4M8 21h8"/>';
$ICON_CHAT = '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>';
$ICON_SOUND = '<path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/>';
$ICON_SETTINGS = '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 01-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 010-4h.1a1.7 1.7 0 001.5-1.1 1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 014 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 010 4h-.1a1.7 1.7 0 00-1.5 1z"/>';
$ICON_CHECK = $icon('<path d="M5 12l5 5L20 7"/>', 18, 3);
$ICON_HEART = '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>';
$ICON_ROTATE = '<path d="M21 12a9 9 0 11-3-6.7L21 8"/><path d="M21 3v5h-5"/>';
$ICON_GLOBE = '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>';
$ICON_GEM = '<path d="M6 3h12l4 6-10 12L2 9z"/><path d="M2 9h20M12 21L8 9l4-6 4 6z"/>';
$ICON_SUN = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>';
$ICON_SWORDS = '<path d="M14.5 17.5L3 6V3h3l11.5 11.5M13 19l6-6M16 16l4 4M19 21l2-2M9.5 6.5L13 3h3v3l-3.5 3.5M5 14l-2 2 3 3 2-2"/>';
$ICON_ARROW = $icon('<path d="M5 12h14M13 6l6 6-6 6"/>', 18, 2.4);
$ICON_SEND = '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>';

$levelChips = function ($name, $default) {
    $out = '<div class="chips" role="radiogroup">';
    foreach (['easy', 'medium', 'hard'] as $lv) {
        $out .= '<label class="chip"><input type="radio" name="' . $name . '" value="' . $lv . '"' . ($lv === $default ? ' checked' : '') . '><span data-i18n="level_' . $lv . '">' . ucfirst($lv) . '</span></label>';
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
    <title>Prism Mine – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/prism-mine/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Prism Mine – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/prism-mine/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/prism-mine/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Prism Mine – a deduction game with beams of light and hidden gems, by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Prism Mine – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/prism-mine/og-image.png">
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": ["VideoGame", "WebApplication"],
            "name": "Prism Mine",
            "url": "https://www.saveriomorelli.com/projects/prism-mine/",
            "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            "applicationCategory": "GameApplication",
            "genre": "Puzzle",
            "operatingSystem": "Any (web browser)",
            "playMode": ["MultiPlayer", "SinglePlayer"],
            "inLanguage": ["en", "it", "es", "fr", "de"],
            "isAccessibleForFree": true,
            "image": "https://www.saveriomorelli.com/projects/prism-mine/og-image.png",
            "author": {"@type": "Person", "name": "Saverio Morelli", "url": "https://www.saveriomorelli.com/"},
            "offers": {"@type": "Offer", "price": "0", "priceCurrency": "EUR"}
        }
    </script>
    <meta name="theme-color" content="#15100D">
    <meta name="color-scheme" content="dark light">
    <script src="theme.js?v=<?= $v ?>"></script>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Prism Mine">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preload" href="fonts/fredoka-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="fonts/nunito-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="style.css?v=<?= $v ?>">
</head>
<body data-v="<?= $v ?>">

<div id="app">
    <!-- Lobby -->
    <div id="screen-lobby" class="screen active">
        <div class="top-tools">
            <div class="theme-selector" role="radiogroup" data-i18n-title="theme_title">
                <button type="button" class="theme-tab" data-theme-mode="auto" role="radio" data-i18n="theme_auto">Auto</button>
                <button type="button" class="theme-tab" data-theme-mode="light" role="radio" data-i18n="theme_light">Light</button>
                <button type="button" class="theme-tab" data-theme-mode="dark" role="radio" data-i18n="theme_dark">Dark</button>
            </div>
            <button type="button" class="theme-toggle" id="btn-theme" data-i18n-title="theme_title">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 010 18z" fill="currentColor" stroke="none"/></svg>
                <span data-theme-label="auto" data-i18n="theme_auto">Auto</span>
                <span data-theme-label="light" data-i18n="theme_light" hidden>Light</span>
                <span data-theme-label="dark" data-i18n="theme_dark" hidden>Dark</span>
            </button>
            <div class="lang-selector">
                <?php foreach (['en', 'it', 'es', 'fr', 'de'] as $code): ?>
                    <button type="button" class="lang-btn" data-lang="<?= $code ?>"><?= strtoupper($code) ?></button>
                <?php endforeach; ?>
            </div>
            <button type="button" class="lang-toggle" id="btn-lang-menu" aria-haspopup="dialog" data-i18n-title="language_title">
                <?= $icon($ICON_GLOBE, 18, 2) ?><span id="lang-current">EN</span><?= $icon('<path d="M6 9l6 6 6-6"/>', 14, 2.6) ?>
            </button>
        </div>

        <div class="lobby-container">
            <section class="hero">
                <div class="hero-art" id="hero-art" aria-hidden="true"></div>
                <h1 class="logo-title">
                    <img class="logo-mark" src="icons/favicon.svg" alt="" width="44" height="44">
                    <span>Prism Mine</span>
                </h1>
                <p class="logo-subtitle" data-i18n="lobby_subtitle">Light a beam into the dark mine. Where it comes out, and in which colour, tells you where the gems are hiding.</p>
                <div class="hero-links">
                    <button type="button" class="link-btn" id="btn-howto"><?= $icon('<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>', 16) ?><span data-i18n="howto_title">How to play</span></button>
                    <button type="button" class="link-btn" data-start-example><?= $icon('<polygon points="6 4 20 12 6 20 6 4"/>', 16) ?><span data-i18n="example_title">Example game</span></button>
                    <button type="button" class="link-btn" data-open-rules><?= $icon($ICON_BOOK, 16) ?><span data-i18n="rules_title">Game rules</span></button>
                    <button type="button" class="link-btn" data-open-settings><?= $icon($ICON_SETTINGS, 16) ?><span data-i18n="settings_title">Settings</span></button>
                </div>
                <ol class="howto-list" id="howto-list">
                    <li><span class="howto-num">1</span><div><strong data-i18n="howto1_title">Send beams</strong><p data-i18n="howto1_text">Tap a number on the edge: a beam of light goes in, bounces off the gems and comes out somewhere, coloured by every gem it touched.</p></div></li>
                    <li><span class="howto-num">2</span><div><strong data-i18n="howto2_title">Sketch the mine</strong><p data-i18n="howto2_text">You know which gems are hidden. Place them on your sketch: it tells you which beams it already explains.</p></div></li>
                    <li><span class="howto-num">3</span><div><strong data-i18n="howto3_title">Give your answer</strong><p data-i18n="howto3_text">When your sketch explains everything, submit it. Fewer beams, better score.</p></div></li>
                </ol>
            </section>

            <section class="lobby-panel">
                <form id="step-name" class="lobby-step active" autocomplete="off">
                    <label class="step-title" for="player-name" data-i18n="step1_title">What's your name, prospector?</label>
                    <p class="step-hint" data-i18n="step1_hint">Your opponent will see it in online duels.</p>
                    <input type="text" id="player-name" maxlength="30" data-i18n-placeholder="name_placeholder" placeholder="e.g. Alex" required>
                    <button type="submit" class="btn btn-primary btn-block" id="btn-continue" disabled><span data-i18n="continue">Continue</span><?= $ICON_ARROW ?></button>
                </form>

                <div id="step-mode" class="lobby-step">
                    <div class="greeting">
                        <span class="step-title" id="greeting-text"></span>
                        <button type="button" class="link-btn" id="btn-change-name" data-i18n="change_name">Change name</button>
                    </div>

                    <div class="mode-tabs" role="tablist">
                        <button type="button" class="mode-tab active" role="tab" data-tab="solo"><?= $icon($ICON_GEM, 18, 2) ?><span data-i18n="tab_solo">Solo</span></button>
                        <button type="button" class="mode-tab" role="tab" data-tab="daily"><?= $icon($ICON_SUN, 18, 2) ?><span data-i18n="tab_daily">Daily</span></button>
                        <button type="button" class="mode-tab" role="tab" data-tab="online"><?= $icon($ICON_SWORDS, 18, 2) ?><span data-i18n="tab_online">Duel</span></button>
                    </div>

                    <div class="mode-panel active" data-panel="solo">
                        <div class="mode-card">
                            <div class="mode-head">
                                <div class="mode-icon"><?= $icon($ICON_GEM, 22) ?></div>
                                <div><h3 data-i18n="solo_title">A new mine</h3><p data-i18n="solo_desc">A freshly dug mine, at your own pace. Your best score is kept for every level.</p></div>
                            </div>
                            <div class="field"><span class="field-label" data-i18n="level_label">Level</span><?= $levelChips('solo-level', 'easy') ?></div>
                            <p class="level-note muted" id="level-note"></p>
                            <button type="button" class="btn btn-primary btn-block" id="btn-start-solo" data-i18n="start_game">Start digging</button>
                        </div>
                    </div>

                    <div class="mode-panel" data-panel="daily">
                        <div class="mode-card">
                            <div class="mode-head">
                                <div class="mode-icon warm"><?= $icon($ICON_SUN, 22) ?></div>
                                <div><h3 data-i18n="daily_title">Mine of the day</h3><p data-i18n="daily_desc">The same mine for everyone, a new one every day. One try: how few beams do you need?</p></div>
                            </div>
                            <p class="daily-status" id="daily-status"></p>
                            <button type="button" class="btn btn-primary btn-block" id="btn-start-daily" data-i18n="daily_btn">Play today's mine</button>
                        </div>
                    </div>

                    <div class="mode-panel" data-panel="online">
                        <div class="mode-card" id="card-create">
                            <div class="mode-head">
                                <div class="mode-icon"><?= $icon('<path d="M12 5v14M5 12h14"/>', 22) ?></div>
                                <div><h3 data-i18n="create_title">Duel a friend</h3><p data-i18n="create_desc">Each of you hides the gems in your own mine, then you take turns finding the other: the first right answer wins.</p></div>
                            </div>
                            <div class="field"><span class="field-label" data-i18n="level_label">Level</span><?= $levelChips('online-level', 'medium') ?></div>
                            <div class="field">
                                <span class="field-label" data-i18n="time_label">Time per player</span>
                                <div class="chips" role="radiogroup">
                                    <?php foreach ([0, 3, 5, 10] as $m): ?>
                                        <label class="chip"><input type="radio" name="time-online" value="<?= $m ?>"<?= $m === 5 ? ' checked' : '' ?>><span><?= $m === 0 ? '<span data-i18n="time_none">No clock</span>' : $m . ' min' ?></span></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" id="btn-create" data-i18n="create_room">Create duel</button>
                        </div>
                        <form class="mode-card" id="card-join" autocomplete="off">
                            <div class="mode-head">
                                <div class="mode-icon alt"><?= $icon('<circle cx="8" cy="15" r="4"/><path d="M10.8 12.2L20 3M16 7l3 3M14 9l2 2"/>', 22) ?></div>
                                <div><h3 data-i18n="join_title">I have a code</h3><p data-i18n="join_desc">Enter the 6-character code your friend sent you.</p></div>
                            </div>
                            <input type="text" id="room-code" maxlength="6" placeholder="ABC123" class="code-input" autocapitalize="characters" spellcheck="false" data-i18n-aria="room_code">
                            <button type="submit" class="btn btn-secondary btn-block" id="btn-join" disabled data-i18n="join_room">Join duel</button>
                        </form>
                        <div class="mode-card">
                            <div class="mode-head">
                                <div class="mode-icon warm"><?= $icon('<path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/>', 22) ?></div>
                                <div><h3 data-i18n="random_title">Random opponent</h3><p id="random-desc">Duel someone who speaks your language. Medium level, 5 minutes each.</p></div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-block" id="btn-random" data-i18n="random_btn">Find a player</button>
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
                <button type="button" class="foot-btn" id="btn-version" data-i18n-title="release_notes"><span id="app-version"></span></button>
            </div>
        </footer>
    </div>

    <!-- Waiting for the second player -->
    <div id="screen-waiting" class="screen">
        <div class="waiting-container">
            <div class="brand-mark"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>Prism Mine</span></div>
            <div class="lantern" aria-hidden="true"></div>
            <h2 id="waiting-title"></h2>
            <p class="muted" id="waiting-public-desc"></p>
            <div id="waiting-private">
                <p class="muted" data-i18n="share_code">Send this code to your friend:</p>
                <button type="button" class="room-code-display" id="btn-copy-code"><span id="display-code"></span><?= $icon('<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>', 18, 2) ?></button>
            </div>
            <div class="waiting-actions">
                <button type="button" class="btn btn-secondary" id="btn-share-link"><span data-i18n="share_link">Copy invite link</span></button>
                <button type="button" class="btn btn-ghost" id="btn-leave-waiting" data-i18n="leave">Leave</button>
            </div>
        </div>
    </div>

    <!-- Game -->
    <div id="screen-game" class="screen">
        <header class="game-header">
            <a class="game-title" href="./"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>Prism Mine</span></a>
            <div class="game-header-right">
                <span class="mode-badge" id="mode-badge"></span>
                <button type="button" class="btn-icon" id="btn-audio-toggle" data-i18n-title="sound"><?= $icon($ICON_SOUND, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-settings data-i18n-title="settings_title"><?= $icon($ICON_SETTINGS, 20, 2) ?></button>
                <button type="button" class="btn-icon" data-open-rules data-i18n-title="rules_title"><?= $icon($ICON_BOOK, 20, 2) ?></button>
                <button type="button" class="btn-icon" id="btn-leave-game" data-i18n-title="leave"><?= $icon($ICON_LEAVE, 20, 2) ?></button>
            </div>
        </header>

        <div class="status-bar" id="status-bar">
            <div class="miner" id="miner-me">
                <div class="miner-info"><span class="miner-name" id="name-me"></span><span class="miner-sub" id="sub-me"></span></div>
                <span class="miner-clock hidden" id="clock-me">0:00</span>
            </div>
            <div class="turn-indicator" id="turn-indicator" role="status" aria-live="polite"></div>
            <div class="miner right" id="miner-opp">
                <span class="miner-clock hidden" id="clock-opp">0:00</span>
                <div class="miner-info"><span class="miner-name" id="name-opp"></span><span class="miner-sub" id="sub-opp"></span></div>
            </div>
        </div>

        <div class="game-body">
            <section class="board-zone">
                <div class="board-wrap">
                    <div class="view-tabs hidden" id="view-tabs" role="tablist">
                        <button type="button" role="tab" data-view="target" data-i18n="view_target">Their mine</button>
                        <button type="button" role="tab" data-view="mine" data-i18n="view_mine">Your mine</button>
                    </div>
                    <div class="board-stage">
                        <svg class="mine-board" id="mine-board" viewBox="0 0 100 100" role="group" data-i18n-aria="board_label"></svg>
                        <div class="gem-toolbar hidden" id="gem-toolbar">
                            <button type="button" id="btn-gem-rotate" data-i18n-title="rotate"><?= $icon($ICON_ROTATE, 20, 2.4) ?></button>
                            <button type="button" id="btn-gem-remove" data-i18n-title="remove_gem"><?= $icon('<path d="M6 6l12 12M18 6L6 18"/>', 20, 2.4) ?></button>
                        </div>
                    </div>
                </div>
                <div class="draft-bar">
                    <div class="example-card hidden" id="example-card" aria-live="polite">
                        <span class="example-step" id="example-step"></span>
                        <p class="example-text" id="example-text"></p>
                        <div class="example-nav">
                            <button type="button" class="btn btn-secondary" id="btn-ex-prev" data-i18n="example_prev">Back</button>
                            <button type="button" class="btn btn-primary" id="btn-ex-next" data-i18n="example_next">Next</button>
                        </div>
                    </div>
                    <ul class="tray" id="tray" data-i18n-aria="gems_label"></ul>
                    <div class="draft-tools">
                        <button type="button" class="btn btn-secondary" id="btn-rotate"><?= $icon($ICON_ROTATE, 18, 2.2) ?><span data-i18n="rotate">Rotate</span></button>
                        <button type="button" class="btn btn-secondary hidden" id="btn-random-place" data-i18n="random_place">Random</button>
                        <button type="button" class="btn btn-ghost" id="btn-clear" data-i18n="clear_sketch">Clear sketch</button>
                        <button type="button" class="btn btn-primary" id="btn-answer" disabled><span data-i18n="answer">Submit answer</span></button>
                    </div>
                    <p class="sketch-status muted" id="sketch-status"></p>
                    <p class="place-countdown hidden" id="place-countdown" aria-live="off"></p>
                    <p class="place-tip muted" id="place-tip" data-i18n="place_tip">Drag the gems onto the mine. Tap a gem to rotate or remove it; drag it out to put it back.</p>
                </div>
            </section>

            <div class="panel-backdrop" id="panel-backdrop"></div>
            <aside class="side-panel" id="side-panel">
                <div class="side-inner">
                    <div class="log-wrap">
                        <div class="chat-head">
                            <h3 class="side-title" data-i18n="log_title">Beams</h3>
                            <button type="button" class="btn-icon chat-close" id="btn-panel-close" data-i18n-title="close"><?= $ICON_CLOSE ?></button>
                        </div>
                        <ol class="log-list" id="log-list"></ol>
                    </div>
                    <section class="chat hidden" id="chat">
                        <h3 class="side-title" data-i18n="chat_title">Chat</h3>
                        <ol class="chat-list" id="chat-list"></ol>
                        <form class="chat-form" id="chat-form" autocomplete="off">
                            <input type="text" id="chat-input" maxlength="200" data-i18n-placeholder="chat_placeholder" placeholder="Write a message…">
                            <button type="submit" class="chat-send" data-i18n-title="chat_send"><?= $icon($ICON_SEND, 16, 2) ?></button>
                        </form>
                    </section>
                </div>
            </aside>
        </div>

        <div class="game-actions" id="game-actions">
            <button type="button" class="action-btn" id="btn-hint"><?= $icon($ICON_BULB, 18, 2) ?><span data-i18n="hint">Reveal a gem</span></button>
            <button type="button" class="action-btn panel-toggle" id="btn-panel-toggle"><?= $icon($ICON_LIST, 18, 2) ?><span data-i18n="log_title">Beams</span></button>
            <button type="button" class="action-btn panel-toggle hidden" id="btn-chat-toggle"><?= $icon($ICON_CHAT, 18, 2) ?><span data-i18n="chat_title">Chat</span></button>
            <button type="button" class="action-btn danger" id="btn-resign"><?= $icon($ICON_FLAG, 18, 2) ?><span data-i18n="resign">Give up</span></button>
            <button type="button" class="action-btn primary hidden" id="btn-result"><?= $icon($ICON_TROPHY, 18, 2) ?><span data-i18n="game_over">Result</span></button>
        </div>
    </div>

    <!-- Sheets -->
    <div id="lang-overlay" class="modal hidden">
        <div class="modal-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <h3 data-i18n="language_title">Language</h3>
            <div class="lang-options">
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
            <button type="button" class="btn btn-secondary btn-block" data-start-example data-i18n="example_cta">Watch an example game</button>
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
            <div class="option-list">
                <label class="option-row"><span data-i18n="opt_sound">Sounds</span><input type="checkbox" class="switch" id="opt-sound"></label>
                <label class="option-row"><span><span data-i18n="opt_check">Check my sketch</span><small data-i18n="opt_check_hint">Marks the beams your sketch already explains and the ones it gets wrong</small></span><input type="checkbox" class="switch" id="opt-check"></label>
                <label class="option-row"><span><span data-i18n="opt_paths">Show beam paths in my sketch</span><small data-i18n="opt_paths_hint">Point at a number to see where a beam would go through your sketch</small></span><input type="checkbox" class="switch" id="opt-paths"></label>
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
        <div class="modal-content center-content">
            <button type="button" class="modal-close" data-close data-i18n-title="close"><?= $ICON_CLOSE ?></button>
            <div class="donate-icon"><?= $icon($ICON_HEART, 28, 2.2) ?></div>
            <h3 data-i18n="donate_modal_title">Support the project</h3>
            <p class="muted" data-i18n="donate_modal_intro">All my projects are free and open-source — no ads, no tracking, no subscriptions. A small donation helps cover server and maintenance costs.</p>
            <a class="btn btn-primary btn-block" href="https://www.saveriomorelli.com/donate/" target="_blank" rel="noopener"><?= $icon($ICON_HEART, 18, 2.2) ?><span data-i18n="donate_cta">Make a donation</span></a>
        </div>
    </div>

    <div id="offline-overlay" class="modal hidden" role="alertdialog" aria-labelledby="offline-title" aria-describedby="offline-text">
        <div class="modal-content center-content offline-content">
            <div class="offline-clock">
                <svg viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="45" class="offline-track"/><circle cx="50" cy="50" r="45" class="offline-ring" id="offline-ring"/></svg>
                <span id="offline-count">2:00</span>
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
        <div class="modal-content center-content">
            <div id="gameover-icon" class="gameover-icon"></div>
            <h2 id="gameover-title"></h2>
            <p class="muted" id="gameover-text"></p>
            <p class="score-big" id="gameover-score"></p>
            <p class="score-line" id="score-line"></p>
            <p class="rematch-status muted" id="rematch-status"></p>
            <div class="gameover-actions">
                <button type="button" class="btn btn-primary btn-block" id="btn-rematch" data-i18n="play_again">Play again</button>
                <button type="button" class="btn btn-secondary btn-block hidden" id="btn-share-result" data-i18n="share_result">Share result</button>
                <button type="button" class="btn btn-secondary btn-block" id="btn-review" data-i18n="review_board">Look at the mine</button>
                <button type="button" class="btn btn-ghost btn-block" id="btn-gameover-home" data-i18n="back_home">Back to start</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
</div>

<script src="i18n.js?v=<?= $v ?>"></script>
<script src="engine.js?v=<?= $v ?>"></script>
<script src="rules.js?v=<?= $v ?>"></script>
<script src="releases.js?v=<?= $v ?>"></script>
<script src="game.js?v=<?= $v ?>"></script>
</body>
</html>
