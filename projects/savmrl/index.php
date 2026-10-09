<?php
$current_page = 'projects';
$title = 'savmrl.it';
$description = 'savmrl.it — free, anonymous link shortener and redirect service: no registration, with optional expiry dates, click limits and access codes. A project by Saverio Morelli.';
$canonical = '/projects/savmrl/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "savmrl.it",
            "applicationCategory": "WebApplication",
            "operatingSystem": "Web",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://savmrl.it",
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
    <h1 class="section-title">savmrl.it</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/savmrl.png" alt="savmrl.it icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">savmrl.it is a free, anonymous URL shortener and redirect service that works
                without registration or an account. Shortened links can have an expiry date, a maximum number of
                openings, a custom name and an access code. A statistics page shows how many times each link has been
                opened, and a QR code is generated for every link. It also offers a simple API and companion browser
                add-ons for Firefox, Chrome and Edge.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Web service</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Add-on users</span>
                    <span class="book-meta-value">~5</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2024 – present <span class="book-meta-note">· <?php $years = date('Y') - 2024; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://savmrl.it" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Visit website
                </a>
                <a href="https://addons.mozilla.org/en-GB/firefox/addon/savmrl-it/" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Firefox
                </a>
                <a href="https://chromewebstore.google.com/detail/savmrlit/pkbjoeedhjjokllfhgcapbbihianeemn" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Chrome
                </a>
                <a href="https://microsoftedge.microsoft.com/addons/detail/jdgcfpdoiojfebhafmihkihficfnpahk" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Install on Edge
                </a>
                <a href="https://github.com/Sav22999/savmrl.it" target="_blank" rel="noopener"
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
        <h2 class="addon-screenshots-title">Screenshots <span class="section-title-note">· savmrl.it companion (browser add-on)</span></h2>
        <div class="screenshot-grid">
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: savmrl.it companion promo banner with the savmrl.it logo and the name Saverio Morelli">
                <img src="/images/projects/screenshots/savmrl/addon-1.png" alt="savmrl.it companion promo banner with the savmrl.it logo and the name Saverio Morelli" width="1024" height="500"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Firefox toolbar with the savmrl.it companion icon highlighted and the caption “Click the icon on toolbar”">
                <img src="/images/projects/screenshots/savmrl/addon-2.png" alt="Firefox toolbar with the savmrl.it companion icon highlighted and the caption “Click the icon on toolbar”" width="1280" height="800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: savmrl.it opened by the add-on with the current page already shortened, buttons to copy the link, generate another one or see click statistics, and a QR code">
                <img src="/images/projects/screenshots/savmrl/addon-3.png" alt="savmrl.it opened by the add-on with the current page already shortened, buttons to copy the link, generate another one or see click statistics, and a QR code" width="1280" height="800"
                     loading="lazy">
            </button>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Changelog <span class="section-title-note">· savmrl.it companion (browser add-on)</span></h2>
        <div class="timeline-list">
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">13 January 2024</span>
                <p>First release: one click on the toolbar icon opens savmrl.it with the link of the current page
                    already shortened. Available for Firefox, Chrome and Edge.</p>
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
