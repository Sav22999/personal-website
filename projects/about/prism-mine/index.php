<?php
$current_page = 'projects';
$title = 'Prism Mine';
$description = 'A deduction game: send beams of light into a dark mine, see where they come out and in which colour, and work out where the gems are hidden. Solo, a daily mine and online duels. No sign-up required. A project by Saverio Morelli.';
$canonical = '/projects/about/prism-mine/';
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
    <h1 class="section-title">Prism Mine</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/prism-mine.png" alt="Prism Mine icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A deduction puzzle game inspired by Black Box, free in the browser.
                Fire beams of coloured light into a dark mine and observe where they exit and how their colour
                changes after bouncing off hidden gems. Use that information to deduce the position and type of
                every gem inside. Play solo puzzles at increasing difficulty, tackle a new daily mine shared by all
                players, or challenge someone to an online duel where both players solve the same mine and the
                fastest wins. No account is needed. The app works offline as a PWA and is also available on
                Android.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Web game</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web, Android</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Languages</span>
                    <span class="book-meta-value">English, Italian, French, German, Spanish</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="/projects/prism-mine/" class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Play
                </a>
            </div>
        </div>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
