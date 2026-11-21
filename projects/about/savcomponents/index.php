<?php
$current_page = 'projects';
$title = 'SavComponents';
$description = 'SavComponents: an open-source library of 34 web components you can theme and drop into any website. Standard custom elements, no framework, no build step, no dependencies. Five styles, light and dark mode, a live customiser. MIT licence. A project by Saverio Morelli.';
$canonical = '/projects/about/savcomponents/';
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
    <h1 class="section-title">SavComponents</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/savcomponents.png" alt="SavComponents icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">An open-source library of ready-to-use web components for
                websites and web apps. Each component is a standard custom element &mdash; a plain HTML tag such as
                <code>&lt;sav-button&gt;</code> or <code>&lt;sav-input&gt;</code> that works in static pages, PHP
                templates, React, Vue or anything else, with no framework, no build step and no dependencies. Choose one
                of five visual styles (Soft, Tilt, Sharp, Brutal, Glass), switch between light and dark mode, and
                fine-tune colours, shapes and sizes through CSS variables or through the live customiser on the site.
                Form components &mdash; inputs, selects, checkboxes, radios, date and time pickers &mdash; submit with
                native forms and support validation out of the box.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Library</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Components</span>
                    <span class="book-meta-value">34</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Styles</span>
                    <span class="book-meta-value">Soft, Tilt, Sharp, Brutal, Glass</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Dependencies</span>
                    <span class="book-meta-value">None</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">MIT</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Version</span>
                    <span class="book-meta-value">1.0.0</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://savcomponents.com/" target="_blank" rel="noopener" class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Open
                </a>
                <a href="https://github.com/Sav22999/savcomponents" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65S8.93 17.38 9 18v4"/>
                        <path d="M9 18c-4.51 2-5-2-7-2"/>
                    </svg>
                    View on GitHub
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">The idea</h2>
        <p>Every project on this site needs buttons, inputs, alerts, dialogs and a dozen other UI pieces. Building
            them from scratch each time means duplicating CSS, re-solving the same accessibility details and
            maintaining slightly different versions across projects. After years of copying the same snippets,
            <strong>SavComponents</strong> was born: one library that collects all of those pieces into custom
            elements any page can use with a single <code>&lt;script&gt;</code> tag.</p>
        <p>The library is built entirely on <strong>standard Web Components</strong> &mdash; custom elements, shadow
            DOM and HTML templates. Because the browser does the heavy lifting, there is no framework dependency, no
            build tool, no virtual DOM and <strong>no runtime dependency at all</strong>: the script is self-contained.
            Tags like <code>&lt;sav-button&gt;</code>, <code>&lt;sav-input&gt;</code> or
            <code>&lt;sav-date-picker&gt;</code> work the moment the script loads, in a plain HTML page just as well
            as in a PHP template, a React app or a Vue project.</p>
        <p>Theming relies on <strong>CSS custom properties</strong> and the <code>color-mix()</code> function: set
            one colour per variant and the hover, soft and outline shades are derived automatically. Five built-in
            styles &mdash; Soft, Tilt, Sharp, Brutal and Glass &mdash; cover most visual directions, each with light
            and dark mode. The <strong>live customiser</strong> on the site lets you tweak every token in real time
            and export the result as a ready-to-use CSS file.</p>
        <p>Form components deserve a special mention: inputs, textareas, selects, checkboxes, radios, date pickers
            and time pickers all participate in <strong>native HTML forms</strong>. They submit their values without
            extra JavaScript and support the standard constraint-validation API, so required fields, patterns and
            custom messages work out of the box.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
