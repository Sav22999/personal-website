<?php
$current_page = 'projects';
$title = 'MyCurriculum';
$description = 'MyCurriculum: a free web app to write your CV in several languages, choose a template, download each language as a PDF and share it as a web page. Import an existing CV from a PDF. A project by Saverio Morelli.';
$canonical = '/projects/about/mycurriculum/';
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
    <h1 class="section-title">MyCurriculum</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/mycurriculum.png" alt="MyCurriculum icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A free web app to write your curriculum vitae once and keep a
                version for every language you need. Choose among four templates, pick your colour and font, decide
                exactly what to show (photo, email, phone, custom fields, whole sections) and add a footer such as the
                Italian data-processing consent. Each language downloads as its own PDF with real, selectable text, and
                the CV can also be published as a web page with a language menu, showing only the public information.
                You can start from the CV you already have: import its PDF and the fields fill in automatically.</p>
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
                <a href="/projects/mycurriculum/" class="book-btn book-btn-primary">
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
        <p>Anyone who has applied for a job abroad knows the routine: one CV in Italian, another in English, maybe a
            third in German, each kept in a separate document that slowly drifts out of date. <strong>MyCurriculum</strong>
            keeps them together: one CV with a <strong>version for each language</strong>. Adding a language copies an
            existing one and translates the section titles and the consent footer, so only your own texts are left to
            translate.</p>
        <p>The same data feeds three outputs that always match: the <strong>editor preview</strong>, the
            <strong>PDF</strong> generated in the browser, and a <strong>public web page</strong> at its own address,
            with a custom language menu. Anything you hide &mdash; your phone number, a custom field, a whole section
            &mdash; is removed on the server before the page is built, so it never reaches a visitor.</p>
        <p>To get started quickly you can <strong>import the PDF of your current CV</strong>. It is read entirely in
            your browser, never uploaded: the app finds your contacts, name, headings in ten languages and one entry per
            date range, even in two-column layouts. Automatic import is never perfect, so the result is there to check
            and correct, with an undo button.</p>
        <p>You can sign in with email and password (the address must be confirmed) or with Google. The account email is
            unique and cannot be changed, and the whole app works just as well on a phone.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
