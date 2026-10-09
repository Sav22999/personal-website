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
                <svg viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.896 13.387c-.01 0-.023 0-.035 0-.421 0-.832.045-1.228.129l.038-.007-1.428 5.938c.235.052.505.082.782.082.026 0 .052 0 .078-.001h-.004c.014 0 .03 0 .047 0 .508 0 .99-.109 1.425-.305l-.022.009c.439-.199.81-.478 1.109-.822l.003-.004c.306-.36.549-.784.706-1.248l.008-.027c.162-.472.255-1.017.255-1.583 0-.021 0-.042 0-.063v.003c.001-.025.001-.054.001-.083 0-.519-.145-1.005-.395-1.418l.007.012c-.27-.377-.706-.62-1.199-.62-.052 0-.103.003-.153.008l.006-.001zM20.284 10.836c.037-.001.08-.002.124-.002.664 0 1.299.128 1.88.36l-.034-.012c.536.219.989.542 1.353.946l.003.003c.346.393.615.863.779 1.38l.007.027c.162.504.255 1.083.255 1.684 0 .014 0 .028 0 .042v-.001c0 .027.001.059.001.091 0 .947-.18 1.851-.508 2.681l.017-.05c-.326.828-.785 1.536-1.359 2.134l.002-.002c-.575.591-1.267 1.065-2.04 1.382l-.04.015c-.761.316-1.646.5-2.573.5-.024 0-.048 0-.072 0h.004c-.491 0-.97-.045-1.436-.13l.049.007-.918 3.694h-3.02l3.387-14.119c.488-.152 1.117-.304 1.757-.422l.11-.017c.661-.123 1.421-.194 2.198-.194.027 0 .055 0 .082 0h-.004zM15.41 5.978l-2.837 11.753c-.048.198-.081.429-.091.666v.008c-.001.018-.002.039-.002.06 0 .175.042.34.117.486l-.003-.006c.095.168.239.299.412.375l.006.002c.243.106.525.173.822.184h.004l-.612 2.509c-.072.003-.157.005-.243.005-.731 0-1.429-.14-2.07-.393l.038.013c-.504-.211-.911-.57-1.177-1.021l-.006-.011c-.22-.406-.349-.889-.349-1.401 0-.035.001-.069.002-.103v.005c.013-.647.098-1.268.247-1.863l-.012.057 2.592-10.834zM3.903 1.004C2.302 1.005 1.005 2.302 1.004 3.903v24.193c.001 1.601 1.298 2.898 2.899 2.899h24.193c1.601 0 2.899-1.298 2.899-2.899V3.903c0-1.601-1.298-2.899-2.899-2.899H3.903z"/>
                </svg>
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
                <svg viewBox="-3.5 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M34.912 3.619C32.671 1.086 28.622 0 23.44 0H8.405c-1.06 0-1.961.765-2.127 1.801L.016 41.194c-.124.777.482 1.48 1.276 1.48h9.282l2.332-14.67-.073.46c.166-1.037 1.061-1.802 2.12-1.802h4.41c8.667 0 15.451-3.492 17.434-13.593.059-.3.154-.875.154-.875.562-3.738-.006-6.274-2.04-8.575zm4.389 10.489c-2.156 9.945-9.03 15.208-19.937 15.208h-3.955L12.458 48h6.416c.927 0 1.714-.669 1.86-1.576l.075-.396 1.476-9.273.095-.511c.144-.907.932-1.577 1.858-1.577h1.172c7.58 0 13.516-3.055 15.25-11.89.696-3.546.36-6.519-1.36-8.669z"/>
                </svg>
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
