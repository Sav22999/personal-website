<?php
$current_page = 'projects';
$title = 'Fucina';
$description = 'Learn to code by playing, from HTML, CSS and JavaScript to Python, Java, SQL, Kotlin, Swift and Rust: levels that get harder, quick drills, games, a daily challenge with its leaderboard, and certificates to unlock with the Sparks you earn. A project by Saverio Morelli.';
$canonical = '/projects/about/fucina/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 3) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__, 3) . '/include/nav.php'); ?>

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
    <h1 class="section-title">Fucina</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/fucina.png" alt="Fucina icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A free, gamified platform for learning programming. Courses cover
                web technologies (HTML, CSS, JavaScript, Angular, Vue), general-purpose languages (Python, Java,
                Kotlin, Swift, Rust) and databases (SQL). Each course is organised into levels of increasing
                difficulty with multiple-choice quizzes, fill-in-the-blank exercises, code-ordering challenges
                and interactive mini-games. You earn Sparks — the in-app currency — by completing levels, winning
                a daily challenge that has its own leaderboard, and keeping a study streak alive. Sparks unlock
                certificates that mark your progress. The app is installable as a PWA and is also available on
                Android.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Web app</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web, Android</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Languages</span>
                    <span class="book-meta-value">English, Italian</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="/projects/fucina/" class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Open app
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">The idea</h2>
        <p>Learning to code should not cost money, feel like a chore, or require a computer science degree to
            get started. Fucina was built as a <strong>free, gamified portal for continuous learning</strong>,
            open to anyone from absolute beginners to experienced developers brushing up on a new language.</p>
        <p>The name comes from the Italian word for <em>forge</em> &mdash; a place where raw material is shaped
            into something useful. Each course in Fucina works the same way: you start with the basics, and
            level by level you build real understanding through <strong>interactive exercises</strong>,
            not passive reading. Multiple-choice quizzes, fill-in-the-blank challenges, code-ordering puzzles
            and mini-games keep the pace varied and engaging.</p>
        <p>Motivation comes from the <strong>Sparks</strong> system &mdash; an in-app currency earned by
            completing levels, winning the daily challenge (which has its own leaderboard), and maintaining a
            study streak. Sparks unlock certificates that mark your progress and give a tangible sense of
            achievement.</p>
        <p>The course catalogue covers the full spectrum: web technologies (HTML, CSS, JavaScript, Angular, Vue),
            general-purpose languages (Python, Java, Kotlin, Swift, Rust) and databases (SQL). All content is
            managed through a dedicated admin panel, making it easy to add new courses and keep existing ones
            up to date without touching the codebase. The goal is a platform that grows with its community,
            always free, always accessible.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
