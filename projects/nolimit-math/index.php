<?php
$current_page = 'projects';
$title = 'NoLimit Math';
$description = 'NoLimit Math — open-source desktop software for calculating mathematical limits and generating function plots. A discontinued project by Saverio Morelli.';
$canonical = '/projects/nolimit-math/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "NoLimit Math",
            "applicationCategory": "EducationalApplication",
            "operatingSystem": "Windows, Linux",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/project_nolimit_math",
            "softwareVersion": "1.0.5 beta",
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
    <h1 class="section-title">NoLimit Math</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/nolimit-math.png" alt="NoLimit Math icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">NoLimit Math was an open-source desktop app, written in Python with a PyQt5
                interface, that calculated limits of functions and drew the matching graph with Matplotlib. It came in
                two flavours: a &#x27;normal&#x27; version with its own solver for rational functions up to the second
                degree, and a version built on the SymPy library that handled any function. Saverio started it in 2018
                for his high-school final exam (the &#x27;Oltre i limiti&#x27; paper), developed it with Simone Massaro,
                and published it on PyPI as the nolimit package.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Desktop software</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Windows, Linux</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v1.0.5 beta</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2018 <span class="book-meta-note">· less than a year</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://sav22999.github.io/project_nolimit_math/" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Visit website
                </a>
                <a href="https://github.com/Sav22999/project_nolimit_math" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: NoLimit Math normal version calculating the limit of (x^2+5)/1 as x approaches 2, showing the result 9.0 next to the plotted parabola">
                <img src="/images/projects/screenshots/nolimit-math/1.png" alt="NoLimit Math normal version calculating the limit of (x^2+5)/1 as x approaches 2, showing the result 9.0 next to the plotted parabola" width="934" height="550"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: NoLimit Math SymPy version with Live calculation turned on, showing the limit of x**2+5 as x approaches 2 equals 9 with its graph">
                <img src="/images/projects/screenshots/nolimit-math/2.png" alt="NoLimit Math SymPy version with Live calculation turned on, showing the limit of x**2+5 as x approaches 2 equals 9 with its graph" width="934" height="550"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Detailed graph window of NoLimit Math, a zoomable Matplotlib plot of the function">
                <img src="/images/projects/screenshots/nolimit-math/3.png" alt="Detailed graph window of NoLimit Math, a zoomable Matplotlib plot of the function" width="770" height="681"
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
                <span class="timeline-version">v1.0.5 beta</span>
                <span class="timeline-date">26 June 2018</span>
                <p>Both versions moved to 1.0.5β. The SymPy version got an updated interface and now shows ∞ instead of
                    inf/oo for x₀. A later fix (3 July 2018) corrected the detailed plot in the SymPy version.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.4 beta</span>
                <span class="timeline-date">26 June 2018</span>
                <p>No release notes available.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.1 beta</span>
                <span class="timeline-date">25 June 2018</span>
                <p>The SymPy version now accepts ^, inf and ∞ in input, can show results as decimal numbers and has
                    basic support for the detailed plot.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0 beta</span>
                <span class="timeline-date">19 June 2018</span>
                <p>Version numbering restarted at 1.0β. The normal and SymPy versions got a matching new interface, the
                    project was packaged as the &#x27;nolimit&#x27; Python package (commands nolimit and nolimit_sympy),
                    and the NoLimit Math website went live.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4</span>
                <span class="timeline-date">16 June 2018</span>
                <p>Added &#x27;Live calculation&#x27; mode, which computes the limit and redraws the graph while you
                    type. The SymPy version was updated to match.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">11 June 2018</span>
                <p>Updated the normal version and the screenshots. The SymPy version gained graph plotting, except for
                    constant functions.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">10 June 2018</span>
                <p>Introduced the SymPy version (&#x27;1.2 S&#x27;), which calculates limits with the SymPy library and
                    has basic error handling.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1 beta</span>
                <span class="timeline-date">9 June 2018</span>
                <p>No release notes available.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.10</span>
                <span class="timeline-date">8 June 2018</span>
                <p>Added the application icon.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.9</span>
                <span class="timeline-date">8 June 2018</span>
                <p>No release notes available.</p>
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
