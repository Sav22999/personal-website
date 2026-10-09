<?php
$current_page = 'projects';
$title = 'HTML per tutti';
$description = 'HTML per tutti — il manuale per imparare HTML da zero, scritto da Saverio Morelli. Disponibile su Amazon in formato cartaceo ed eBook.';
$canonical = '/htmlpertutti/';
$page_lang = 'it_IT';
$og_image = 'https://www.saveriomorelli.com/htmlpertutti/images/copertina_cartaceo.png';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include_once(dirname(__DIR__) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Book",
            "name": "HTML per tutti",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "inLanguage": "it",
            "image": "https://www.saveriomorelli.com/htmlpertutti/images/copertina_cartaceo.png",
            "url": "https://www.saveriomorelli.com/htmlpertutti/",
            "bookFormat": [
                "https://schema.org/Paperback",
                "https://schema.org/EBook"
            ],
            "offers": [
                {
                    "@type": "Offer",
                    "availability": "https://schema.org/InStock",
                    "url": "https://amzn.to/2SaSRPu",
                    "priceCurrency": "EUR"
                },
                {
                    "@type": "Offer",
                    "availability": "https://schema.org/InStock",
                    "url": "https://amzn.to/2C8QrMc",
                    "priceCurrency": "EUR"
                }
            ],
            "about": "A beginner-friendly guide to learning HTML from scratch, written in Italian."
        }
    </script>
</head>
<body>

<?php include_once(dirname(__DIR__) . '/include/nav.php'); ?>

<div class="page-header">
    <a href="/projects/" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Torna ai progetti
    </a>
    <p class="section-label">Progetto</p>
    <h1 class="section-title">HTML per tutti</h1>
</div>

<section class="section">
    <div class="book-hero">
        <div class="book-cover" id="book-cover">
            <img src="/htmlpertutti/images/copertina_cartaceo.png" alt="Copertina di HTML per tutti">
        </div>
        <div class="book-info">
            <p class="book-description">Il manuale definitivo per imparare HTML da zero, scritto in italiano. Dalle basi
                ai concetti avanzati, con esempi pratici e spiegazioni chiare per chiunque voglia creare pagine web.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Autore</span>
                    <span class="book-meta-value">Saverio Morelli</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Pubblicazione</span>
                    <span class="book-meta-value">2019 <span class="book-meta-note">· ebook 2020</span></span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Lingua</span>
                    <span class="book-meta-value">Italiano</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Piattaforma</span>
                    <span class="book-meta-value"><a href="https://kdp.amazon.com" target="_blank" rel="noopener">Amazon KDP</a></span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Valutazione</span>
                    <span class="book-meta-value">5,0/5 su Amazon <span class="book-meta-note">· 3 voti</span></span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://amzn.to/2SaSRPu" target="_blank" rel="noopener" class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    </svg>
                    Acquista cartaceo
                </a>
                <a href="https://amzn.to/2C8QrMc" target="_blank" rel="noopener" class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="6" y="3" width="12" height="18" rx="2"/>
                        <line x1="12" y1="18" x2="12" y2="18.01"/>
                    </svg>
                    Acquista eBook
                </a>
            </div>
            <p class="book-note">Reindirizzamento su Amazon.it &middot; eBook disponibile gratis con Kindle
                Unlimited</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="book-errata">
        <h2 class="book-errata-title">Errori post-pubblicazione</h2>
        <div class="book-errata-content">
            <p>Non è stato segnalato alcun errore post-pubblicazione.</p>
        </div>
        <p class="book-errata-report">Per segnalare un errore, compilare il <a href="/contact-me/email/">modulo di
                contatto</a>.</p>
    </div>
</section>

<div class="book-overlay" id="book-overlay">
    <img src="/htmlpertutti/images/copertina_cartaceo.png" alt="Copertina di HTML per tutti">
</div>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
