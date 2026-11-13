<?php
$current_page = 'projects';
$title = 'ThinkHop';
$description = 'The logic game: liars, treasure chests, mini sudoku, absurd reasoning and traps across 500 levels that keep getting harder. Five languages, a daily ranking and friends, with no sign-up required to play. A project by Saverio Morelli.';
$canonical = '/projects/about/thinkhop/';
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
    <h1 class="section-title">ThinkHop</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/thinkhop.png" alt="ThinkHop icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A logic puzzle game with hundreds of levels that get progressively
                harder, free in the browser. Each level is a self-contained riddle drawn from different categories:
                liar-and-truth-teller puzzles, treasure-chest deduction, mini sudoku grids, absurd reasoning
                challenges, and traps designed to catch impulsive answers. Levels are grouped into worlds, and a
                daily challenge lets you compete on a shared leaderboard. You can pick a username to appear on
                rankings and add friends, but no sign-up is required to play. Available in five languages, the app
                works offline as a PWA and is also available on Android.</p>
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
                <a href="/projects/thinkhop/" class="book-btn book-btn-primary">
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
