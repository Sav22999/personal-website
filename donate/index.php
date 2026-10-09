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

<div class="page-header">
    <p class="section-label">Support</p>
    <h1 class="section-title">Buy me a coffee</h1>
</div>

<section class="section">
    <p class="donate-intro">If you enjoy my open-source projects and want to support my work, a small donation goes a
        long way. Every contribution helps me keep building and maintaining free software.</p>

    <div class="card-grid">
        <a class="card recommended" href="https://liberapay.com/Sav22999/" target="_blank" rel="noopener">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7v10"/>
            </svg>
            <div class="card-icon">
                <img src="/images/icons/liberapay.png" alt="">
            </div>
            <h3>LiberaPay <span class="donate-recommended-badge">Recommended</span></h3>
            <p>Open-source platform run by a non-profit. Recurrent donations via Stripe or PayPal.</p>
        </a>
        <a class="card" href="https://www.paypal.me/saveriomorelli" target="_blank" rel="noopener">
            <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7v10"/>
            </svg>
            <div class="card-icon">
                <img src="/images/icons/paypal.png" alt="">
            </div>
            <h3>PayPal</h3>
            <p>Quick one-time donation via PayPal.</p>
        </a>
    </div>

    <p class="donate-thanks">Thank you for your generosity!</p>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
