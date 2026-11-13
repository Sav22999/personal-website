<?php
$current_page = 'projects';
$title = 'Fischietto';
$description = 'Personal, unofficial web app to help volleyball referees study, revise and stay up to date with the FIPAV rulebook: quizzes, rule references, official documents, personal stats and a match simulation to referee in real time. A project by Saverio Morelli.';
$canonical = '/projects/about/ripasso-fipav/';
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
    <h1 class="section-title">Fischietto</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/ripasso-fipav.png" alt="Fischietto icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A personal, unofficial web app for Italian volleyball referees who
                want to study and revise the FIPAV rulebook. It offers multiple-choice quizzes by topic and
                difficulty, a searchable rule reference that links every article to its official source, a
                collection of downloadable official documents (circulars, guides, case books), personal statistics
                that track your progress and weak spots, and a match simulation where you can practice refereeing
                a live-action volleyball match in real time, including video replays of key moments. The app is
                installable as a PWA and works offline. It is written in Italian.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Web app</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Languages</span>
                    <span class="book-meta-value">Italian</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="/projects/ripasso-fipav/" class="book-btn book-btn-primary">
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

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
