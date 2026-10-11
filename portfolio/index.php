<?php
$current_page = 'portfolio';
$title = 'Portfolio';
$description = 'Portfolio of Saverio Morelli — frontend developer and UX designer. Open-source apps, browser extensions, and digital products.';
$canonical = '/portfolio/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__) . '/include/nav.php'); ?>

<section class="portfolio-hero">
    <div class="portfolio-hero-content">
        <p class="section-label">Portfolio</p>
        <h1>Designing &amp; building<br>digital products.</h1>
        <p class="portfolio-hero-sub">Frontend developer and UX designer based in Italy. I create open-source tools, apps, and browser extensions — always free, always private, always ad-free.</p>
    </div>
    <div class="portfolio-stats">
        <div class="portfolio-stat">
            <span class="portfolio-stat-value">10+</span>
            <span class="portfolio-stat-label">Projects shipped</span>
        </div>
        <div class="portfolio-stat">
            <span class="portfolio-stat-value">100K+</span>
            <span class="portfolio-stat-label">Users reached</span>
        </div>
        <div class="portfolio-stat">
            <span class="portfolio-stat-value">7+</span>
            <span class="portfolio-stat-label">Years open source</span>
        </div>
        <div class="portfolio-stat">
            <span class="portfolio-stat-value">5</span>
            <span class="portfolio-stat-label">Browser extensions</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="portfolio-section-header">
        <h2>What I do</h2>
        <p>I focus on building things that are useful, accessible, and respectful of user privacy.</p>
    </div>
    <div class="portfolio-skills">
        <div class="portfolio-skill">
            <div class="portfolio-skill-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6"/>
                    <polyline points="8 6 2 12 8 18"/>
                </svg>
            </div>
            <h3>Frontend development</h3>
            <p>Responsive websites, single-page apps, and Progressive Web Apps with a focus on performance and accessibility.</p>
            <ul class="portfolio-skill-tags">
                <li>HTML / CSS / JS</li>
                <li>Vue.js</li>
                <li>PHP</li>
                <li>Responsive design</li>
            </ul>
        </div>
        <div class="portfolio-skill">
            <div class="portfolio-skill-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M12 18h.01"/>
                </svg>
            </div>
            <h3>App development</h3>
            <p>Native Android apps and browser extensions — from concept to publishing on Google Play and Firefox Add-ons.</p>
            <ul class="portfolio-skill-tags">
                <li>Android</li>
                <li>WebExtensions</li>
                <li>Google Play</li>
            </ul>
        </div>
        <div class="portfolio-skill">
            <div class="portfolio-skill-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838.838-2.872a2 2 0 0 1 .506-.855z"/>
                </svg>
            </div>
            <h3>UX &amp; UI design</h3>
            <p>User-centered interfaces from research to hi-fi prototypes. Wireframing, usability testing, and visual design.</p>
            <ul class="portfolio-skill-tags">
                <li>Figma</li>
                <li>User research</li>
                <li>Prototyping</li>
                <li>Usability testing</li>
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="portfolio-section-header">
        <h2>Selected work</h2>
        <p>A few highlights from my projects — all open-source.</p>
    </div>

    <div class="portfolio-featured">
        <a href="/projects/sav-pdf-viewer/" class="portfolio-featured-card">
            <div class="portfolio-featured-meta">
                <span class="portfolio-featured-tag">Android</span>
                <span class="portfolio-featured-tag">100K+ users</span>
            </div>
            <h3>Sav PDF Viewer</h3>
            <p>A private, open-source PDF reader for Android with no ads and no tracking. Over 100,000 installs on Google Play.</p>
            <span class="portfolio-featured-link">
                View project
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
        <a href="/projects/notefox/" class="portfolio-featured-card">
            <div class="portfolio-featured-meta">
                <span class="portfolio-featured-tag">Browser extension</span>
                <span class="portfolio-featured-tag">Open source</span>
            </div>
            <h3>Notefox</h3>
            <p>Take notes on any website. Private, open-source, with cloud sync across devices. Built for Firefox.</p>
            <span class="portfolio-featured-link">
                View project
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
        <a href="/projects/emoticolor/" class="portfolio-featured-card">
            <div class="portfolio-featured-meta">
                <span class="portfolio-featured-tag">Web app</span>
                <span class="portfolio-featured-tag">Thesis project</span>
            </div>
            <h3>Emoticolor</h3>
            <p>An emotion-based social network designed as a master's thesis. Ethical design, emotional literacy, and privacy by design.</p>
            <span class="portfolio-featured-link">
                View project
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
        <a href="/projects/emoji/" class="portfolio-featured-card">
            <div class="portfolio-featured-meta">
                <span class="portfolio-featured-tag">Browser extension</span>
            </div>
            <h3>Emoji</h3>
            <p>Copy or insert any emoji with a click. Search by keywords, color, or gender. Built for Firefox.</p>
            <span class="portfolio-featured-link">
                View project
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
    </div>

    <div class="portfolio-all-projects">
        <a href="/projects/" class="portfolio-all-link">
            See all projects
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

