<?php
$current_page = 'services';
$title = 'Services';
$description = 'Frontend development, app development, and UX/UI design services by Saverio Morelli. Let\'s build something together.';
$canonical = '/services/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__) . '/include/nav.php'); ?>

<section class="services-hero">
    <div class="services-hero-content">
        <p class="section-label">Services</p>
        <h1>Got an idea?<br>Let's make it real.</h1>
        <p class="services-hero-sub">I design and build digital products — from a quick landing page to a full app. Tell me what you need, and I'll take care of the rest.</p>
        <a href="/contact-me/email/?reason=Project+request" class="services-cta">
            <span>Get in touch</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

<section class="section">
    <div class="services-grid">
        <div class="service-card">
            <div class="service-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <path d="M8 21h8M12 17v4"/>
                </svg>
            </div>
            <h3>Web development</h3>
            <p>Responsive websites, web apps, and landing pages. Fast, clean, and built to last.</p>
            <ul class="service-tags">
                <li>HTML / CSS / JS</li>
                <li>PHP</li>
                <li>Responsive</li>
                <li>SEO</li>
            </ul>
        </div>

        <div class="service-card">
            <div class="service-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M12 18h.01"/>
                </svg>
            </div>
            <h3>App development</h3>
            <p>Android apps and browser extensions — from first sketch to store release.</p>
            <ul class="service-tags">
                <li>Android</li>
                <li>Browser extensions</li>
                <li>Store publishing</li>
            </ul>
        </div>

        <div class="service-card">
            <div class="service-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838.838-2.872a2 2 0 0 1 .506-.855z"/>
                </svg>
            </div>
            <h3>UX & UI design</h3>
            <p>Interfaces that feel right. Research, wireframes, prototypes, and pixel-perfect design.</p>
            <ul class="service-tags">
                <li>User research</li>
                <li>Wireframing</li>
                <li>Prototyping</li>
                <li>Visual design</li>
            </ul>
        </div>
    </div>

    <div class="services-process">
        <h2 class="services-process-title">How it works</h2>
        <div class="services-steps">
            <div class="services-step">
                <span class="services-step-number">01</span>
                <h3>You tell me your idea</h3>
                <p>What do you need? A website, an app, a redesign? Describe your goals and I'll get back to you.</p>
            </div>
            <div class="services-step">
                <span class="services-step-number">02</span>
                <h3>We plan it together</h3>
                <p>I propose a scope, timeline, and design direction. Nothing starts without your OK.</p>
            </div>
            <div class="services-step">
                <span class="services-step-number">03</span>
                <h3>I build, you follow along</h3>
                <p>Regular updates so you can see progress and steer the direction as we go.</p>
            </div>
            <div class="services-step">
                <span class="services-step-number">04</span>
                <h3>Launch & beyond</h3>
                <p>I deliver, you launch. I stick around for fixes and adjustments after go-live.</p>
            </div>
        </div>
    </div>

    <div class="services-cta-section">
        <h2>Ready to start?</h2>
        <p>Drop me a message — no commitment, just a conversation.</p>
        <a href="/contact-me/email/?reason=Project+request" class="services-cta">
            <span>Request a project</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

<script src="/script.js"></script>
</body>
</html>
