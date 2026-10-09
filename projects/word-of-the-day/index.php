<?php
$current_page = 'projects';
$title = 'Word of the Day';
$description = 'Word of the Day — learn a new word every day with its definition, etymology and pronunciation. An Android app. A discontinued project by Saverio Morelli.';
$canonical = '/projects/word-of-the-day/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Word of the Day",
            "applicationCategory": "EducationalApplication",
            "operatingSystem": "Android",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/word-of-the-day",
            "softwareVersion": "2.0",
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
    <h1 class="section-title">Word of the Day</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/word-of-the-day.png" alt="Word of the Day icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Word of the Day was a free, open-source Android app that showed a new word
                every day with its definition, etymology and RP-IPA phonetic transcription, ready to copy or share. It
                could send an optional daily notification at 07:00 and kept a “Words learnt” history of past words.</p>
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
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v2.0</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2021 – 2026 <span class="book-meta-note">· 5 years</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/Sav22999/word-of-the-day" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Store graphic reading “Every day a new word” next to a tilted phone showing the word of the day “Inheritance” with its definition and etymology">
                <img src="/images/projects/screenshots/word-of-the-day/1.png" alt="Store graphic reading “Every day a new word” next to a tilted phone showing the word of the day “Inheritance” with its definition and etymology" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Store graphic reading “Phonetics” next to the app showing the date, the word with its IPA transcription and the settings icon">
                <img src="/images/projects/screenshots/word-of-the-day/2.png" alt="Store graphic reading “Phonetics” next to the app showing the date, the word with its IPA transcription and the settings icon" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Final part of the store graphic: the blue wave ending on a white background">
                <img src="/images/projects/screenshots/word-of-the-day/3.png" alt="Final part of the store graphic: the blue wave ending on a white background" width="919" height="1800"
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
                <span class="timeline-version">v2.0</span>
                <span class="timeline-date">15 October 2024</span>
                <p>Added the Words learnt history of previously shown words, along with many improvements and bug fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5</span>
                <span class="timeline-date">10 November 2023</span>
                <p>Updated the app icon and made some improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.1</span>
                <span class="timeline-date">12 October 2021</span>
                <p>First version published on F-Droid.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4</span>
                <span class="timeline-date">3 October 2021</span>
                <p>Removed ads, improved some graphic details and notifications, and fixed a bug.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.3</span>
                <span class="timeline-date">25 June 2021</span>
                <p>Shows the app version in Settings and sends the 07:00 daily notification every day.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.2</span>
                <span class="timeline-date">24 June 2021</span>
                <p>Fixed notifications and connection retries, shortened the splash screen and added a message when the
                    app is offline and showing the last downloaded word.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.1</span>
                <span class="timeline-date">29 January 2021</span>
                <p>Fixed the duplicate launcher icon and improved the push notification system.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">29 January 2021</span>
                <p>Added daily push notifications.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">26 January 2021</span>
                <p>Added a splash screen and the source of each word, plus general improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">21 January 2021</span>
                <p>Added an Internet connection check with error handling, offline restore of the day’s word and an
                    option to turn off ads in Settings.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">20 January 2021</span>
                <p>First release: a new word every day with definition, etymology and phonetics, which can be copied or
                    shared.</p>
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
