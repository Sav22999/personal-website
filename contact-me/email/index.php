<?php
$current_page = 'contact-me';
$title = 'Send an email';
$description = 'Send an email to Saverio Morelli — bug reports, feature requests, collaborations, or general inquiries.';
$canonical = '/contact-me/email/';

$config = require dirname(__DIR__, 2) . '/.mail-config.php';
$form_ts = time();
$form_token = $form_ts . '.' . hash_hmac('sha256', 'form:' . $form_ts, $config['altcha_secret']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script async defer src="https://cdn.jsdelivr.net/npm/altcha/dist/altcha.min.js" type="module"></script>
</head>
<body>

<?php include_once(dirname(__DIR__, 2) . '/include/nav.php'); ?>

<div class="page-header">
    <p class="section-label">Contact</p>
    <h1 class="section-title">Send an email</h1>
</div>

<section class="section">
    <form id="contact-form" class="contact-form" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label for="name">Full name <span class="field-badge required">Required</span></label>
                <input type="text" id="name" name="name" required placeholder="Your full name">
            </div>
            <div class="form-group">
                <label for="email">Email <span class="field-badge required">Required</span></label>
                <input type="email" id="email" name="email" required placeholder="your@email.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="reason">Reason <span class="field-badge required">Required</span></label>
                <select id="reason" name="reason" required>
                    <option value="" disabled selected>Select a reason</option>
                    <option value="General">General</option>
                    <option value="Bug report">Bug report</option>
                    <option value="Feature request">Feature request</option>
                    <option value="Collaboration">Collaboration</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="project">Project <span class="field-badge optional">Optional</span></label>
                <select id="project" name="project">
                    <option value="" selected>None</option>
                    <option value="Notefox">Notefox</option>
                    <option value="Sav PDF Viewer">Sav PDF Viewer</option>
                    <option value="Emoji">Emoji</option>
                    <option value="savmrl.it">savmrl.it</option>
                    <option value="Accented Letters">Accented Letters</option>
                    <option value="Limite">Limite</option>
                    <option value="Word of the Day">Word of the Day</option>
                    <option value="HTML per tutti">HTML per tutti</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="message">Message <span class="field-badge required">Required</span></label>
            <textarea id="message" name="message" required rows="5" placeholder="Write your message here..."></textarea>
        </div>

        <div class="form-row form-row-three">
            <div class="form-group">
                <label for="project_version">Project version <span class="field-badge optional">Optional</span></label>
                <input type="text" id="project_version" name="project_version" placeholder="e.g. 2.1.0">
            </div>
            <div class="form-group">
                <label for="os">OS <span class="field-badge optional">Optional</span></label>
                <select id="os" name="os">
                    <option value="" selected>Not specified</option>
                    <option value="Windows">Windows</option>
                    <option value="macOS">macOS</option>
                    <option value="Linux">Linux</option>
                    <option value="Android">Android</option>
                    <option value="iOS">iOS</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="browser">Web browser <span class="field-badge optional">Optional</span></label>
                <select id="browser" name="browser">
                    <option value="" selected>Not specified</option>
                    <option value="Chrome">Chrome</option>
                    <option value="Firefox">Firefox</option>
                    <option value="Safari">Safari</option>
                    <option value="Edge">Edge</option>
                    <option value="Opera">Opera</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="language">Language <span class="field-badge optional">Optional</span></label>
            <select id="language" name="language">
                <option value="" selected>Not specified</option>
                <option value="English">English</option>
                <option value="Italian">Italian</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">
            <input type="text" name="company" id="company" tabindex="-1" autocomplete="off" value="">
        </div>
        <input type="hidden" name="ts" id="ts" value="<?= htmlspecialchars($form_token) ?>">

        <div class="form-altcha">
            <altcha-widget challengeurl="/contact-me/challenge.php"></altcha-widget>
        </div>

        <p class="form-privacy">Your data will only be used to respond to your inquiry. It will not be shared with third
            parties or used for any other purpose.</p>

        <div id="form-status" class="form-status" hidden></div>

        <button type="submit" class="form-submit" id="form-submit" disabled>
            <span class="form-submit-text">Send message</span>
            <svg class="form-submit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>
</section>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