<section class="section">
    <div class="portfolio-section-header">
        <h2>Experience</h2>
    </div>
    <div class="portfolio-experience">
        <div class="portfolio-exp-item">
            <div class="portfolio-exp-period">Apr 2024 — Present</div>
            <div class="portfolio-exp-content">
                <h3>Frontend App Developer &amp; UX Designer</h3>
                <p class="portfolio-exp-company">Fondazione Bruno Kessler (FBK)</p>
                <p class="portfolio-exp-desc">Designing and developing user interfaces for research-driven digital products.</p>
            </div>
        </div>
        <div class="portfolio-exp-item">
            <div class="portfolio-exp-period">Sep 2022 — Aug 2023</div>
            <div class="portfolio-exp-content">
                <h3>Computer Science Teacher</h3>
                <p class="portfolio-exp-company">Professional High School, Rovereto</p>
            </div>
        </div>
        <div class="portfolio-exp-item">
            <div class="portfolio-exp-period">Jan 2022 — Jul 2022</div>
            <div class="portfolio-exp-content">
                <h3>Mathematics Teacher</h3>
                <p class="portfolio-exp-company">Professional High School, Rovereto</p>
            </div>
        </div>
        <div class="portfolio-exp-item">
            <div class="portfolio-exp-period">Dec 2025 — Present</div>
            <div class="portfolio-exp-content">
                <h3>Indoor Volleyball Referee</h3>
                <p class="portfolio-exp-company">Federazione Italiana Pallavolo (FIPAV)</p>
            </div>
        </div>
        <div class="portfolio-exp-item">
            <div class="portfolio-exp-period">Oct 2021 — Present</div>
            <div class="portfolio-exp-content">
                <h3>Swimming Instructor</h3>
                <p class="portfolio-exp-company">Buonconsiglio Nuoto, Trento</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="portfolio-section-header">
        <h2>Education</h2>
    </div>
    <div class="portfolio-education">
        <div class="portfolio-edu-item">
            <div class="portfolio-edu-degree">
                <h3>M.Sc. Multimedia Communication and Information Technologies</h3>
                <p class="portfolio-edu-school">University of Udine</p>
            </div>
            <div class="portfolio-edu-detail">
                <span class="portfolio-edu-year">2024 — 2026</span>
                <span class="portfolio-edu-grade">110/110 cum laude</span>
            </div>
        </div>
        <div class="portfolio-edu-item">
            <div class="portfolio-edu-degree">
                <h3>B.Sc. Computer Science</h3>
                <p class="portfolio-edu-school">University of Trento</p>
            </div>
            <div class="portfolio-edu-detail">
                <span class="portfolio-edu-year">2019 — 2024</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="portfolio-section-header">
        <h2>Volunteering &amp; community</h2>
    </div>
    <div class="portfolio-community">
        <div class="portfolio-community-item">
            <span class="portfolio-community-role">Mozilla Rep</span>
            <span class="portfolio-community-org">Mozilla</span>
            <span class="portfolio-community-year">2020 — 2025</span>
        </div>
        <div class="portfolio-community-item">
            <span class="portfolio-community-role">Volunteer</span>
            <span class="portfolio-community-org">Mozilla Italia</span>
            <span class="portfolio-community-year">2017 — 2025</span>
        </div>
        <div class="portfolio-community-item">
            <span class="portfolio-community-role">Volunteer</span>
            <span class="portfolio-community-org">A.M.A. — Auto Mutuo Aiuto</span>
            <span class="portfolio-community-year">Since 2023</span>
        </div>
        <div class="portfolio-community-item">
            <span class="portfolio-community-role">Volunteer</span>
            <span class="portfolio-community-org">Croce Rossa Italiana</span>
            <span class="portfolio-community-year">2021 — 2026</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="portfolio-cta-section">
        <h2>Let's work together.</h2>
        <p>Interested in collaborating? Drop me a message — no commitment, just a conversation.</p>
        <a href="/contact-me/" class="services-cta">
            <span>Get in touch</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
