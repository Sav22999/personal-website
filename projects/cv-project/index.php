<?php
$current_page = 'projects';
$title = 'CV Project';
$description = 'CV Project — unofficial Android app for contributing to Mozilla Common Voice, the open-source voice dataset, by recording sentences and validating clips. A discontinued project by Saverio Morelli.';
$canonical = '/projects/cv-project/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "CV Project",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "Android",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/cv-project-app/common-voice-app",
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
    <p class="section-label section-label-discontinued">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="21 8 21 21 3 21 3 8"/>
            <rect x="1" y="3" width="22" height="5"/>
            <line x1="10" y1="12" x2="14" y2="12"/>
        </svg>
        Discontinued project
    </p>
    <h1 class="section-title">CV Project</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/cv-project.png" alt="CV Project icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">CV Project (originally released as Common Voice Android, later renamed CV
                Android) was an unofficial, open-source Android app for contributing to Mozilla Common Voice from a
                smartphone. Users could record sentences (Speak) and validate other people’s clips (Listen), even
                offline. It also added features the mobile website lacked, such as a daily goal with notifications,
                gestures, a dark theme, statistics and leaderboards, and it was translated into dozens of languages on
                Crowdin. Development stopped after version 2.5.1 in 2023, and the project was officially discontinued in
                July 2026.</p>
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
                    <span class="book-meta-value">v2.5.1</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2019 – 2026 <span class="book-meta-note">· 7 years</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/cv-project-app/common-voice-app" target="_blank" rel="noopener"
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
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Store graphic reading “Common Voice in your pocket” with the CV Project home screen: a greeting, a Profile button, the robot mascot and the Speak and Listen buttons">
                <img src="/images/projects/screenshots/cv-project/1.png" alt="Store graphic reading “Common Voice in your pocket” with the CV Project home screen: a greeting, a Profile button, the robot mascot and the Speak and Listen buttons" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Profile screen with email, username, age and gender fields and an All badges button, captioned “Log in to your account”">
                <img src="/images/projects/screenshots/cv-project/2.png" alt="Profile screen with email, username, age and gender fields and an All badges button, captioned “Log in to your account”" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Settings screen in dark theme with a language selector and sections for Listen, Speak, User Interface, Offline mode, Customise gestures and Other">
                <img src="/images/projects/screenshots/cv-project/3.png" alt="Settings screen in dark theme with a language selector and sections for Listen, Speak, User Interface, Offline mode, Customise gestures and Other" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Speak screen recording a sentence, with an animated sound wave around the stop button and Skip and Report buttons">
                <img src="/images/projects/screenshots/cv-project/4.png" alt="Speak screen recording a sentence, with an animated sound wave around the stop button and Skip and Report buttons" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Listen screen showing the sentence “He campaigned against tobacco advertising.” with instructions to vote thumbs up or down and a play button">
                <img src="/images/projects/screenshots/cv-project/5.png" alt="Listen screen showing the sentence “He campaigned against tobacco advertising.” with instructions to vote thumbs up or down and a play button" width="919" height="1800"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Store graphic reading “Developed with” and a blue heart, with a photo of Saverio Morelli next to his name">
                <img src="/images/projects/screenshots/cv-project/6.png" alt="Store graphic reading “Developed with” and a blue heart, with a photo of Saverio Morelli next to his name" width="919" height="1800"
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
                <span class="timeline-date">26 October 2022</span>
                <p>Added a download progress bar and a “Clear offline data” button for Offline mode, plus an option to
                    upload and download only on Wi-Fi. Includes 2.5.0.1–2.5.1 (up to 18 May 2023), which improved
                    offline downloads, added options to swap the Yes/No buttons or require a long press in Listen, and
                    fixed an Android 12 permissions bug.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.4</span>
                <span class="timeline-date">12 September 2021</span>
                <p>Now requires Android 8.0 or later. Added badge, level-up and daily-goal notifications, accessibility
                    improvements, push-to-talk (experimental), new gestures and a custom API server option. Includes
                    2.4.0.1–2.4.0.5 (up to 31 May 2022): translation updates and bug fixes, including one for Android 12
                    and later.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.3</span>
                <span class="timeline-date">8 March 2021</span>
                <p>Renamed the app CV Project with a new icon, published it on the Amazon Appstore, and added text-size
                    settings, in-app message banners, languages loaded from the server and optional ads in the Google
                    Play version. Includes 2.3.1–2.3.9 (up to 1 September 2021), which added motivational sentences,
                    playback speed control, customisable gestures and a configurable offline clip count. 2.3.9 was the
                    last version for Android 6.0–7.1.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.2</span>
                <span class="timeline-date">6 January 2021</span>
                <p>Added a daily-goal progress bar and sharing, an automatic theme, the option to turn off Offline mode,
                    an in-app data reset, log export and native Android 11 support. The app also arrived on Huawei
                    AppGallery. Includes 2.2.1–2.2.3 (up to 23 January 2021): language and offline-mode bug fixes and an
                    F-Droid build fix.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v2.1</span>
                <span class="timeline-date">24 September 2020</span>
                <p>Added login with a verification code, a splash screen, new badges, an improved Profile section and a
                    validation-guidelines button in the first-run tutorial.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v2.0</span>
                <span class="timeline-date">19 June 2020</span>
                <p>Rewritten from scratch with a new interface, Offline mode, Top contributors, app statistics,
                    background sending of recordings and validations, and new animations. Includes 2.0.1–2.0.4 (up to 1
                    August 2020): the app was renamed CV Android, gained links to Mozilla’s Terms of Service and
                    validation guidelines, fixed a crash in Listen and moved to the new commonvoice.mozilla.org domain.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4</span>
                <span class="timeline-date">9 May 2020</span>
                <p>Added optional gestures, a “skip recording confirmation” option and Persian. Includes 1.4.1–1.4.4 (up
                    to 18 May 2020), which added Arabic and Catalan and kept Listen working after Mozilla changed its
                    API response.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">7 May 2020</span>
                <p>Released as 1.3.1: setting a daily goal now requires logging in and logging out resets it, with
                    improved update checks, performance and translations.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2</span>
                <span class="timeline-date">26 April 2020</span>
                <p>Added a recording indicator sound, Check for updates, Report buttons, a “Record again” icon and a
                    redesigned landscape layout. Includes 1.2.1–1.2.3 (up to 3 May 2020): bug fixes, translation updates
                    and an updated anonymous statistics API.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1</span>
                <span class="timeline-date">22 April 2020</span>
                <p>Added the daily goal, new-version notices and a Turkish translation. 1.1.1 (23 April 2020) moved the
                    app to the stable channel, with dialogs that follow the app theme.</p>
            </div>
            <div class="timeline-item major">
                <span class="timeline-version">v1.0</span>
                <span class="timeline-date">20 April 2020</span>
                <p>Added the Speak section for recording sentences, a partial Dutch translation and anonymous statistics
                    that can be turned off (beta, published as 1.0b).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.2</span>
                <span class="timeline-date">20 January 2020</span>
                <p>Added a dark theme and faster clip loading in Listen. Includes 0.2.1b–0.2.11b (up to 13 April 2020):
                    new translations (including Russian, German, Spanish and Czech), more supported languages, UI
                    improvements and login fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1</span>
                <span class="timeline-date">18 January 2020</span>
                <p>Added levels in Profile, an auto-play option for clips, today’s contributions and a reorganised
                    Settings screen. 0.1.1b updated the Basque translation.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0</span>
                <span class="timeline-date">29 November 2019</span>
                <p>Early alpha and beta builds (0.0.1a–0.0.20b, up to 16 January 2020) that built the core app: clip
                    validation, first-run tutorials, login, a Profile with badges, landscape mode and the first
                    translations (Swedish, French and Basque).</p>
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
