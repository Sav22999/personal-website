<?php
$current_page = 'projects';
$title = 'FoodTrack';
$description = 'Keep track of the food in your fridge, freezer and pantry, get alerts before it expires, share spaces and shopping lists with your household, plan your weekly meals and find recipes with what you already have. A project by Saverio Morelli.';
$canonical = '/projects/about/foodtrack/';
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
    <h1 class="section-title">FoodTrack</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/foodtrack.png" alt="FoodTrack icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">A free web app to manage the food at home and reduce waste. Add items
                to your fridge, freezer or pantry with their expiry date and get notifications before they go off.
                Organise items into spaces that you can share with other household members, so everyone sees what
                is available. Create shopping lists that sync across the household, plan weekly meals with a drag-and-drop
                calendar, and discover recipes based on the ingredients you already have. The app is installable
                as a PWA and is also available on Android.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Web app</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Web, Android</span>
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
                <a href="/projects/foodtrack/" class="book-btn book-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Open app
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">The idea</h2>
        <p>FoodTrack was born from a simple observation: too much food ends up in the bin because people forget what
            they have at home. Students living away from their family for the first time, busy professionals who
            shop in a rush, parents juggling meals for a household &mdash; they all share the same pain point.
            Existing apps either focus on recipes alone or on shopping lists alone, but none of them connect the
            dots between <em>what is already in the kitchen</em>, <em>when it expires</em> and <em>what you can
                cook with it</em>.</p>
        <p>The core vision is a single app that tracks every food item at home &mdash; fridge, freezer, pantry
            &mdash; with its expiry date, warns the user before anything goes off, and then suggests recipes
            that use those soon-to-expire ingredients first. Around that core, the app adds household
            <strong>spaces</strong> (each with its own icon and colour), shareable <strong>shopping lists</strong>
            that sync across devices, and a <strong>nutri-score</strong> label (A&ndash;E) on every product so
            you can make informed choices at a glance.</p>
        <p>What sets FoodTrack apart from competitors such as Cookpad, Bring!, Frigo Magic or Samsung Food is
            the <strong>expiry-safety information</strong>: when a product passes its date, FoodTrack does not just
            say &ldquo;expired&rdquo; &mdash; it explains whether the food might still be safe to consume
            (as with many dry or canned goods) or should definitely be discarded, helping to reduce unnecessary
            waste without compromising health.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Design process</h2>
        <p>The project followed a full user-centred design cycle: <strong>user research</strong> with personas
            and competitive analysis, three rounds of <strong>paper sketches</strong> (low-fidelity), a
            <strong>medium-fidelity wireframe</strong> built in Figma, and finally a <strong>high-fidelity
                prototype</strong> with the chosen colour palette and typography.</p>
        <p>Four mood boards were explored, each rooted in colour psychology applied to food: orange
            (vitality), brown (earth and balance), purple (opulence) and green (freshness). The final palette
            uses <strong>green as the primary colour</strong> &mdash; the most natural association with fresh
            food across cultures &mdash; paired with an orange accent for calls to action, because orange
            conveys the idea of doing and creating.</p>
        <p>At every fidelity stage the prototype was <strong>tested with real users</strong> from the target
            groups. Feedback was incorporated iteratively: for example, the &ldquo;Add&rdquo; section was
            removed from the navigation bar after the first sketch because testers found it confusing, expiry
            alerts were made dismissible, and shopping-list items were split into &ldquo;to buy&rdquo; and
            &ldquo;in the cart&rdquo; sections after testers struggled with the single list.</p>
        <p>The app was then implemented in <strong>Flutter</strong> (Dart), closely following the high-fidelity
            prototype. It features three main sections &mdash; Shopping Lists, Spaces and Recipes &mdash; with
            a bottom navigation bar, floating action buttons and bottom sheets for quick actions.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-screenshots">
        <h2 class="addon-screenshots-title">Sketches, prototypes and implementation</h2>

        <h3 class="screenshot-label">Low-fidelity</h3>
        <div class="screenshot-grid">
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Low-fidelity paper sketch of the main flow (Spaces, Products)">
                <img src="/images/projects/screenshots/foodtrack/lowfi-main.jpg"
                     alt="Low-fidelity paper sketch of the main flow: Spaces, Products and navigation"
                     width="719" height="933" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Low-fidelity paper sketch of the secondary flow (Shopping lists)">
                <img src="/images/projects/screenshots/foodtrack/lowfi-secondary.jpg"
                     alt="Low-fidelity paper sketch of the secondary flow: Shopping lists and product management"
                     width="618" height="861" loading="lazy">
            </button>
        </div>

        <h3 class="screenshot-label">Medium-fidelity</h3>
        <div class="screenshot-grid">
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Medium-fidelity wireframe of the Spaces screens">
                <img src="/images/projects/screenshots/foodtrack/medfi-spaces.png"
                     alt="Medium-fidelity Figma wireframe: Spaces screens with product forms and alerts"
                     width="1203" height="533" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Medium-fidelity wireframe of the Shopping list screens">
                <img src="/images/projects/screenshots/foodtrack/medfi-shopping.png"
                     alt="Medium-fidelity Figma wireframe: Shopping list screens with cart and products"
                     width="1203" height="532" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Medium-fidelity wireframe of the Recipes screens">
                <img src="/images/projects/screenshots/foodtrack/medfi-recipes.png"
                     alt="Medium-fidelity Figma wireframe: Recipes screens with search and detail"
                     width="1203" height="667" loading="lazy">
            </button>
        </div>

        <h3 class="screenshot-label">High-fidelity</h3>
        <div class="screenshot-grid portrait">
            <button type="button" class="screenshot"
                    aria-label="Enlarge: High-fidelity prototype of the Spaces screen with expiry alerts">
                <img src="/images/projects/screenshots/foodtrack/hifi-spaces.png"
                     alt="High-fidelity Figma prototype: Spaces screen with expiry alert and space cards"
                     width="339" height="755" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: High-fidelity prototype of the Shopping lists screen">
                <img src="/images/projects/screenshots/foodtrack/hifi-shopping.png"
                     alt="High-fidelity Figma prototype: Shopping lists with birthday cake and house icons"
                     width="339" height="755" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: High-fidelity prototype of the Shopping list detail">
                <img src="/images/projects/screenshots/foodtrack/hifi-shopping-detail.png"
                     alt="High-fidelity Figma prototype: Shopping list detail with products and cart section"
                     width="339" height="755" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: High-fidelity prototype of the Recipe detail screen">
                <img src="/images/projects/screenshots/foodtrack/hifi-recipe.png"
                     alt="High-fidelity Figma prototype: Recipe detail with ingredients and preparation steps"
                     width="339" height="755" loading="lazy">
            </button>
        </div>

        <h3 class="screenshot-label">Implementation (v1)</h3>
        <div class="screenshot-grid portrait">
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Spaces screen with Frigorifero, Dispensa and Cantina">
                <img src="/images/projects/screenshots/foodtrack/spaces-main.png"
                     alt="Spaces screen with Frigorifero, Dispensa and Cantina"
                     width="338" height="727" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Expiry and expired product alert cards on the Spaces screen">
                <img src="/images/projects/screenshots/foodtrack/spaces-alerts.png"
                     alt="Expiry and expired product alert cards on the Spaces screen"
                     width="338" height="727" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Product detail bottom sheet with Nutriscore rating">
                <img src="/images/projects/screenshots/foodtrack/product-detail.png"
                     alt="Product detail bottom sheet with Nutriscore rating"
                     width="340" height="755" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Add product screen with food catalogue and emoji icons">
                <img src="/images/projects/screenshots/foodtrack/add-product.png"
                     alt="Add product screen with food catalogue and emoji icons"
                     width="338" height="727" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Shopping lists overview with weekly and special lists">
                <img src="/images/projects/screenshots/foodtrack/shopping-lists.png"
                     alt="Shopping lists overview with weekly and special lists"
                     width="338" height="727" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Shopping list detail with cart suggestion tip">
                <img src="/images/projects/screenshots/foodtrack/shopping-list-cart.png"
                     alt="Shopping list detail with cart suggestion tip"
                     width="338" height="727" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Recipes section with search and recipe cards">
                <img src="/images/projects/screenshots/foodtrack/recipes.png"
                     alt="Recipes section with search and recipe cards"
                     width="338" height="727" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Recipe detail with ingredients and availability check">
                <img src="/images/projects/screenshots/foodtrack/recipe-detail.png"
                     alt="Recipe detail with ingredients and availability check"
                     width="340" height="755" loading="lazy">
            </button>
        </div>

        <h3 class="screenshot-label">Implementation (v2 &mdash; current)</h3>
        <div class="screenshot-grid portrait">
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Spaces screen with Frigorifero, Freezer and Dispensa">
                <img src="/images/projects/screenshots/foodtrack/v2-spaces.png"
                     alt="V2 Spaces screen with Frigorifero, Freezer and Dispensa cards"
                     width="338" height="731" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Add product screen with food catalogue and barcode scanner">
                <img src="/images/projects/screenshots/foodtrack/v2-add-product.png"
                     alt="V2 Add product screen with search, barcode scanner and food catalogue"
                     width="338" height="731" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Recipes section with search, filters and recipe cards">
                <img src="/images/projects/screenshots/foodtrack/v2-recipes.png"
                     alt="V2 Recipes section with search, filters, meal planner and recipe cards"
                     width="338" height="731" loading="lazy">
            </button>
            <button type="button" class="screenshot"
                    aria-label="Enlarge: Recipe detail with photo, ingredients and servings">
                <img src="/images/projects/screenshots/foodtrack/v2-recipe-detail.png"
                     alt="V2 Recipe detail with photo, ingredients, servings and meal plan button"
                     width="338" height="731" loading="lazy">
            </button>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Project documents</h2>
        <p class="addon-timeline-external">The complete design documents are available for download below. They
            cover user research, sketches, wireframes, prototypes, user testing and the implementation report.</p>
        <div class="timeline-list">
            <div class="timeline-item">
                <span class="timeline-version">Assignment 1</span>
                <span class="timeline-date">PDF &middot; 6 pages</span>
                <p>System concept statement, target users, personas, competitive analysis of six existing apps,
                    and the prioritised list of fourteen functional requirements.</p>
                <a href="/projects/about/foodtrack/docs/assignment-1.pdf" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download
                </a>
            </div>
            <hr style="grid-column:1/-1; border:none; border-top:1px solid var(--border-light); margin:8px 0;">
            <div class="timeline-item">
                <span class="timeline-version">Assignment 2</span>
                <span class="timeline-date">PDF &middot; 20 pages</span>
                <p>Three rounds of paper sketches (low-fidelity), the medium-fidelity wireframe in Figma,
                    mood boards with colour-psychology rationale, the high-fidelity prototype, and the results
                    of user testing at each stage.</p>
                <a href="/projects/about/foodtrack/docs/assignment-2.pdf" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download
                </a>
            </div>
            <hr style="grid-column:1/-1; border:none; border-top:1px solid var(--border-light); margin:8px 0;">
            <div class="timeline-item">
                <span class="timeline-version">Implementation report</span>
                <span class="timeline-date">PDF &middot; 17 pages</span>
                <p>Side-by-side comparison of every screen between the high-fidelity prototype and the final
                    Flutter implementation, documenting the differences and the reasoning behind each
                    deviation.</p>
                <a href="/projects/about/foodtrack/docs/implementation-report.pdf" target="_blank" rel="noopener"
                   class="book-btn book-btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download
                </a>
            </div>
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

<?php include_once(dirname(__DIR__, 3) . '/include/footer.php'); ?>

</body>
</html>
