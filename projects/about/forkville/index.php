<?php
$current_page = 'projects';
$title = 'Forkville';
$description = 'Forkville: a free 3D restaurant game in the browser. Build and decorate your restaurant, grow ingredients, hire staff, improve your dishes and visit your friends. A project by Saverio Morelli.';
$canonical = '/projects/about/forkville/';
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
    <h1 class="section-title">Forkville</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/forkville.png" alt="Forkville icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A free 3D restaurant management game that runs entirely in the
                browser. Start with a small empty plot, build your restaurant room by room, and furnish it with
                tables, chairs, decorations and kitchen equipment. Hire chefs and waiters, each with their own
                skill levels. Grow ingredients in your garden, unlock new recipes, and improve your dishes to
                attract more customers and earn more revenue. Visit your friends' restaurants to see how they
                have decorated and what they serve. The game renders a top-down 3D view, requires an email-verified
                account to save progress, and is installable as a PWA.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Web game</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web</span>
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
                <a href="/projects/forkville/" class="book-btn book-btn-primary">
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
