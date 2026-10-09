<?php
$current_page = 'projects';
$title = 'Emoji';
$description = 'Emoji — copy or insert any emoji with a click, search by keyword, color or gender, and customize the picker; free and open-source for Firefox, Chrome and Edge. A project by Saverio Morelli.';
$canonical = '/projects/emoji/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Emoji",
            "applicationCategory": "BrowserApplication",
            "operatingSystem": "Firefox, Chrome, Edge, Thunderbird",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://www.emojiaddon.com/",
            "softwareVersion": "3.25",
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
    <h1 class="section-title">Emoji</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/emoji.png" alt="Emoji icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Emoji is a free, open-source emoji picker for the browser: click an emoji to
                copy it to the clipboard or, optionally, insert it straight into the focused text field. It offers
                search by keyword, colour or gender, a most used section, skin tones, light and dark themes, several
                emoji styles and a customisable keyboard shortcut (Ctrl/Cmd+Alt+A by default). It collects no data, can
                sync settings and most used emojis through the browser account, and is available for Firefox, Chrome and
                Edge.</p>
            <div class="addon-badges">
                <span class="addon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Recommended by Firefox
                </span>
                <span class="addon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Featured on Chrome Web Store
                </span>
                <span class="addon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Featured on Microsoft Edge Add-ons
                </span>
            </div>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Browser add-on</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Firefox, Chrome, Edge, Thunderbird</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Users</span>
                    <span class="book-meta-value">~47,700</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v3.25</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2019 – present <span class="book-meta-note">· <?php $years = date('Y') - 2019; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://addons.mozilla.org/firefox/addon/emoji-sav/" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Firefox
                </a>
                <a href="https://chromewebstore.google.com/detail/emoji/kjepehkgbooeigeflhiogplnckadlife" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Chrome
                </a>
                <a href="https://microsoftedge.microsoft.com/addons/detail/emoji/ejcgfbaipbelddlbokgcfajefbnnagfm" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Edge
                </a>
                <a href="https://addons.thunderbird.net/en-US/thunderbird/addon/emoji-sav/" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Thunderbird
                </a>
                <a href="https://www.emojiaddon.com/" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Visit website
                </a>
                <a href="https://github.com/Sav22999/emoji" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Emoji popup with a search box for keyword, colour or gender, category tabs and a row of most used emojis, under the heading “All emojis in a single add-on”">
                <img src="/images/projects/screenshots/emoji/1.png" alt="Emoji popup with a search box for keyword, colour or gender, category tabs and a row of most used emojis, under the heading “All emojis in a single add-on”" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Emoji popup in the dark theme, with the search box, category tabs and most used emojis">
                <img src="/images/projects/screenshots/emoji/2.png" alt="Emoji popup in the dark theme, with the search box, category tabs and most used emojis" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: People category in the Emoji popup with a highlighted row of Santa Claus emojis in different skin tones">
                <img src="/images/projects/screenshots/emoji/3.png" alt="People category in the Emoji popup with a highlighted row of Santa Claus emojis in different skin tones" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Emoji Settings panel with theme, language, columns, rows and emoji size options">
                <img src="/images/projects/screenshots/emoji/4.png" alt="Emoji Settings panel with theme, language, columns, rows and emoji size options" width="1280" height="800"
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
                <span class="timeline-version">v3.25</span>
                <span class="timeline-date">30 July 2025</span>
                <p>Updates emojis to the Emoji 16.0 standard, with minor improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.24</span>
                <span class="timeline-date">28 April 2025</span>
                <p>Adds more keywords, the wilted rose emoji and a tooltip that explains why a search result matched.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.23</span>
                <span class="timeline-date">12 November 2024</span>
                <p>Adds all Emoji 15.1 emojis, an improved UI and arrow-key navigation. Includes 3.23.1–3.23.1.2
                    (OpenMoji theme fix, Twemoji updated to 15.1, a fifth-birthday message and translation updates).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.22</span>
                <span class="timeline-date">30 April 2024</span>
                <p>Direct emoji insertion now also works in Chromium-based browsers. Includes 3.22.0.1–3.22.1 (emoji
                    style preview in Settings, new keywords and translations).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.21</span>
                <span class="timeline-date">29 January 2024</span>
                <p>Improvements and fixes, plus an option to change the toolbar icon in the Chromium version. Includes
                    3.21.1 (search by category, new keyword shortcut options and bug fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.20</span>
                <span class="timeline-date">20 November 2023</span>
                <p>Fixes some wrong emojis, updates Google Noto Color Emoji, adds keywords and improves the UI.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.19</span>
                <span class="timeline-date">14 August 2023</span>
                <p>Adds export and import, confirmations before clearing data or resetting settings, and a counter of
                    copied emojis. Includes 3.19.1–3.19.2 (fixes and a modernised UI).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.18</span>
                <span class="timeline-date">13 April 2023</span>
                <p>Makes search more precise, adds search by shortcode, colour and gender, and launches the
                    emojiaddon.com website. Includes 3.18.1 (missing emojis added, language picker without flags,
                    translations).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.17</span>
                <span class="timeline-date">24 March 2023</span>
                <p>Direct emoji insertion now works in many more kinds of fields. Includes 3.17.1.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.16</span>
                <span class="timeline-date">23 March 2023</span>
                <p>Released as 3.16.1: adds a customisable keyboard shortcut for the popup, new emojis and an emoji-size
                    preview in Settings.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.15</span>
                <span class="timeline-date">5 December 2022</span>
                <p>Adds many emojis, fixes several incorrect ones and updates translations.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.14</span>
                <span class="timeline-date">17 March 2022</span>
                <p>Adds the option to insert emojis directly into the page instead of only copying them. Includes
                    3.14.2–3.14.6 (Unicode 14 emojis, England, Scotland and Wales flags, and fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.13</span>
                <span class="timeline-date">26 July 2021</span>
                <p>Makes the add-on translatable on Crowdin; 3.13.1 adds about 20 languages. Includes 3.13.1–3.13.7.1
                    (more flags, Korean, an option to add spaces between multi-copied emojis, and fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.12</span>
                <span class="timeline-date">22 July 2021</span>
                <p>Adds quick skin-tone selection by right-clicking an emoji.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.11</span>
                <span class="timeline-date">1 May 2021</span>
                <p>Fixes multi-copy, adds tooltips to section titles and improves the UI. Includes 3.11.1–3.11.4 (option
                    to change the toolbar icon, and fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.10</span>
                <span class="timeline-date">4 February 2021</span>
                <p>Adds emoji name tooltips, an “I need help” button and auto-saving Settings. Includes 3.10.1–3.10.4
                    (faster loading and skin-tone changes without reopening the popup).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.9</span>
                <span class="timeline-date">18 January 2021</span>
                <p>Adds the Ctrl/Cmd+Alt+A shortcut, Enter to jump to search results and an in-app release notes
                    message. Includes 3.9.1–3.9.2.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.8</span>
                <span class="timeline-date">24 December 2020</span>
                <p>Adds multi-copy, to copy several emojis at once. Includes 3.8.1–3.8.2.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.7</span>
                <span class="timeline-date">9 October 2020</span>
                <p>Includes 3.7.1–3.7.2: 62 new Unicode 13 emojis, an updated OpenMoji Color font, the new OpenMoji
                    Black font and improved Settings.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.6</span>
                <span class="timeline-date">4 September 2020</span>
                <p>Lets you remove emojis from the most used list.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.5</span>
                <span class="timeline-date">21 August 2020</span>
                <p>Adds skin tones and the OpenMoji and system emoji fonts. Includes 3.5.1–3.5.2 (bug fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.4</span>
                <span class="timeline-date">1 August 2020</span>
                <p>Adds options to close the popup after copying and to choose the emoji font; the add-on is also
                    published on Microsoft Edge Add-ons.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.2</span>
                <span class="timeline-date">23 July 2020</span>
                <p>Adds a dark theme and a Settings page, with UI improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.1</span>
                <span class="timeline-date">16 July 2020</span>
                <p>Adds the Most used emojis section. Includes 3.1.1–3.1.3 (496 new emojis).</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v3.0</span>
                <span class="timeline-date">25 June 2020</span>
                <p>New icon, 274 new emojis in two new sections and a search box to find emojis by keyword.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">8 April 2020</span>
                <p>Adds a “Copied” message and other improvements.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0</span>
                <span class="timeline-date">29 January 2020</span>
                <p>Adds sections, hundreds of new emojis (905 in total) and the Twemoji font. Includes 2.0.1–2.0.2.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">30 November 2019</span>
                <p>First release.</p>
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
