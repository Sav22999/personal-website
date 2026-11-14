<?php
$current_page = 'projects';
$title = 'Giftwise';
$description = 'Wish lists for every occasion, shared with the people who follow you. Your friends agree among themselves on who gives what and chat about it, without spoiling the surprise. A project by Saverio Morelli.';
$canonical = '/projects/about/giftwise/';
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
    <h1 class="section-title">Giftwise</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/giftwise.png" alt="Giftwise icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A free web app for organising wish lists and coordinating gifts
                without spoiling the surprise. Create wish lists for birthdays, Christmas, weddings or any other
                occasion and share them with friends and family who follow you. Followers can see what you wish
                for, claim an item so others know it is taken, chip in together on expensive gifts, and chat about
                it in a private thread that the list owner never sees. The app is installable as a PWA and is also
                available on Android.</p>
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
                <a href="/projects/giftwise/" class="book-btn book-btn-primary">
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
        <p>Gift-giving should feel thoughtful, not frantic. Yet most wish-list apps treat it as a last-minute
            exercise: a birthday approaches, you scramble to fill a list, and your friends end up buying generic
            items you barely remember adding. Giftwise flips that model on its head.</p>
        <p>The core principle is <strong>year-round wish accumulation</strong>. Whenever you spot something you
            would like &mdash; walking past a shop, browsing online, chatting with a friend &mdash; you add it
            to Giftwise straight away. Over months, the list fills organically with things you genuinely want,
            rather than things you rushed to think of the week before your birthday.</p>
        <p>On the other side, your followers can browse your wishes at any time. They <strong>claim an item</strong>
            so that others know it is taken, <strong>chip in together</strong> on expensive gifts, and discuss
            ideas in a <strong>private chat thread that the list owner never sees</strong>. The surprise stays
            intact: you know <em>something</em> from your list is coming, but you never know <em>what</em> or
            <em>from whom</em>.</p>
        <p>The design goal was to keep the flow as lightweight as possible. Creating a list takes seconds, sharing
            it is a single link, and followers do not need an account to view &mdash; only to claim or chat. The
            result is a small, focused tool that sits quietly in the background all year and comes alive whenever
            an occasion arrives.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
