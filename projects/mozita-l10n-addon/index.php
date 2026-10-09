<?php
$current_page = 'projects';
$title = 'MozIta L10n Addon';
$description = 'MozIta L10n Addon — firefox add-on for the Mozilla Italia localization team to copy special characters in one click and search Transvision, Microsoft Language Portal and Pontoon. A project by Saverio Morelli.';
$canonical = '/projects/mozita-l10n-addon/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "MozIta L10n Addon",
            "applicationCategory": "BrowserApplication",
            "operatingSystem": "Firefox",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://addons.mozilla.org/firefox/addon/mozita-l10n/",
            "softwareVersion": "2.2.2.1",
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
    <p class="section-label">Project</p>
    <h1 class="section-title">MozIta L10n Addon</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/mozita-l10n-addon.png" alt="MozIta L10n Addon icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">MozIta L10n Addon is a Firefox add-on built for the Mozilla Italia localization
                (l10n) team. Its popup copies, with one click, typographic and accented characters the team uses often
                but that are missing from the Italian keyboard, such as curly quotes, ellipsis, dashes and capital
                accented vowels, including several characters at once. It also offers a quick search on Mozilla
                Transvision, Microsoft Language Portal or Mozilla Pontoon and can be opened with Ctrl+Alt+I.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Browser add-on</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Firefox</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Users</span>
                    <span class="book-meta-value">~2</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v2.2.2.1</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2019 – present <span class="book-meta-note">· <?php $years = date('Y') - 2019; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://addons.mozilla.org/firefox/addon/mozita-l10n/" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Firefox
                </a>
                <a href="https://github.com/Sav22999/mozita-l10n-addon" target="_blank" rel="noopener"
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
    <div class="addon-screenshots">
        <h2 class="addon-screenshots-title">Screenshots</h2>
        <div class="screenshot-grid">
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: MozIta L10n add-on promo image (&#x27;Il componente aggiuntivo per il team l10n di Mozilla Italia&#x27;) showing the popup with Transvision, Microsoft and Pontoon search tabs and a grid of useful characters">
                <img src="/images/projects/screenshots/mozita-l10n-addon/1.png" alt="MozIta L10n add-on promo image (&#x27;Il componente aggiuntivo per il team l10n di Mozilla Italia&#x27;) showing the popup with Transvision, Microsoft and Pontoon search tabs and a grid of useful characters" width="1280" height="800"
                     loading="lazy">
            </button>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Changelog</h2>
        <div class="timeline-list">
            <div class="timeline-item">
                <span class="timeline-version">v2.2.2.1</span>
                <span class="timeline-date">2 August 2021</span>
                <p>No release notes available.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2.2</span>
                <span class="timeline-date">31 July 2021</span>
                <p>Added multiple character selection: click several characters in the right order to copy them
                    together.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2.1</span>
                <span class="timeline-date">29 July 2021</span>
                <p>Added tooltips on characters, remembered the last search engine used (Transvision, Microsoft or
                    Pontoon) and added a new character. Also improved performance and fixed some bugs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2</span>
                <span class="timeline-date">21 July 2021</span>
                <p>Added search on Pontoon, switched to a bold font, updated the extension icon and made some
                    improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1.1</span>
                <span class="timeline-date">26 July 2020</span>
                <p>Improved the user interface.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">20 July 2020</span>
                <p>Added search on Microsoft Language Portal alongside Transvision, fixed bugs and improved the
                    interface.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2</span>
                <span class="timeline-date">7 April 2020</span>
                <p>Improved the interface, added a &#x27;Copiato&#x27; (copied) message after a character is copied and
                    updated the icons.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">30 January 2020</span>
                <p>Added the Ctrl+Alt+I keyboard shortcut to open the add-on.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">1 December 2019</span>
                <p>First release, with one-click copying of characters and a search box for Transvision
                    (English–Italian).</p>
            </div>
        </div>
    </div>
</section>

<div class="screenshot-overlay" id="screenshot-overlay">
    <img src="" alt="">
    <button type="button" class="screenshot-nav screenshot-prev" aria-label="Previous screenshot" hidden>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>
    <button type="button" class="screenshot-nav screenshot-next" aria-label="Next screenshot" hidden>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>
</div>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
