<?php
$current_page = 'projects';
$title = 'Accented Letters';
$description = 'Accented Letters — open-source browser add-on by Saverio Morelli to copy accented and special characters with a single click.';
$canonical = '/projects/accented-letters/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Accented Letters",
            "applicationCategory": "BrowserApplication",
            "operatingSystem": "Firefox, Chrome, Edge",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/accented-letters-addons",
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
    <h1 class="section-title">Accented Letters</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/accented-letters.png" alt="Accented Letters icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Browser add-on to copy accented and special characters with a single click.
                Supports 66+ characters with translations in multiple languages.</p>
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
                    <span class="book-meta-value">~690</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v3.0</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2019 – present <span class="book-meta-note">· <?php $years = date('Y') - 2019; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://addons.mozilla.org/firefox/addon/accented-letters/" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Firefox
                </a>
                <a href="https://chromewebstore.google.com/detail/accented-letters/ebngeaihhcglhedbgaogppeeofhhbdpm"
                   target="_blank" rel="noopener" class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Chrome
                </a>
                <a href="https://microsoftedge.microsoft.com/addons/detail/accented-letters/daabdiglenlnegnicakpnijaocjdjenp"
                   target="_blank" rel="noopener" class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Edge
                </a>
                <a href="https://github.com/Sav22999/accented-letters-addons" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Accented Letters popup with lowercase accented characters ready to copy">
                <img src="/images/projects/screenshots/accented-letters/1.png" alt="Accented Letters popup with lowercase accented characters ready to copy" width="1280" height="800"
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
                <span class="timeline-version">v3.0</span>
                <span class="timeline-date">26 May 2026</span>
                <p>A fully refreshed version: new settings to customise the add-on, custom characters, predefined character sets, default visibility and case, monospace or sans-serif typeface, import/export of settings, and a new search bar.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">16 December 2023</span>
                <p>Published on Google Chrome Web Store and Microsoft Edge Addons.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">28 June 2021</span>
                <p>Improvements to the UI.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0</span>
                <span class="timeline-date">16 July 2020</span>
                <p>New icon, 40 new characters (total 66), and several improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">8 April 2020</span>
                <p>Added a "Copied" message after copying a character.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.1</span>
                <span class="timeline-date">7 January 2020</span>
                <p>Added Polish and Dutch translations.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">9 December 2019</span>
                <p>Added translations for titles in many languages (English, French, Spanish, Italian, German, Portuguese, Chinese, Japanese, Arabic).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.2</span>
                <span class="timeline-date">1 December 2019</span>
                <p>Fixed the UI.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.1</span>
                <span class="timeline-date">29 November 2019</span>
                <p>Small improvements to the UI.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">23 November 2019</span>
                <p>New icon, support for Firefox light and dark themes, UI closer to Firefox design, new fonts.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">18 April 2019</span>
                <p>First release of the add-on.</p>
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
