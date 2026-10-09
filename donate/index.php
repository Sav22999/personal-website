<?php
$current_page = 'donate';
$title = 'Buy me a coffee';
$description = 'Support Saverio Morelli\'s open-source work with a donation via LiberaPay or PayPal.';
$canonical = '/donate/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__) . '/include/nav.php'); ?>

<section class="donate-hero">
    <div class="donate-hero-content">
        <p class="section-label">Support</p>
        <h1>Keep it free,<br>keep it going.</h1>
        <p class="donate-hero-sub">All my projects are free and open-source — no ads, no tracking, no premium tiers. A small donation helps me keep it that way.</p>
    </div>
</section>

<section class="section">
    <div class="donate-options">
        <a class="donate-option recommended" href="https://liberapay.com/Sav22999/" target="_blank" rel="noopener">
            <div class="donate-option-badge">Recommended</div>
            <div class="donate-option-icon">
                <img src="/images/icons/liberapay.png" alt="">
            </div>
            <h3>LiberaPay</h3>
            <p>Open-source platform run by a non-profit. Set up a recurring donation — even just €1/week makes a difference.</p>
            <span class="donate-option-cta">
                <span>Donate on LiberaPay</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 17L17 7M17 7H7M17 7v10"/>
                </svg>
            </span>
        </a>
        <a class="donate-option" href="https://www.paypal.me/saveriomorelli" target="_blank" rel="noopener">
            <div class="donate-option-icon">
                <img src="/images/icons/paypal.png" alt="">
            </div>
            <h3>PayPal</h3>
            <p>Quick one-time donation. No account needed — just pick an amount and you're done.</p>
            <span class="donate-option-cta">
                <span>Donate via PayPal</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 17L17 7M17 7H7M17 7v10"/>
                </svg>
            </span>
        </a>
    </div>

    <div class="donate-impact">
        <h2 class="donate-impact-title">What your support helps</h2>
        <div class="donate-impact-grid">
            <div class="donate-impact-item">
                <div class="donate-impact-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h3>No ads, ever</h3>
                <p>Donations keep my apps and extensions ad-free for everyone.</p>
            </div>
            <div class="donate-impact-item">
                <div class="donate-impact-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3>Open source</h3>
                <p>Every project stays free, transparent, and open to contributions.</p>
            </div>
            <div class="donate-impact-item">
                <div class="donate-impact-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </div>
                <h3>Active maintenance</h3>
                <p>Bug fixes, updates, and new features — I keep things running.</p>
            </div>
            <div class="donate-impact-item">
                <div class="donate-impact-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
                    </svg>
                </div>
                <h3>New ideas</h3>
                <p>Your support gives me time to experiment and build new things.</p>
            </div>
        </div>
    </div>

    <div class="donate-thanks-section">
        <p>Every little bit counts — thank you for being part of this.</p>
    </div>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
