<?php
$current_page = 'projects';
$title = 'Notefox';
$description = 'Notefox — take notes on any website, per page, per domain or globally, with sticky notes and optional sync across devices; private, open-source and available for Firefox, Chrome and Edge. A project by Saverio Morelli.';
$canonical = '/projects/notefox/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Notefox",
            "applicationCategory": "BrowserApplication",
            "operatingSystem": "Firefox, Chrome, Edge",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://www.notefox.eu",
            "softwareVersion": "5.0.0.2",
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
    <h1 class="section-title">Notefox</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/websites-notes.png" alt="Notefox icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Notefox is an open-source browser add-on for taking notes on websites: a note
                can belong to a single page, a whole domain, a sub-path or every site, with rich text formatting, tags
                and folders. Notes can be opened as movable sticky notes on the page and managed from a dedicated All
                notes page with search and filters, while an optional free Notefox Account syncs them across devices. It
                is available for Firefox (including Firefox for Android), Chrome and Edge.</p>
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
            </div>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Browser add-on</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Firefox, Chrome, Edge</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Users</span>
                    <span class="book-meta-value">~13,500</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v5.0.0.2</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2021 – present <span class="book-meta-note">· <?php $years = date('Y') - 2021; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://addons.mozilla.org/firefox/addon/websites-notes/" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Firefox
                </a>
                <a href="https://chromewebstore.google.com/detail/agcdffobijddcccbfnhfjmaohnljefpm" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Chrome
                </a>
                <a href="https://microsoftedge.microsoft.com/addons/detail/lkahmkadpaibphpoiofpdinacjffddda" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Edge
                </a>
                <a href="https://www.notefox.eu" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Visit website
                </a>
                <a href="https://github.com/Sav22999/websites-notes" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Notefox popup with Global, Domain and Page tabs, a formatted note and the formatting toolbar, next to the tagline “Take notes on every website in a smart and simple way!”">
                <img src="/images/projects/screenshots/notefox/1.png" alt="Notefox popup with Global, Domain and Page tabs, a formatted note and the formatting toolbar, next to the tagline “Take notes on every website in a smart and simple way!”" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Notefox Settings page with general options and keyboard shortcut settings, under the heading “Customise your experience, make your Notefox!”">
                <img src="/images/projects/screenshots/notefox/2.png" alt="Notefox Settings page with general options and keyboard shortcut settings, under the heading “Customise your experience, make your Notefox!”" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: All notes page listing notes for notefox.eu with search, filter, edit, copy and clear buttons">
                <img src="/images/projects/screenshots/notefox/3.png" alt="All notes page listing notes for notefox.eu with search, filter, edit, copy and clear buttons" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Two Notefox popups explaining the toolbar icon: green when at least one note is found, orange when there are none">
                <img src="/images/projects/screenshots/notefox/4.png" alt="Two Notefox popups explaining the toolbar icon: green when at least one note is found, orange when there are none" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Annotated Notefox popup pointing out the Global, Domain, Page and sub-path tabs, images, text styles, sticky-note button and All notes">
                <img src="/images/projects/screenshots/notefox/5.png" alt="Annotated Notefox popup pointing out the Global, Domain, Page and sub-path tabs, images, text styles, sticky-note button and All notes" width="1280" height="800"
                     loading="lazy">
            </button>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Changelog</h2>
        <div class="timeline-list">
            <div class="timeline-item major">
                <span class="timeline-version">v5.0</span>
                <span class="timeline-date">6 October 2026</span>
                <p>Introduces Notefox Account v2 with merge-based sync, web access to synced notes, sync history,
                    two-factor authentication and session management, plus multiple notes per type and multiple pinnable
                    sticky notes. Includes 5.0.0.1–5.0.0.2.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.7</span>
                <span class="timeline-date">9 April 2026</span>
                <p>Adds folders, tags as text, ordered and unordered lists, default sticky-note properties and editable
                    page/domain links in All notes; 4.7.1 adds the sidebar view. Includes 4.7.1–4.7.2.2 (translation
                    updates, a donation message and a fix for IME input).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.6</span>
                <span class="timeline-date">4 December 2025</span>
                <p>New icon and default font, resizable popup, adjustable text size, a clear-formatting button,
                    fullscreen in All notes and developer options with a custom API endpoint. Includes 4.6.0.1–4.6.1
                    (link fix and translation updates).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.5</span>
                <span class="timeline-date">23 July 2025</span>
                <p>Adds options to colour the toolbar icon and notes by tag colour, a paste-without-formatting shortcut,
                    optional anonymous telemetry and error-log sending, and sidebar and sticky-note improvements.
                    Includes 4.5.0.1–4.5.5.3 (offline handling for Notefox Account and bug fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.4</span>
                <span class="timeline-date">18 April 2025</span>
                <p>Adds debugging tools, an error-log feature, code-block and highlight formatting buttons and a privacy
                    consent screen requested by Mozilla. Includes 4.4.1–4.4.5.5, which fixed a Firefox crash and several
                    smaller bugs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.3</span>
                <span class="timeline-date">11 March 2025</span>
                <p>Extends sub-path (“•••”) URLs to any combination of URL parameters, improves section detection and
                    fixes the priority of sub-path notes. Includes 4.3.1–4.3.2.1 (bug fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.2</span>
                <span class="timeline-date">8 February 2025</span>
                <p>Adds more font families with preview, saving and searching page content, custom date/time formats, an
                    option to hide Undo/Redo and refreshed tabs. Includes 4.2.0.1–4.2.1 (sticky notes in the mobile
                    version).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.1</span>
                <span class="timeline-date">19 July 2024</span>
                <p>Adds the mobile version for Firefox for Android and two new themes, darker and lighter. Includes
                    4.1.0.1–4.1.0.4; 4.1.0.4 brought the new version to the Chrome Web Store and Edge Add-ons.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v4.0</span>
                <span class="timeline-date">4 May 2024</span>
                <p>Completely redesigned popup, Settings and All notes, and introduces the Notefox Account to sync notes
                    across devices, plus new formatting buttons, sticky-note themes and editable note titles. Includes
                    4.0.1–4.0.1.5 (immersive sticky notes, Interlingua).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.11</span>
                <span class="timeline-date">18 March 2024</span>
                <p>Switches notes to a handwriting font and adds an insert/remove link button. Includes 3.11.1–3.11.2.2
                    (option to disable the Shantell Sans font).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.10</span>
                <span class="timeline-date">19 February 2024</span>
                <p>Brings sticky notes to Chromium-based browsers. Includes 3.10.0.1–3.10.1 (option to check notes on
                    all supported protocols).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.9</span>
                <span class="timeline-date">3 November 2023</span>
                <p>Saves sticky-note size, opacity and position per page. Includes 3.9.1–3.9.3, which added clickable
                    links, more tag colours and changing the tag colour from the popup.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.8</span>
                <span class="timeline-date">25 October 2023</span>
                <p>Fixes abnormal CPU usage and adds export to and import from a file.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.7</span>
                <span class="timeline-date">14 October 2023</span>
                <p>Adds sub-path notes and rich text: bold, italic, underline, images and undo/redo. Includes
                    3.7.1–3.7.12 (spellcheck, more HTML tags, a word-wrap option, multi-keyword search and sticky-note
                    fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.6</span>
                <span class="timeline-date">12 October 2023</span>
                <p>Improves search and fixes an important storage bug.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.5</span>
                <span class="timeline-date">5 October 2023</span>
                <p>Improves search, adds Page notes for moz-extension pages and makes some permissions optional.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.4</span>
                <span class="timeline-date">21 August 2023</span>
                <p>Adds Global notes, light and dark themes, sorting and filtering in All notes and a welcome page.
                    Includes 3.4.0.1–3.4.1.3 (note titles and notes on non-HTTP(S) pages).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.3</span>
                <span class="timeline-date">11 August 2023</span>
                <p>Moves notes to synced storage so they sync through a Firefox Account. Includes 3.3.1–3.3.2 (minimise
                    and restore for sticky notes, toolbar icon fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.2</span>
                <span class="timeline-date">11 August 2023</span>
                <p>Sticky notes follow the default Domain/Page preference. Includes 3.2.1–3.2.1.1 (sticky notes show
                    whether they belong to a page or a domain).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.1</span>
                <span class="timeline-date">11 August 2023</span>
                <p>Sticky notes become available for domain notes too.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v3.0</span>
                <span class="timeline-date">11 August 2023</span>
                <p>Introduces sticky notes on pages, with adjustable size, position and opacity, plus a refreshed UI and
                    icons. Includes 3.0.1.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.3</span>
                <span class="timeline-date">21 March 2023</span>
                <p>Adds keyboard shortcuts to open the popup on the default, domain or page tab. Includes 2.3.1–2.3.2
                    (bug fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2</span>
                <span class="timeline-date">17 November 2022</span>
                <p>Adds a new option in Settings and other improvements. Includes 2.2.1–2.2.2 (bug fix).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">18 October 2022</span>
                <p>Adds a search box and filters to All notes. Includes 2.1.1 (better search and a Crowdin translation
                    button).</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0</span>
                <span class="timeline-date">13 October 2022</span>
                <p>Adds a Settings page, improves import and export, and introduces the orange colour scheme and a new
                    icon.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6</span>
                <span class="timeline-date">20 September 2021</span>
                <p>Translation updates, including Spanish (1.6.0.2). Includes 1.6.0.1–1.6.0.2.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5</span>
                <span class="timeline-date">20 September 2021</span>
                <p>No release notes available. Includes 1.5.1.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">2 August 2021</span>
                <p>Adds clearing notes for a single page or a whole domain and a different toolbar icon when notes
                    exist. Includes 1.3.0.1–1.3.2.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">1 August 2021</span>
                <p>Adds import and export, a confirmation before clearing all notes and a new icon. Includes 1.2.1.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">31 July 2021</span>
                <p>Adds the dedicated All notes page, plus bug fixes.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">30 July 2021</span>
                <p>First release. Includes 1.0.1.</p>
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
