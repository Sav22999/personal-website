<?php
$current_page = 'projects';
$title = 'My Code Editor';
$description = 'My Code Editor — free, lightweight HTML and CSS editor for Windows with syntax highlighting, live preview and many built-in tools. A discontinued project by Saverio Morelli.';
$canonical = '/projects/my-code-editor/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "My Code Editor",
            "applicationCategory": "DeveloperApplication",
            "operatingSystem": "Windows",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/mycodeeditor",
            "softwareVersion": "5.1.2.0",
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
    <h1 class="section-title">My Code Editor</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/my-code-editor.png" alt="My Code Editor icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">My Code Editor, originally called Minion One, was a free Windows editor for
                building websites in HTML and CSS. It had syntax highlighting, a live project preview, guides, HTML
                insertion tools and auto-save, and it also opened other languages such as PHP, JavaScript, C# and
                VB.NET. Saverio wrote it in VB.NET as a hobby project, starting at age 12. The first beta came out in
                2014 and the last version, 5.1.2.0, was published on GitHub in 2018.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Desktop software</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Windows</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v5.1.2.0</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2014 – 2018 <span class="book-meta-note">· 4 years</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/Sav22999/mycodeeditor" target="_blank" rel="noopener"
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
                <span class="timeline-version">v5.1.2.0</span>
                <span class="timeline-date">21 November 2018</span>
                <p>No release notes available.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.0.1.2</span>
                <span class="timeline-date">26 June 2016</span>
                <p>Fixed bugs in Recent files, in New project / New project + stylesheet and in the update checker.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.0.1.1</span>
                <span class="timeline-date">24 June 2016</span>
                <p>Fixed bugs in the update checker and in the project preview, and added a progress bar to the update
                    checker.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.0.1.0</span>
                <span class="timeline-date">24 June 2016</span>
                <p>Fixed many bugs, made Find and Replace work across multiple lines, let HTML tools reuse the selected
                    text and added a strikethrough quick tag. The minimum window size went up to 584×430.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v4.0.0.1</span>
                <span class="timeline-date">22 June 2016</span>
                <p>Fixed some errors.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v4.0.0.0</span>
                <span class="timeline-date">22 June 2016</span>
                <p>New interface and features: edit the stylesheet at the same time as the page, written and video
                    tutorials, W3Schools suggestions, recent files and auto-save.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.0.2.2</span>
                <span class="timeline-date">20 June 2015</span>
                <p>Fixed numerous bugs from the previous version.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v3.0.2.1</span>
                <span class="timeline-date">1 June 2015</span>
                <p>Added icons to some menu items.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v3.0.0.0</span>
                <span class="timeline-date">17 May 2015</span>
                <p>Fully redesigned look with a toolbar, full-screen mode, a guided project wizard and support for being
                    the default program. Settings are now saved to an external file, updates and the preview were
                    improved, and C# and VB.NET were added.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.0.0.1</span>
                <span class="timeline-date">6 March 2015</span>
                <p>Fixed some errors, improved the update process (now with more details) and improved general
                    performance.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0.0.0</span>
                <span class="timeline-date">1 December 2014</span>
                <p>Added Settings with customisation options, an in-app feedback form, terms of use and a What&#x27;s
                    New section. Performance, windows and update checking were improved, and the icon changed.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.2.0</span>
                <span class="timeline-date">25 September 2014</span>
                <p>Fixed some errors and problems, and made startup faster.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.0.5</span>
                <span class="timeline-date">25 September 2014</span>
                <p>Fixed the &#x27;TXT – New Document&#x27; button.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.0.4</span>
                <span class="timeline-date">28 August 2014</span>
                <p>Fixed the Link window and improved the startup window.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.0.3</span>
                <span class="timeline-date">10 August 2014</span>
                <p>Fixed the Save button.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.0.2</span>
                <span class="timeline-date">3 August 2014</span>
                <p>Improved stability and added a character counter (letters, numbers and symbols), shortcuts in the
                    Design window, and PHP and JavaScript support (also on the startup window).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.0.1</span>
                <span class="timeline-date">16 June 2014</span>
                <p>Fixed some errors.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0.0.0 beta</span>
                <span class="timeline-date">24 May 2014</span>
                <p>First public beta, released as Minion One under the MixiM name.</p>
            </div>
        </div>
    </div>
</section>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
