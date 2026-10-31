<?php
$description = 'FaceGuess – a two-player face-guessing game with brand-new characters every match. Play with a friend online, no sign-up needed.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <title>FaceGuess – Saverio Morelli</title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="author" content="Saverio Morelli">
    <link rel="canonical" href="https://www.saveriomorelli.com/projects/faceguess/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="FaceGuess – Saverio Morelli">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:url" content="https://www.saveriomorelli.com/projects/faceguess/">
    <meta property="og:image" content="https://www.saveriomorelli.com/projects/faceguess/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="FaceGuess – guess who’s hiding behind the cards, by Saverio Morelli">
    <meta property="og:site_name" content="Saverio Morelli">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="FaceGuess – Saverio Morelli">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="twitter:image" content="https://www.saveriomorelli.com/projects/faceguess/og-image.png">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": [
            "VideoGame",
            "WebApplication"
        ],
        "name": "FaceGuess",
        "url": "https://www.saveriomorelli.com/projects/faceguess/",
        "description": <?= json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "applicationCategory": "GameApplication",
        "genre": "Guessing game",
        "operatingSystem": "Any (web browser)",
        "playMode": "MultiPlayer",
        "inLanguage": [
            "en",
            "it",
            "es",
            "fr",
            "de"
        ],
        "isAccessibleForFree": true,
        "image": "https://www.saveriomorelli.com/projects/faceguess/og-image.png",
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
    <meta name="theme-color" content="#0F0F1A">
    <meta name="color-scheme" content="dark">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FaceGuess">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=15">
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
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg>
            <span id="lang-current">EN</span>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </button>

        <div class="lobby-container">
            <section class="hero">
                <div class="logo-area">
                    <div class="logo-faces" aria-hidden="true">
                        <span class="logo-face f1"></span>
                        <span class="logo-face f2"></span>
                        <span class="logo-face f3"></span>
                    </div>
                    <h1 class="logo-title"><img class="logo-mark" src="icons/favicon.svg" alt="" width="44" height="44"><span>FaceGuess</span></h1>
                    <p class="logo-subtitle" data-i18n="lobby_subtitle">Guess who's hiding behind the cards!</p>
                    <div class="hero-links">
                    <button type="button" class="link-btn howto-link" id="btn-howto">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/></svg>
                        <span data-i18n="howto_title">How to play</span>
                    </button>
                    <button type="button" class="link-btn howto-link rules-link" data-open-rules>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/></svg>
                        <span class="rules-link-label">Game rules</span>
                    </button>
                    </div>
                </div>
                <div class="howto howto-inline">
                    <h2 data-i18n="howto_title">How to play</h2>
                    <ol class="howto-list" id="howto-list">
                        <li>
                            <span class="howto-num">1</span>
                            <div><strong data-i18n="howto1_title">Get your character</strong><p data-i18n="howto1_text">Each player secretly gets a different character from the same board.</p></div>
                        </li>
                        <li>
                            <span class="howto-num">2</span>
                            <div><strong data-i18n="howto2_title">Ask yes/no questions</strong><p data-i18n="howto2_text">Take turns asking about hair, glasses, hats… and hide the faces you rule out.</p></div>
                        </li>
                        <li>
                            <span class="howto-num">3</span>
                            <div><strong data-i18n="howto3_title">Make your guess</strong><p data-i18n="howto3_text">Sure who it is? Guess! A wrong guess loses the game.</p></div>
                        </li>
                    </ol>
                    <button type="button" class="link-btn rules-more" data-open-rules>
                        <span class="rules-link-label">Game rules</span>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </div>
            </section>

            <section class="lobby-panel">
            <ol class="steps-indicator">
                <li class="step active" id="dot-1">
                    <button type="button" class="step-btn" id="btn-step-1" disabled>
                        <span class="step-num">
                            <span class="step-n">1</span>
                            <svg class="step-check" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg>
                        </span>
                        <span class="step-label" data-i18n="step_label_name">Your name</span>
                    </button>
                </li>
                <li class="step-line" aria-hidden="true"></li>
                <li class="step" id="dot-2">
                    <span class="step-btn">
                        <span class="step-num"><span class="step-n">2</span></span>
                        <span class="step-label" data-i18n="step_label_game">Game</span>
                    </span>
                </li>
            </ol>

            <!-- Step 1: name -->
            <form id="step-name" class="lobby-step active" autocomplete="off">
                <label class="step-title" for="player-name" data-i18n="step1_title">What's your name?</label>
                <p class="step-hint" data-i18n="step1_hint">Your opponent will see it during the game.</p>
                <input type="text" id="player-name" maxlength="30" data-i18n-placeholder="name_placeholder" placeholder="e.g. Alex" required>
                <button type="submit" class="btn btn-primary btn-block" id="btn-continue" disabled>
                    <span data-i18n="continue">Continue</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <!-- Step 2: create or join -->
            <div id="step-mode" class="lobby-step">
                <div class="greeting">
                    <span class="step-title" id="greeting-text"></span>
                    <button type="button" class="link-btn" id="btn-change-name" data-i18n="change_name">Change name</button>
                </div>
                <p class="step-hint" data-i18n="step2_hint">Start a new game or join a friend's one.</p>

                <div class="mode-options">
                    <div class="mode-card" id="card-create">
                        <div class="mode-icon">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        </div>
                        <div class="mode-body">
                            <h3 data-i18n="create_title">New game</h3>
                            <p data-i18n="create_desc">We'll generate a code to send to your friend.</p>
                        </div>
                        <div class="play-mode" role="radiogroup" data-i18n-title="play_mode">
                            <label class="play-mode-option">
                                <input type="radio" name="play-mode" value="online" checked>
                                <span class="play-mode-box">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"/></svg>
                                    <strong data-i18n="mode_online">Online</strong>
                                    <small data-i18n="mode_online_desc">Type questions in the chat</small>
                                </span>
                            </label>
                            <label class="play-mode-option">
                                <input type="radio" name="play-mode" value="inperson">
                                <span class="play-mode-box">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 20c0-3.3 2.7-6 6-6s6 2.7 6 6M12 20c0-3.3 1.8-6 4-6 3.3 0 6 2.7 6 6"/></svg>
                                    <strong data-i18n="mode_inperson">In person</strong>
                                    <small data-i18n="mode_inperson_desc">Ask out loud, same room</small>
                                </span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary btn-block" id="btn-create" data-i18n="create_room">Create game</button>
                    </div>

                    <form class="mode-card" id="card-join" autocomplete="off">
                        <div class="mode-icon alt">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="15" r="4"/><path d="M10.8 12.2L20 3M16 7l3 3M14 9l2 2"/></svg>
                        </div>
                        <div class="mode-body">
                            <h3 data-i18n="join_title">I have a code</h3>
                            <p data-i18n="join_desc">Enter the 6-character code your friend sent you.</p>
                        </div>
                        <input type="text" id="room-code" maxlength="6" placeholder="ABC123" class="code-input" autocapitalize="characters" spellcheck="false" aria-label="Room code">
                        <button type="submit" class="btn btn-secondary btn-block" id="btn-join" disabled data-i18n="join_room">Join game</button>
                    </form>

                    <div class="mode-card mode-card-wide" id="card-random">
                        <div class="mode-icon random">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
                        </div>
                        <div class="mode-body">
                            <h3 data-i18n="random_title">Random opponent</h3>
                            <p id="random-desc"></p>
                        </div>
                        <button type="button" class="btn btn-secondary" id="btn-random" data-i18n="random_btn">Find a player</button>
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
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="10"/></svg>
            </button>
        </footer>
    </div>

    <!-- Waiting -->
    <div id="screen-waiting" class="screen">
        <div class="waiting-container">
            <div class="brand-mark"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>FaceGuess</span></div>
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
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                </button>
                <p class="mode-note" id="waiting-mode-note"></p>
            </div>
            <div class="waiting-actions">
                <button type="button" class="btn btn-primary hidden" id="btn-native-share">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
                    <span data-i18n="share_invite">Share invite</span>
                </button>
                <button type="button" class="btn btn-secondary" id="btn-share-link">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 13a5 5 0 007.5.5l3-3a5 5 0 00-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 00-7.5-.5l-3 3a5 5 0 007 7l1.7-1.7"/></svg>
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
                <h2 class="game-title"><img src="icons/favicon.svg" alt="" width="28" height="28"><span>FaceGuess</span></h2>
                <div class="turn-indicator" id="turn-indicator"></div>
                <div class="mode-badge hidden" id="mode-badge" data-i18n="mode_inperson">In person</div>
            </div>
            <div class="game-header-right">
                <div class="players-status" id="players-status"></div>
                <button type="button" class="my-card-btn" id="btn-my-card" data-i18n-title="your_character">
                    <span class="my-card-thumb" id="my-card-thumb"></span>
                    <span class="my-card-label" data-i18n="you_are">You are</span>
                </button>
                <button type="button" class="btn-icon" data-open-rules data-i18n-title="rules_short">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/></svg>
                </button>
                <button type="button" class="btn-icon" id="btn-leave-game" data-i18n-title="leave">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                </button>
            </div>
        </header>

        <div class="game-body">
        <div class="board-container" id="board-container">
            <div id="disconnect-banner" class="disconnect-banner hidden" role="status"></div>
            <div id="guess-banner" class="guess-banner hidden">
                <span data-i18n="guess_banner">Tap the character you think your opponent has</span>
                <button type="button" class="btn btn-ghost btn-sm" id="btn-cancel-guess" data-i18n="cancel">Cancel</button>
            </div>
            <div id="game-board" class="game-board"></div>
        </div>

        <aside class="action-bar">
            <div class="action-bar-inner">
                <div class="side-head">
                    <button type="button" class="side-me" id="side-me">
                        <span class="side-me-thumb" id="side-me-thumb"></span>
                        <span class="side-me-text">
                            <small data-i18n="your_character">Your character</small>
                            <strong id="side-me-name"></strong>
                        </span>
                    </button>
                    <div class="side-score" id="side-score"></div>
                </div>
                <h3 class="side-log-title" data-i18n="questions_title">Questions</h3>
                <p class="side-log-empty" id="side-log-empty" data-i18n="questions_empty">No questions yet.</p>
                <div class="log-wrap empty" id="log-wrap">
                    <div id="question-log" class="question-log" role="log" aria-live="polite"></div>
                    <button type="button" class="log-toggle" id="log-toggle" aria-expanded="false">
                        <span id="log-toggle-count"></span>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 15l6-6 6 6"/></svg>
                    </button>
                </div>
                <form class="ask-section hidden" id="ask-section" autocomplete="off">
                    <p class="ask-label"><span class="ask-dot"></span><span data-i18n="ask_label">Your turn — ask a yes/no question</span></p>
                    <div class="ask-row">
                        <label class="ask-field">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"/><path d="M9.5 9.5a2.5 2.5 0 114 2c-.9.6-1.5 1-1.5 2M12 16.5h.01"/></svg>
                            <input type="text" id="question-input" data-i18n-placeholder="ask_placeholder" placeholder="e.g. Does your character wear glasses?" maxlength="255" enterkeyhint="send">
                        </label>
                        <button type="submit" class="btn btn-primary btn-ask">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            <span class="btn-label" data-i18n="ask">Ask</span>
                        </button>
                        <button type="button" class="btn btn-accent btn-ask" id="btn-guess" data-i18n="guess">Guess!</button>
                    </div>
                </form>
                <div class="answer-section hidden" id="answer-section">
                    <div class="answer-head">
                        <span class="answer-thumb" id="answer-thumb"></span>
                        <p id="pending-question"></p>
                    </div>
                    <div class="answer-buttons">
                        <button type="button" class="btn btn-yes" data-answer="yes" data-i18n="yes">Yes</button>
                        <button type="button" class="btn btn-no" data-answer="no" data-i18n="no">No</button>
                    </div>
                </div>
                <p class="wait-section hidden" id="wait-section"></p>
                <div class="lastchance-section hidden" id="lastchance-section">
                    <div class="lastchance-head">
                        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        <div>
                            <strong id="lastchance-title"></strong>
                            <span data-i18n="last_chance_text">Last chance: guess their character to draw. A wrong guess loses the game.</span>
                        </div>
                    </div>
                    <div class="lastchance-actions">
                        <button type="button" class="btn btn-ghost btn-ask" id="btn-concede" data-i18n="concede">I give up</button>
                        <button type="button" class="btn btn-accent btn-ask" id="btn-guess-last" data-i18n="guess">Guess!</button>
                    </div>
                </div>
                <div class="inperson-section hidden" id="inperson-section">
                    <div class="inperson-hint">
                        <svg class="inperson-icon-mine" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a3 3 0 013 3v6a3 3 0 01-6 0V5a3 3 0 013-3z"/><path d="M19 10v1a7 7 0 01-14 0v-1M12 18v4M8 22h8"/></svg>
                        <svg class="inperson-icon-theirs" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 19a2 2 0 01-2 2h-1v-6h3zM3 19a2 2 0 002 2h1v-6H3z"/></svg>
                        <div>
                            <strong id="inperson-title"></strong>
                            <span id="inperson-text"></span>
                        </div>
                    </div>
                    <div class="inperson-actions" id="inperson-actions">
                        <button type="button" class="btn btn-primary btn-ask" id="btn-end-turn">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            <span data-i18n="end_turn">End turn</span>
                        </button>
                        <button type="button" class="btn btn-accent btn-ask" id="btn-guess-inperson" data-i18n="guess">Guess!</button>
                    </div>
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
                <button type="button" class="lang-option" data-lang="en" lang="en"><span class="lang-code">EN</span>English<svg class="lang-check" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg></button>
                <button type="button" class="lang-option" data-lang="it" lang="it"><span class="lang-code">IT</span>Italiano<svg class="lang-check" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg></button>
                <button type="button" class="lang-option" data-lang="es" lang="es"><span class="lang-code">ES</span>Español<svg class="lang-check" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg></button>
                <button type="button" class="lang-option" data-lang="fr" lang="fr"><span class="lang-code">FR</span>Français<svg class="lang-check" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg></button>
                <button type="button" class="lang-option" data-lang="de" lang="de"><span class="lang-code">DE</span>Deutsch<svg class="lang-check" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg></button>
            </div>
        </div>
    </div>

    <!-- How to play (phones and tablets) -->
    <div id="howto-overlay" class="modal hidden">
        <div class="modal-content howto">
            <h3 data-i18n="howto_title">How to play</h3>
            <div id="howto-sheet-body"></div>
            <button type="button" class="btn btn-primary" id="btn-close-howto" data-i18n="got_it">Got it!</button>
            <button type="button" class="btn btn-ghost rules-sheet-btn" data-open-rules><span class="rules-link-label">Game rules</span></button>
        </div>
    </div>

    <!-- Game rules -->
    <div id="rules-overlay" class="modal hidden">
        <div class="modal-content rules-content">
            <button type="button" class="zoom-close" id="btn-close-rules" data-i18n-title="close">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <h3 id="rules-title"></h3>
            <div class="rules-body" id="rules-body"></div>
        </div>
    </div>

    <!-- Release notes -->
    <div id="release-overlay" class="modal hidden">
        <div class="modal-content release-content">
            <button type="button" class="zoom-close" id="btn-close-release" data-i18n-title="close">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <h3 data-i18n="release_notes">Release notes</h3>
            <div class="release-list" id="release-list"></div>
        </div>
    </div>

    <!-- Own character -->
    <div id="mycard-overlay" class="modal hidden">
        <div class="modal-content">
            <h3 data-i18n="your_character">Your character</h3>
            <div class="modal-avatar" id="mycard-display"></div>
            <p class="modal-name" id="mycard-name"></p>
            <p class="muted" data-i18n="your_character_hint">Your opponent has to guess who this is. Answer their questions about this character.</p>
            <button type="button" class="btn btn-primary" id="btn-close-mycard" data-i18n="got_it">Got it!</button>
        </div>
    </div>

    <!-- Zoomed character -->
    <div id="zoom-overlay" class="modal hidden">
        <div class="modal-content zoom-content">
            <button type="button" class="zoom-close" id="btn-zoom-close" data-i18n-title="close">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <div class="zoom-stage">
                <button type="button" class="zoom-nav" id="btn-zoom-prev" data-i18n-title="previous">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <div class="zoom-avatar" id="zoom-avatar"></div>
                <button type="button" class="zoom-nav" id="btn-zoom-next" data-i18n-title="next">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                </button>
            </div>
            <p class="modal-name" id="zoom-name"></p>
            <p class="zoom-counter" id="zoom-counter"></p>
            <button type="button" class="btn btn-secondary" id="btn-zoom-toggle"></button>
        </div>
    </div>

    <!-- Confirm final guess -->
    <div id="confirm-overlay" class="modal hidden">
        <div class="modal-content">
            <h3 id="confirm-title"></h3>
            <div class="modal-avatar small" id="confirm-avatar"></div>
            <p class="warning-text" id="confirm-warning"></p>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="btn-confirm-no" data-i18n="cancel">Cancel</button>
                <button type="button" class="btn btn-accent" id="btn-confirm-yes" data-i18n="confirm_guess">Yes, guess!</button>
            </div>
        </div>
    </div>

    <!-- Opponent left -->
    <div id="left-overlay" class="modal hidden">
        <div class="modal-content">
            <div class="left-icon">
                <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M17 8l5 5M22 8l-5 5"/></svg>
            </div>
            <h3 id="left-title"></h3>
            <p class="muted" data-i18n="opponent_left_text">The game can't continue. Go back to start a new one.</p>
            <button type="button" class="btn btn-primary" id="btn-left-home" data-i18n="back_home">Back to start</button>
        </div>
    </div>

    <!-- Game over -->
    <div id="gameover-overlay" class="modal hidden">
        <div class="modal-content gameover-content">
            <div id="gameover-icon"></div>
            <h2 id="gameover-title"></h2>
            <p class="muted" id="gameover-text"></p>
            <div class="reveal-row">
                <figure>
                    <div class="modal-avatar small" id="gameover-mine"></div>
                    <figcaption data-i18n="you_were">You were</figcaption>
                </figure>
                <figure>
                    <div class="modal-avatar small" id="gameover-theirs"></div>
                    <figcaption data-i18n="they_were">They were</figcaption>
                </figure>
            </div>
            <p class="score-line" id="score-line"></p>
            <p class="rematch-status" id="rematch-status"></p>
            <div class="gameover-actions">
                <button type="button" class="btn btn-primary btn-block" id="btn-rematch"></button>
                <button type="button" class="btn btn-ghost btn-block" id="btn-new-opponent" data-i18n="play_someone_else">Play with someone else</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
</div>

<script src="i18n.js?v=15"></script>
<script src="characters.js?v=15"></script>
<script src="releases.js?v=15"></script>
<script src="rules.js?v=15"></script>
<script src="game.js?v=15"></script>
</body>
</html>
