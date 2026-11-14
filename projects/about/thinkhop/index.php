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
                <a href="https://play.google.com/store/apps/details?id=com.saverio.thinkhop" target="_blank"
                   rel="noopener" class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Google Play
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">The idea</h2>
        <p>Most brain-training games rely on speed: tap faster, match quicker, beat the clock. ThinkHop takes
            the opposite approach. It is a <strong>&ldquo;non-sense&rdquo; logic game</strong> &mdash; a
            collection of puzzles where the answer is never obvious, the rules bend from level to level, and
            rushing guarantees a wrong answer.</p>
        <p>The idea was to create a game that <strong>trains reasoning, not reflexes</strong>. Each level is a
            self-contained riddle drawn from a rotating mix of categories: liar-and-truth-teller puzzles where
            you must figure out who is lying, treasure-chest deduction where clues narrow down the right box,
            mini sudoku grids, absurd lateral-thinking challenges, and traps specifically designed to punish
            impulsive answers. The variety keeps the brain engaged because you can never settle into a single
            solving strategy.</p>
        <p>Levels are grouped into themed <strong>worlds</strong> that unlock progressively, giving a sense
            of journey. A <strong>daily challenge</strong> adds a competitive edge: every player gets the same
            puzzle each day, and the shared leaderboard rewards both speed and accuracy. You can pick a username
            and add friends to compare progress, but no sign-up is required to play &mdash; the barrier to entry
            is deliberately zero.</p>
        <p>The game is available in five languages and works offline as a PWA, making it the kind of pocket
            puzzle you can pull out on a commute, in a waiting room, or whenever you have a few minutes to
            give your mind a proper workout.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
