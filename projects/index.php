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
        <p class="projects-hero-sub">Personal and open-source projects I've designed and developed on my own — apps,
            browser extensions, tools, and more.</p>
        <a class="projects-status-link" href="/projects/status/"><span class="projects-status-dot" aria-hidden="true"></span>Status
            of the services</a>
    </div>
</section>

<section class="section">
    <div class="card-grid">
        <a class="card" href="/projects/about/notefox/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/websites-notes.png" alt="">
            </div>
            <h3>Notefox</h3>
            <p>Take notes on any website. Private, open-source, with sync across devices.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/sav-pdf-viewer/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
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
        <a class="card" href="/projects/about/emoji/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
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
        <a class="card" href="/projects/about/savmrl/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
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
        <a class="card" href="/projects/about/accented-letters/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
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
        <a class="card" href="/projects/about/limite/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
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
        <a class="card" href="/projects/about/mozita-l10n-addon/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
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
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/html-per-tutti.png" alt="">
            </div>
            <h3>HTML per tutti</h3>
            <p>The definitive guide to learning HTML from scratch, with hands-on examples and clear explanations.
                Available on Amazon as paperback and eBook.</p>
            <ul class="project-tags">
                <li>Book</li>
                <li>Italian</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/ripasso-fipav/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/ripasso-fipav.png" alt="">
            </div>
            <h3>Fischietto</h3>
            <p>Personal, unofficial web app to help volleyball referees study, revise and stay up to date with the FIPAV
                rulebook: quizzes, rule references, official documents, personal stats and a match simulation to referee
                in real time.</p>
            <ul class="project-tags">
                <li>Web app</li>
                <li>Italian</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/foodtrack/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/foodtrack.png" alt="">
            </div>
            <h3>FoodTrack</h3>
            <p>Keep track of the food in your fridge, freezer and pantry, get alerts before it expires, share spaces and
                shopping lists with your household, plan your weekly meals and find recipes with what you already
                have.</p>
            <ul class="project-tags">
                <li>Web app</li>
                <li>Android</li>
                <li>English</li>
                <li>Italian</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/giftwise/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/giftwise.png" alt="">
            </div>
            <h3>Giftwise</h3>
            <p>Wish lists for every occasion, shared with the people who follow you. Your friends agree among themselves
                on who gives what and chat about it, without spoiling the surprise.</p>
            <ul class="project-tags">
                <li>Web app</li>
                <li>Android</li>
                <li>English</li>
                <li>Italian</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/fucina/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/fucina.png" alt="">
            </div>
            <h3>Fucina</h3>
            <p>Learn to code by playing, from HTML, CSS and JavaScript to Python, Java, SQL, Kotlin, Swift and Rust: levels
                that get harder, quick drills, games, a daily challenge with its leaderboard, and certificates to unlock
                with the Sparks you earn.</p>
            <ul class="project-tags">
                <li>Web app</li>
                <li>English</li>
                <li>Italian</li>
            </ul>
        </a>
    </div>

    <div class="projects-divider">
        <span class="projects-divider-line"></span>
        <h2>Built with AI</h2>
        <span class="projects-divider-line"></span>
    </div>
    <div class="card-grid">
        <a class="card" href="/projects/about/faceguess/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/faceguess.png" alt="">
            </div>
            <h3>FaceGuess</h3>
            <p>Two-player face-guessing game with unique characters generated every match. Play on the same device, no
                app needed.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/chess/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/chess.png" alt="">
            </div>
            <h3>Chess</h3>
            <p>Play chess with a friend online, a random opponent, on the same device or against the computer. No
                account required.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/sudoku/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/sudoku.png" alt="">
            </div>
            <h3>Sudoku</h3>
            <p>The classic number puzzle with four difficulty levels, pencil notes you can turn into ink, tips that
                explain the next logical move and a single-cell check. No sign-up needed.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/prism-mine/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/prism-mine.png" alt="">
            </div>
            <h3>Prism Mine</h3>
            <p>A deduction game: send beams of light into a dark mine, see where they come out and in which colour,
                and work out where the gems are hidden. Solo, a daily mine and online duels. No sign-up required.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/sea-battle/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/sea-battle.png" alt="">
            </div>
            <h3>Sea Battle</h3>
            <p>The classic naval battle (Battaglia Navale): hide your fleet and sink your opponent's. Play a friend online,
                a random opponent, on the same device or against the computer. No sign-up required.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/connect-four/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/connect-four.png" alt="">
            </div>
            <h3>Connect Four</h3>
            <p>The classic four-in-a-row game: play a friend online, a random opponent, on the same device or against
                the computer. Customise your discs with vibrant colors. No sign-up required.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/letterhop/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/letterhop.png" alt="">
            </div>
            <h3>LetterHop</h3>
            <p>The word ladder game: go from one word to another changing one letter at a time. Three levels, five
                languages, and the fewer words you use, the more points you get. No sign-up required.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/thinkhop/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/thinkhop.png" alt="">
            </div>
            <h3>ThinkHop</h3>
            <p>The logic game: liars, treasure chests, mini sudoku, absurd reasoning and traps across 500 levels that keep
                getting harder. Five languages, a daily ranking and friends, with no sign-up required to play.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>Multilingual</li>
                <li>Built with Claude</li>
            </ul>
        </a>
        <a class="card" href="/projects/about/forkville/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/forkville.png?v=3" alt="">
            </div>
            <h3>Forkville</h3>
            <p>Run your own little restaurant in 3D: build and decorate it, hire chefs and waiters, grow ingredients,
                improve your dishes and visit your friends' restaurants.</p>
            <ul class="project-tags">
                <li>Web game</li>
                <li>3D</li>
                <li>Built with Claude</li>
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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
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
        <a class="card faded" href="/projects/about/emoticolor/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/emoticolor.png" alt="">
            </div>
            <h3>Emoticolor</h3>
            <p>Emotion-based social network where users share how they feel through colors.</p>
            <ul class="project-tags">
                <li>Web app</li>
                <li>Thesis project</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/word-of-the-day/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/word-of-the-day.png" alt="">
            </div>
            <h3>Word of the Day</h3>
            <p>Learn a new word every day with its definition, origin, and pronunciation.</p>
            <ul class="project-tags">
                <li>Android</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/cv-project/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/cv-project.png" alt="">
            </div>
            <h3>CV Project</h3>
            <p>Unofficial Android app for contributing to Mozilla Common Voice.</p>
            <ul class="project-tags">
                <li>Android</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/my-code-editor/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/my-code-editor.png" alt="">
            </div>
            <h3>My Code Editor</h3>
            <p>Free, lightweight HTML and CSS editor for Windows with live preview.</p>
            <ul class="project-tags">
                <li>Windows</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/nolimit-math/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/nolimit-math.png" alt="">
            </div>
            <h3>NoLimit Math</h3>
            <p>Desktop software for calculating mathematical limits and function plots.</p>
            <ul class="project-tags">
                <li>Windows</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/scrolly/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/scrolly.png" alt="">
            </div>
            <h3>Scrolly</h3>
            <p>Firefox add-on that saved and restored scroll positions for every page.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/mozita-antispam/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozita-antispam.png" alt="">
            </div>
            <h3>MozIta Antispam</h3>
            <p>Telegram bot for the Mozilla Italia community to detect and remove spam.</p>
            <ul class="project-tags">
                <li>Telegram bot</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/all-currencies/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/all-currencies.png" alt="">
            </div>
            <h3>All Currencies</h3>
            <p>Firefox add-on to copy currency symbols with a single click.</p>
            <ul class="project-tags">
                <li>Browser extension</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/mozita-myuserid-bot/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozita-myuserid.png" alt="">
            </div>
            <h3>MozIta MyUserId Bot</h3>
            <p>Telegram bot to get your own Telegram user ID.</p>
            <ul class="project-tags">
                <li>Telegram bot</li>
            </ul>
        </a>
        <a class="card faded" href="/projects/about/mozitabot/">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <div class="card-icon card-icon-project">
                <img src="/images/projects/mozitabot.png" alt="">
            </div>
            <h3>MozItaBot</h3>
            <p>The official Telegram bot of the Mozilla Italia community.</p>
            <ul class="project-tags">
                <li>Telegram bot</li>
            </ul>
        </a>
    </div>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
