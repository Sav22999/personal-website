<?php if (!empty($maintenance_bypass_active)): ?>
    <div class="maintenance-banner" aria-hidden="true"><span>MAINTENANCE</span></div>
<?php endif; ?>
<nav>
    <div class="nav-inner">
        <a href="/" class="nav-logo<?php if (isset($current_page) && $current_page === 'home') echo ' is-home'; ?>"><span class="nav-logo-icon" aria-hidden="true"></span>Saverio Morelli</a>
        <ul class="nav-links">
            <li>
                <a href="/about-me/"<?php if (isset($current_page) && $current_page === 'about-me') echo ' class="active"'; ?>>About</a>
            </li>
            <li>
                <a href="/projects/"<?php if (isset($current_page) && $current_page === 'projects') echo ' class="active"'; ?>>Projects</a>
            </li>
            <?php /* <li>
                <a href="/services/"<?php if (isset($current_page) && $current_page === 'services') echo ' class="active"'; ?>>Services</a>
            </li> */ ?>
            <li>
                <a href="/contact-me/"<?php if (isset($current_page) && $current_page === 'contact-me') echo ' class="active"'; ?>>Contact</a>
            </li>
            <li>
                <a href="/donate/"<?php if (isset($current_page) && $current_page === 'donate') echo ' class="active"'; ?>>Donate</a>
            </li>
        </ul>
        <button class="nav-toggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>
