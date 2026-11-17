<?php
$current_page = 'projects';
$title = 'OpenNewsletter';
$description = 'OpenNewsletter: a free web app to write, approve and send newsletters as a team, with a drag-and-drop editor, roles, subscribers in every language, a subscription page and a widget for any website. A project by Saverio Morelli.';
$canonical = '/projects/about/opennewsletter/';
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
    <h1 class="section-title">OpenNewsletter</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/opennewsletter.png" alt="OpenNewsletter icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A free web app to run newsletters as a team. Each group has its own members,
                its own subscribers and its own outgoing email address. Moderators design newsletters with a
                drag-and-drop editor, starting from ready templates or from the group's own, and propose a date and
                time; publishers approve or reject them; the newsletter leaves on time, to all the subscribers or only
                to those of one language. A subscription page and a widget for any website are ready from the start.</p>
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
                    <span class="book-meta-value">English, Italian</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="/projects/opennewsletter/" class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Open
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">The idea</h2>
        <p>A club, an association or a small business that sends a newsletter is rarely one person. Someone writes it,
            someone else checks it, and nobody wants a message to reach hundreds of people by mistake.
            <strong>OpenNewsletter</strong> gives every group <strong>roles</strong> &mdash; users, moderators,
            publishers and administrators &mdash; and an <strong>approval step</strong>: moderators propose a time slot,
            publishers approve it, and the newsletter is sent at that time. Publishers get a reminder when a proposal is
            about to miss its slot.</p>
        <p>The <strong>editor</strong> works with blocks you drag onto the page: titles, text, images, image and text
            side by side, buttons, cards, quotes, dividers and social links, each with its own spacing, typeface and
            colours. What you see is what subscribers get: the preview and the test email come from the same renderer
            that sends the newsletter, built for every mail app.</p>
        <p>Subscribers sign up from a <strong>ready page</strong> or from a <strong>widget</strong> that fits on any
            website, in five looks. They choose their language, accept the terms and confirm their address by email;
            every newsletter has a one-click unsubscribe link, and anyone leaving can say why. Each group sends from
            <strong>its own address</strong>, with an hourly limit so that providers never block it.</p>
        <p>You can sign in with email and password (the address must be confirmed) or with Google. The account email is
            unique and cannot be changed, and the whole app works just as well on a phone.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
