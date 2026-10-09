<?php
$current_page = 'projects';
$title = 'MozIta MyUserId Bot';
$description = 'MozIta MyUserId Bot — small Telegram bot that let Mozilla Italia community members get their own Telegram user ID with a single command. A discontinued project by Saverio Morelli.';
$canonical = '/projects/mozita-myuserid-bot/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "MozIta MyUserId Bot",
            "applicationCategory": "CommunicationApplication",
            "operatingSystem": "Telegram",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/mozitamyuserid_bot",
            "softwareVersion": "1.0.7",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "EUR"
            }
        }
    </script>
</head>
<body>

<?php include_once(dirname(__DIR__, 2) . '/include/nav.php'); ?>

<div class="page-header">
    <a href="/projects/" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to projects
    </a>
    <p class="section-label section-label-discontinued">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="21 8 21 21 3 21 3 8"/>
            <rect x="1" y="3" width="22" height="5"/>
            <line x1="10" y1="12" x2="14" y2="12"/>
        </svg>
        Discontinued project
    </p>
    <h1 class="section-title">MozIta MyUserId Bot</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/mozita-myuserid.png" alt="MozIta MyUserId Bot icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">MozIta MyUserId Bot (@mozita_myuserid_bot) was a small utility for the Mozilla
                Italia community. It replied to /myuserid (or /start) with the sender&#x27;s numeric Telegram user ID,
                in private chat or in a group where it was an admin. It also recorded the user IDs, usernames and group
                chat IDs it saw in JSON files and notified the developer about each new one. It was written in Python 3
                with the telepot library.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Telegram bot</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Telegram</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v1.0.7</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2018 – 2019 <span class="book-meta-note">· 1 year</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/Sav22999/mozitamyuserid_bot" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65S8.93 17.38 9 18v4"/>
                        <path d="M9 18c-4.51 2-5-2-7-2"/>
                    </svg>
                    View on GitHub
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Changelog</h2>
        <div class="timeline-list">
            <div class="timeline-item">
                <span class="timeline-version">v1.0.7</span>
                <span class="timeline-date">30 March 2019</span>
                <p>The developer&#x27;s notification about each new user ID now includes a clickable mention of the
                    user.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.6</span>
                <span class="timeline-date">1 March 2019</span>
                <p>The bot now saves each user&#x27;s username along with the user ID, and no longer stores private
                    chats as chat IDs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.5</span>
                <span class="timeline-date">27 January 2019</span>
                <p>Fixed the UTF-8 encoding of console output.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.4</span>
                <span class="timeline-date">27 January 2019</span>
                <p>Fixed startup problems: f-strings were replaced with string concatenation and the earlier encoding
                    change was reverted.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.3</span>
                <span class="timeline-date">27 January 2019</span>
                <p>UTF-8 fix for console messages. The script was renamed to myuserid_mozita.py and now prints its
                    version at startup.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.2</span>
                <span class="timeline-date">19 January 2019</span>
                <p>The developer gets a notification for every new user ID and chat ID.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.1</span>
                <span class="timeline-date">18 January 2019</span>
                <p>The bot token moved to a config.ini file, and the chat IDs where the command is used are now saved
                    too.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1</span>
                <span class="timeline-date">25 November 2018</span>
                <p>First stable version: the bot replies to /myuserid (or /start) with the sender&#x27;s Telegram user
                    ID and saves it to userid_list.json.</p>
            </div>
        </div>
    </div>
</section>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
