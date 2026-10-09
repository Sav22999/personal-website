<?php
$current_page = 'projects';
$title = 'Sav PDF Viewer';
$description = 'Sav PDF Viewer — private, open-source PDF reader for Android with no ads, no permissions and no tracking, downloaded more than 50,000 times on Google Play. A project by Saverio Morelli.';
$canonical = '/projects/sav-pdf-viewer/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Sav PDF Viewer",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "Android",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://www.savpdfviewer.com/",
            "softwareVersion": "2.5.1",
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
    <h1 class="section-title">Sav PDF Viewer</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/sav-pdf-viewer-pro.png" alt="Sav PDF Viewer icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Sav PDF Viewer (listed on Google Play as &quot;Sav PDF Viewer Pro&quot;) is a
                lightweight, open-source PDF reader for Android that needs no permissions, shows no ads and collects no
                data. It reopens each file where you left off and supports bookmarks, text search and selection,
                password-protected PDFs (including AES-256), night mode, links inside documents, printing and sharing.
                It is available on Google Play, IzzyOnDroid and GitHub, and is translated into many languages via
                Crowdin.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Android app</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Android</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Downloads</span>
                    <span class="book-meta-value">90,000+</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v2.5.1</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2021 – present <span class="book-meta-note">· <?php $years = date('Y') - 2021; echo $years === 0 ? 'less than a year' : $years . ($years === 1 ? ' year' : ' years'); ?></span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://play.google.com/store/apps/details?id=com.saverio.pdfviewer" target="_blank" rel="noopener"
                   class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Get it on Google Play
                </a>
                <a href="https://apt.izzysoft.de/fdroid/index/apk/com.saverio.pdfviewer" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Get it on IzzyOnDroid
                </a>
                <a href="https://www.savpdfviewer.com/" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Visit website
                </a>
                <a href="https://github.com/Sav22999/sav-pdf-viewer-pro" target="_blank" rel="noopener"
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
        <div class="screenshot-grid portrait">
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Promotional graphic with the word &quot;Safe&quot; and a padlock shield beside a tilted phone showing a PDF page open in the app">
                <img src="/images/projects/screenshots/sav-pdf-viewer/1.png" alt="Promotional graphic with the word &quot;Safe&quot; and a padlock shield beside a tilted phone showing a PDF page open in the app" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Promotional graphic with the word &quot;Lightweight&quot; and two crossed phones: one shows a PDF with the page counter and a bookmark, the other shows the red menu panel">
                <img src="/images/projects/screenshots/sav-pdf-viewer/2.png" alt="Promotional graphic with the word &quot;Lightweight&quot; and two crossed phones: one shows a PDF with the page counter and a bookmark, the other shows the red menu panel" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Promotional graphic with the word &quot;Simple&quot; and a PDF logo beside a phone showing the app&#x27;s menu with share, help and zoom options">
                <img src="/images/projects/screenshots/sav-pdf-viewer/3.png" alt="Promotional graphic with the word &quot;Simple&quot; and a PDF logo beside a phone showing the app&#x27;s menu with share, help and zoom options" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Text graphic reading &quot;Your privacy is important. You are important. Full stop.&quot; signed by Saverio Morelli">
                <img src="/images/projects/screenshots/sav-pdf-viewer/4.png" alt="Text graphic reading &quot;Your privacy is important. You are important. Full stop.&quot; signed by Saverio Morelli" width="919" height="1800"
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
                <span class="timeline-version">v2.5</span>
                <span class="timeline-date">8 October 2026</span>
                <p>Added a high-contrast option and a choice of accent colour in Settings. Includes 2.5–2.5.1 (2.5.1, a
                    minor high-contrast fix, was published on Google Play on 9 October 2026).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.4</span>
                <span class="timeline-date">20 August 2026</span>
                <p>Tapping the page now toggles the toolbar, plus minor fixes. Includes 2.4–2.4.0.2 (Settings reordered
                    to match the toolbox menu, a refreshed app icon).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.3</span>
                <span class="timeline-date">13 August 2026</span>
                <p>Search now supports multi-word queries, with improvements to the navigation bar and other minor
                    improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2</span>
                <span class="timeline-date">8 July 2026</span>
                <p>Added support for AES-256-protected PDFs and separate per-ABI builds. Includes 2.2–2.2.2 (fixes for
                    the panel sitting behind the navigation bar on Android 15+, slow loading of all pages at startup and
                    double-byte characters in file names).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">6 July 2026</span>
                <p>Switched to a FOSS PDF library, removed dependency libraries and unnecessary permissions, and added a
                    Print button in the top bar. Also fixed bottom-up and right-to-left swiping.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0</span>
                <span class="timeline-date">25 March 2026</span>
                <p>Major update with text selection and copy, a search feature with filters and a new Settings page,
                    plus many bug fixes and improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.17</span>
                <span class="timeline-date">12 March 2026</span>
                <p>Added a search feature in the menu panel and fixed some bugs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.16</span>
                <span class="timeline-date">6 August 2025</span>
                <p>Added a lock-rotation option in the menu panel and moved the Share button to the top bar.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.15</span>
                <span class="timeline-date">31 October 2023</span>
                <p>New app icon with support for the monochrome (themed) icon style. Includes 1.15–1.15.1.2 (tap the PDF
                    to show the top bar, fixes to the light and forced-dark filters, a confirmation dialog before
                    opening links, signed release APK on GitHub).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.14</span>
                <span class="timeline-date">27 October 2023</span>
                <p>Added support for links in PDFs, horizontal scrolling, a forced-dark filter and single-page mode.
                    Includes 1.14–1.14.2 (bug fixes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.13</span>
                <span class="timeline-date">1 August 2023</span>
                <p>Any opened file can now be shared. Includes 1.13–1.13.4 (fixed a crash when opening PDFs from other
                    apps and a black screen with PDFs opened from Gmail).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.12</span>
                <span class="timeline-date">6 June 2023</span>
                <p>Added zoom controls in the menu panel and fixed bugs when changing orientation. Includes
                    1.12–1.12.0.2 (updated icons and SDK changes).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.11</span>
                <span class="timeline-date">12 April 2023</span>
                <p>Tapping anywhere on the page now hides or shows the top bar, with improved gestures and an updated
                    PDF library. Includes 1.11–1.11.1 (zoom bug fix).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.10</span>
                <span class="timeline-date">27 March 2023</span>
                <p>Added a scrollbar button for faster navigation and improved bookmark gestures. Includes 1.10–1.10.4
                    (an All bookmarks button, page numbers while scrolling, a Get help button and an improved night
                    theme).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.9</span>
                <span class="timeline-date">24 April 2022</span>
                <p>Added bookmarks, which can be added, removed and managed, plus backend and UI improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.8</span>
                <span class="timeline-date">1 October 2021</span>
                <p>Added a menu panel so the app works well on small screens, plus performance improvements. Includes
                    1.8–1.8.0.3 (a small bug fix, the first F-Droid release and updated translations).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.7</span>
                <span class="timeline-date">30 September 2021</span>
                <p>The top bar now hides after 5 seconds of inactivity.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6</span>
                <span class="timeline-date">11 June 2021</span>
                <p>Added a night-light mode. Includes 1.6–1.6.4 (fit-to-width and page-restore fixes when rotating, a
                    sharing fix and new translations).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5</span>
                <span class="timeline-date">8 June 2021</span>
                <p>Added support for password-protected PDFs and a Go to top button. Includes 1.5–1.5.1 (an Open new
                    file button).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4</span>
                <span class="timeline-date">7 June 2021</span>
                <p>Added a total-pages counter and fixed an orientation bug. Includes 1.4–1.4.2 (a fix for restoring the
                    last position and a Go to page feature).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">6 June 2021</span>
                <p>Added a top bar and full-screen mode.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">1 May 2021</span>
                <p>Fixed a crash on Android 11.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">11 February 2021</span>
                <p>Added a Share button and a review prompt (released as 1.1.1).</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">20 January 2021</span>
                <p>First release of the app.</p>
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
