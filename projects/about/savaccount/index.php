<?php
$current_page = 'projects';
$title = 'SavAccount';
$description = 'SavAccount: one account to sign in to many services without Google, Microsoft or Apple. Verified email addresses, two-step verification, and you decide what each service gets. OpenID Connect. A project by Saverio Morelli.';
$canonical = '/projects/about/savaccount/';
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
    <h1 class="section-title">SavAccount</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/savaccount.png" alt="SavAccount icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">One account to sign in to the services you use, without going through Google,
                Microsoft or Apple. You create it with your name, an email address and a password; every address is
                verified, and you can link several to the same account. A service only receives the data you approve on
                its consent page, with the address you choose, and you can change your mind or disconnect it at any
                time. Two-step verification works with an authenticator app or a code by email, with recovery codes for
                emergencies.</p>
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
                    <span class="book-meta-label">Standard</span>
                    <span class="book-meta-value">OpenID Connect</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Two-step verification</span>
                    <span class="book-meta-value">App, email, recovery codes</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://www.savaccount.eu/" target="_blank" rel="noopener" class="book-btn book-btn-primary">
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
        <p>&ldquo;Sign in with&hellip;&rdquo; buttons are handy, but they hand every sign-in to a company whose business
            is knowing what you do. <strong>SavAccount</strong> keeps the convenience &mdash; one account, many services
            &mdash; without the profile built around it: no advertising, no tracking, no data sold.</p>
        <p>By default each service knows you by a <strong>different identifier</strong>, so two services cannot compare
            their users. Your name, your address or your date of birth are shared only if you say so, service by
            service.</p>
        <p>For developers it is a standard <strong>OpenID Connect</strong> provider: authorization code flow with PKCE
            required for everyone, signed ID tokens, rotating refresh tokens. Any OIDC library works with it, starting
            from the <a href="https://www.savaccount.eu/developers" target="_blank" rel="noopener">developer
            page</a>.</p>
        <p>From your account you can see the signed-in devices and the security log, download all your data, or delete
            everything. The interface is built with <a href="/projects/about/savcomponents/">SavComponents</a>, in its
            Tilt style.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
