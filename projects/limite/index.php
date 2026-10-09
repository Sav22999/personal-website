<?php
$current_page = 'projects';
$title = 'Limite';
$description = 'Limite — open-source browser add-on by Saverio Morelli that tracks how much time you spend on each website, daily.';
$canonical = '/projects/limite/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Limite",
            "applicationCategory": "BrowserApplication",
            "operatingSystem": "Firefox",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/limite",
            "softwareVersion": "3.0.2",
            "dateModified": "2026-06-25",
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
    <h1 class="section-title">Limite</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/limite.png" alt="Limite icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Browser add-on that tracks how much time you spend on each website, daily. View
                detailed statistics, categorise websites, customise the add-on from its settings, import/export data, and
                get notified about your browsing habits. Available in English, German, Italian, French and Russian.</p>
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
                    <span class="book-meta-value">~30</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v3.0.2</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2021 – present <span class="book-meta-note">· <?php $years = date('Y') - 2021; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://addons.mozilla.org/firefox/addon/limite/" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Firefox
                </a>
                <a href="https://github.com/Sav22999/limite" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Limite popup showing the time spent today and since install on the current website">
                <img src="/images/projects/screenshots/limite/1.png" alt="Limite popup showing the time spent today and since install on the current website" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: "All time spent" page listing every visited website with status, category and daily time">
                <img src="/images/projects/screenshots/limite/2.png" alt=""All time spent" page listing every visited website with status, category and daily time" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Limite settings: visible columns, display interval, default sorting and tracking of new websites">
                <img src="/images/projects/screenshots/limite/3.png" alt="Limite settings: visible columns, display interval, default sorting and tracking of new websites" width="1280" height="800"
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
                <span class="timeline-version">v3.0.2</span>
                <span class="timeline-date">25 June 2026</span>
                <p>Minor improvements and fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.0.1</span>
                <span class="timeline-date">25 June 2026</span>
                <p>Fixed the width of the website column. Minor fixes and improvements.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v3.0</span>
                <span class="timeline-date">25 June 2026</span>
                <p>Refreshed "time spent" page, new settings to customise the add-on (visible columns and days, default sorting, infinite scrolling), resizable website column, websites list per category, faster loading of websites. Translated into German, Italian, French and Russian.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2</span>
                <span class="timeline-date">16 October 2023</span>
                <p>Added new column: average. Fixed a bug with sort-by-category.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1.2</span>
                <span class="timeline-date">8 October 2023</span>
                <p>Long URLs are shortened to 46 characters to avoid UI problems (hover to see the full URL).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1.1</span>
                <span class="timeline-date">8 October 2023</span>
                <p>The selected sorting is now kept when navigating days, searching and refreshing data.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">8 October 2023</span>
                <p>Added search and sorting by any column in "All websites". Added "Cloud" category and new default websites. Bug fixes and back-end improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.0.1</span>
                <span class="timeline-date">13 September 2023</span>
                <p>The timer now pauses when the browser loses focus or is minimised.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0.0.1</span>
                <span class="timeline-date">13 September 2023</span>
                <p>Revisited UI of "All time spent", 7-day view, icons in buttons, status toggle and categories. Many improvements and bug fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.1.2</span>
                <span class="timeline-date">12 February 2022</span>
                <p>Updated PayPal link.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.1.1</span>
                <span class="timeline-date">9 August 2021</span>
                <p>Fixed strings.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.1</span>
                <span class="timeline-date">3 August 2021</span>
                <p>Added "delete" button for each website, wider "All time spent" page, dates sorted from the most recent. Bug fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">3 August 2021</span>
                <p>Added "All time spent" page, import/export/delete features. Fixed a bug.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.1</span>
                <span class="timeline-date">19 July 2021</span>
                <p>Fixed a small bug.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">10 July 2021</span>
                <p>New icon, notifications, and badge.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.2</span>
                <span class="timeline-date">3 June 2021</span>
                <p>Fixed some important bugs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.1</span>
                <span class="timeline-date">29 May 2021</span>
                <p>Fixed an issue.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">26 May 2021</span>
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
