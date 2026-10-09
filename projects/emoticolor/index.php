<?php
$current_page = 'projects';
$title = 'Emoticolor';
$description = 'Emoticolor — an emotion-based social network where users share how they feel through colors. A discontinued thesis project by Saverio Morelli.';
$canonical = '/projects/emoticolor/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Emoticolor",
            "applicationCategory": "SocialNetworkingApplication",
            "operatingSystem": "Any",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/Sav22999/emoticolor",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "EUR"
            }
        }
    </script>
</head>
<body>

<?php include_once(dirname(__DIR__, 2) . '/include/nav.php'); ?>

<div class="page-header">
    <a href="/projects/" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to projects
    </a>
    <p class="section-label section-label-discontinued">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="21 8 21 21 3 21 3 8"/>
            <rect x="1" y="3" width="22" height="5"/>
            <line x1="10" y1="12" x2="14" y2="12"/>
        </svg>
        Discontinued project
    </p>
    <h1 class="section-title">Emoticolor</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/emoticolor.png" alt="Emoticolor icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">Emoticolor was an emotion-based social network designed and developed as a
                master's thesis project. Users could share their emotional states through posts associated with
                colors, follow both other users and specific emotions, react to posts anonymously, and explore
                guided learning paths to better understand their emotions. The platform was built around
                transparency and privacy: a purely chronological feed with no algorithmic ranking, no push
                notifications, anonymous reactions, and privacy-by-design with end-to-end encrypted emails and
                two-factor authentication.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Progressive Web App</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web (all browsers)</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Stack</span>
                    <span class="book-meta-value">Vue.js + Vite, PHP 8.1, MySQL</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">GPL v3</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Context</span>
                    <span class="book-meta-value">Master's thesis — University of Udine</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2024 – 2025 <span class="book-meta-note">· 1 year</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/Sav22999/emoticolor" target="_blank" rel="noopener"
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
    <div class="addon-screenshots">
        <h2 class="addon-screenshots-title">Screenshots</h2>
        <div class="screenshot-grid portrait">
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Login screen">
                <img src="/images/projects/screenshots/emoticolor/login.webp" alt="Login screen showing the Emoticolor logo, email and password fields, a sign-in button and a create account option" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Home feed with emotional posts">
                <img src="/images/projects/screenshots/emoticolor/home-feed.webp" alt="Home feed showing posts from users sharing emotions like joy, optimism and trust, with reaction buttons and a create button" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Empty home explaining the no-algorithm philosophy">
                <img src="/images/projects/screenshots/emoticolor/empty-home.webp" alt="Empty home screen explaining that Emoticolor has no algorithms: the user decides what to see by following emotions or other users" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: User profile page">
                <img src="/images/projects/screenshots/emoticolor/profile.webp" alt="User profile page showing bio, follower counts, and emotional posts with colored borders and reaction buttons" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Emotion page for Fear">
                <img src="/images/projects/screenshots/emoticolor/emotion-page.webp" alt="Emotion page for Fear showing a follow button, a link to start learning about the emotion, and related posts" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Create a new emotional state">
                <img src="/images/projects/screenshots/emoticolor/create-post.webp" alt="New emotional state form with required fields for visibility, emotion and color, plus optional text, image, place and location" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Emotion selector">
                <img src="/images/projects/screenshots/emoticolor/emotion-selector.webp" alt="Emotion selector listing all available emotions: love, anxiety, anticipation, disgust, contempt, trust, joy, optimism, fear, anger and more" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Color picker for emotional posts">
                <img src="/images/projects/screenshots/emoticolor/color-picker.webp" alt="Color picker showing a full grid of colors from light pastels to deep saturated tones, used to express the shade of an emotion" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Reaction bottom sheet on a post">
                <img src="/images/projects/screenshots/emoticolor/reactions.webp" alt="Post in the feed with a reaction bottom sheet offering eight anonymous reaction types: OK, Like, Kiss, Alien, Dislike, Applause, Diamond and Fire Heart" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Learning paths for Anticipation">
                <img src="/images/projects/screenshots/emoticolor/learning-paths.webp" alt="Learning paths for Anticipation showing a guided path at 75% progress and bite-sized lessons at 0%, with illustrated cards" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Guided learning path structure">
                <img src="/images/projects/screenshots/emoticolor/guided-path.webp" alt="Guided learning path for Anticipation with four sections — Psychology, Curiosities, Physiology and Colors — showing expandable lessons" width="1170" height="2532"
                     loading="lazy">
            </button>
            <button type="button" class="screenshot" aria-label="Enlarge screenshot: Learning content about Degas">
                <img src="/images/projects/screenshots/emoticolor/learning-content.webp" alt="Detailed learning content about anticipation in Degas' ballet paintings, with a reproduction of a Degas pastel and source attribution" width="1170" height="2532"
                     loading="lazy">
            </button>
        </div>
    </div>
</section>

<div class="screenshot-overlay" id="screenshot-overlay">
    <img src="" alt="">
    <button type="button" class="screenshot-nav screenshot-prev" aria-label="Previous screenshot" hidden>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>
    <button type="button" class="screenshot-nav screenshot-next" aria-label="Next screenshot" hidden>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>
</div>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
