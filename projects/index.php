<?php
$current_page = 'projects';
$title = 'Projects';
$description = 'Open-source projects by Saverio Morelli: Notefox, Sav PDF Viewer, Emoji add-on, and more browser extensions and Android apps.';
$canonical = '/projects/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__) . '/include/nav.php'); ?>

<section class="projects-hero">
    <div class="projects-hero-content">
        <p class="section-label">Projects</p>
        <h1>What I've built.</h1>
        <p class="projects-hero-sub">Personal and open-source projects I've designed and developed on my own — apps, browser extensions, tools, and more.</p>
    </div>
</section>

<section class="section">
    <div class="card-grid">
        <a class="card" href="/projects/notefox/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/websites-notes.png" alt="">
            </div>
            <h3>Notefox</h3>
            <p>Take notes on any website. Private, open-source, with sync across devices.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
                <li>Open source</li>
            </ul>
        </a>
        <a class="card" href="/projects/sav-pdf-viewer/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/sav-pdf-viewer-pro.png" alt="">
            </div>
            <h3>Sav PDF Viewer</h3>
            <p>Private, open-source PDF reader for Android. No ads, no tracking. 100,000+ users.</p>
            <ul class="project-tags">
                <li>Android</li>
                <li>100K+ users</li>
            </ul>
        </a>
        <a class="card" href="/projects/emoji/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/emoji.png" alt="">
            </div>
            <h3>Emoji</h3>
            <p>Copy or insert any emoji with a click. Search by keywords, color, or gender.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card" href="/projects/savmrl/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/savmrl.png" alt="">
            </div>
            <h3>savmrl.it</h3>
            <p>Free, anonymous link shortener and redirecting service. No registration, no tracking.</p>
            <ul class="project-tags">
                <li>Web</li>
            </ul>
        </a>
        <a class="card" href="/projects/accented-letters/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/accented-letters.png" alt="">
            </div>
            <h3>Accented Letters</h3>
            <p>Firefox add-on to copy accented and special characters with a single click.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card" href="/projects/limite/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/limite.png" alt="">
            </div>
            <h3>Limite</h3>
            <p>Browser add-on that tracks how much time you spend on each website, daily.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card" href="/projects/mozita-l10n-addon/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozita-l10n-addon.png" alt="">
            </div>
            <h3>MozIta L10n Addon</h3>
            <p>Firefox add-on to support the Mozilla Italia localization team workflow.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
                <li>Mozilla Italia</li>
            </ul>
        </a>
        <a class="card" href="/htmlpertutti/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/html-per-tutti.png" alt="">
            </div>
            <h3>HTML per tutti</h3>
            <p>A book to learn HTML from scratch, written in Italian. Available on Amazon.</p>
            <ul class="project-tags">
                <li>Book</li>
                <li>Italian</li>
            </ul>
        </a>
    </div>

    <div class="projects-divider">
        <span class="projects-divider-line"></span>
        <h2>Contributions</h2>
        <span class="projects-divider-line"></span>
    </div>
    <div class="card-grid">
        <a class="card" href="https://github.com/MozillaItalia/firefox-vademecum" target="_blank" rel="noopener">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7v10"/>
            </svg>
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65S8.93 17.38 9 18v4"/>
                    <path d="M9 18c-4.51 2-5-2-7-2"/>
                </svg>
            </div>
            <h3>Firefox Vademecum</h3>
            <p>A printable A4 reference sheet with useful information about Mozilla Firefox.</p>
            <ul class="project-tags">
                <li>Mozilla Italia</li>
            </ul>
        </a>
        <a class="card" href="https://github.com/Mte90/Share-Backported" target="_blank" rel="noopener">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7v10"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/share-backported.png" alt="">
            </div>
            <h3>Share Backported</h3>
            <p>Firefox add-on to share web pages on social networks with a single click.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card" href="https://www.mozillaitalia.org" target="_blank" rel="noopener">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7v10"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozita-website.png" alt="">
            </div>
            <h3>Mozilla Italia Website</h3>
            <p>The website of the Italian Mozilla community, built with other volunteers.</p>
            <ul class="project-tags">
                <li>Web</li>
                <li>Mozilla Italia</li>
            </ul>
        </a>
    </div>

    <div class="projects-divider">
        <span class="projects-divider-line"></span>
        <h2>Discontinued</h2>
        <span class="projects-divider-line"></span>
    </div>
    <div class="card-grid">
        <a class="card faded" href="/projects/emoticolor/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/emoticolor.png" alt="">
            </div>
            <h3>Emoticolor</h3>
            <p>Emotion-based social network where users share how they feel through colors.</p>
            <ul class="project-tags"><li>Web app</li><li>Thesis project</li></ul>
        </a>
        <a class="card faded" href="/projects/word-of-the-day/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/word-of-the-day.png" alt="">
            </div>
            <h3>Word of the Day</h3>
            <p>Learn a new word every day with its definition, origin, and pronunciation.</p>
            <ul class="project-tags"><li>Android</li></ul>
        </a>
        <a class="card faded" href="/projects/cv-project/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/cv-project.png" alt="">
            </div>
            <h3>CV Project</h3>
            <p>Unofficial Android app for contributing to Mozilla Common Voice.</p>
            <ul class="project-tags"><li>Android</li></ul>
        </a>
        <a class="card faded" href="/projects/my-code-editor/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/my-code-editor.png" alt="">
            </div>
            <h3>My Code Editor</h3>
            <p>Free, lightweight HTML and CSS editor for Windows with live preview.</p>
            <ul class="project-tags"><li>Windows</li></ul>
        </a>
        <a class="card faded" href="/projects/nolimit-math/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/nolimit-math.png" alt="">
            </div>
            <h3>NoLimit Math</h3>
            <p>Desktop software for calculating mathematical limits and function plots.</p>
            <ul class="project-tags"><li>Windows</li></ul>
        </a>
        <a class="card faded" href="/projects/scrolly/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/scrolly.png" alt="">
            </div>
            <h3>Scrolly</h3>
            <p>Firefox add-on that saved and restored scroll positions for every page.</p>
            <ul class="project-tags"><li>Browser extension</li></ul>
        </a>
        <a class="card faded" href="/projects/mozita-antispam/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozita-antispam.png" alt="">
            </div>
            <h3>MozIta Antispam</h3>
            <p>Telegram bot for the Mozilla Italia community to detect and remove spam.</p>
            <ul class="project-tags"><li>Telegram bot</li></ul>
        </a>
        <a class="card faded" href="/projects/all-currencies/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/all-currencies.png" alt="">
            </div>
            <h3>All Currencies</h3>
            <p>Firefox add-on to copy currency symbols with a single click.</p>
            <ul class="project-tags"><li>Browser extension</li></ul>
        </a>
        <a class="card faded" href="/projects/mozita-myuserid-bot/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozita-myuserid.png" alt="">
            </div>
            <h3>MozIta MyUserId Bot</h3>
            <p>Telegram bot to get your own Telegram user ID.</p>
            <ul class="project-tags"><li>Telegram bot</li></ul>
        </a>
        <a class="card faded" href="/projects/mozitabot/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozitabot.png" alt="">
            </div>
            <h3>MozItaBot</h3>
            <p>The official Telegram bot of the Mozilla Italia community.</p>
            <ul class="project-tags"><li>Telegram bot</li></ul>
        </a>
    </div>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
