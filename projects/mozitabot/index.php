<?php
$current_page = 'projects';
$title = 'MozItaBot';
$description = 'MozItaBot — the official Telegram bot of the Mozilla Italia community, with quick access to its groups, support, news, meetings and projects. A discontinued project by Saverio Morelli.';
$canonical = '/projects/mozitabot/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "MozItaBot",
            "applicationCategory": "CommunicationApplication",
            "operatingSystem": "Telegram",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/MozillaItalia/mozitahub_bot",
            "softwareVersion": "1.6.3.1",
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
    <h1 class="section-title">MozItaBot</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/mozitabot.png" alt="MozItaBot icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">MozItaBot (@MozItaBot, first called MozIta Hub) was the official Telegram bot
                of Mozilla Italia. It pointed newcomers to the community&#x27;s groups and channels, and offered support
                and contribution info, the Firefox vademecum PDFs, the monthly meetings, active projects, the rules and
                opt-in news alerts. Commands included /gruppi, /supporto, /vademecum, /prossimoMeeting, /progetti and
                /avvisiOn. Admins could broadcast messages, manage projects and channels, and download logs, and from
                late 2020 the bot also forwarded Mozilla Italia tweets to the MozItaNews channel. It was written in
                Python 3 with telepot, and with tweepy for the Twitter integration.</p>
            <div class="book-meta">
                <div class="book-meta-item">
                    <span class="book-meta-label">Type</span>
                    <span class="book-meta-value">Telegram bot</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Platforms</span>
                    <span class="book-meta-value">Telegram</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">License</span>
                    <span class="book-meta-value">Open source</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Latest version</span>
                    <span class="book-meta-value">v1.6.3.1</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2018 – 2021 <span class="book-meta-note">· 3 years</span></span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Role</span>
                    <span class="book-meta-value">Creator and lead developer (2018 – 2020), with contributions from the Mozilla Italia community</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/MozillaItalia/mozitahub_bot" target="_blank" rel="noopener"
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
    <div class="addon-timeline">
        <h2 class="addon-timeline-title">Changelog</h2>
        <div class="timeline-list">
            <div class="timeline-item">
                <span class="timeline-version">v1.6.3.1</span>
                <span class="timeline-date">27 August 2021</span>
                <p>The Design and Marketing groups were merged and Amazon was removed from the projects list. Group
                    links are now set in one place, and requirements pin a patched telepot (by Damiano Gualandri). The
                    Twitter integration had been switched off in May 2021.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.3</span>
                <span class="timeline-date">18 November 2020</span>
                <p>Added a Marketing group button and texts, and Firefox Reality in the projects list (contributed by
                    Damiano Gualandri).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.2</span>
                <span class="timeline-date">15 November 2020</span>
                <p>Removed the IoT and &#x27;Become a volunteer&#x27; groups.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.1</span>
                <span class="timeline-date">16 October 2020</span>
                <p>Forwarded tweets get a &#x27;View tweet&#x27; button and an RT label for retweets. Added the Common
                    Voice vademecum (/vademecumCV) and the L10n group, and updated the projects lists.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.0</span>
                <span class="timeline-date">3 October 2020</span>
                <p>Twitter integration: new Mozilla Italia tweets are forwarded automatically to the MozItaNews channel
                    (by Damiano Gualandri). Admins can download today&#x27;s or yesterday&#x27;s log.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5.4</span>
                <span class="timeline-date">3 October 2020</span>
                <p>Improved the admin help message.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5.3</span>
                <span class="timeline-date">2 October 2020</span>
                <p>The channel commands are now grouped under /admin canale. Fixed the error message shown for a missing
                    channel; input is case-insensitive, and &#x27;@&#x27; is added automatically when needed.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5.2</span>
                <span class="timeline-date">2 October 2020</span>
                <p>Clarified the admin section and documented the admin IDs in code comments.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5.1</span>
                <span class="timeline-date">2 October 2020</span>
                <p>Fixed a misspelling in the admin help.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5.0</span>
                <span class="timeline-date">2 October 2020</span>
                <p>Admins can manage a list of channels and preview, send or broadcast messages to them. The code was
                    refactored (contributed by Damiano Gualandri).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.1</span>
                <span class="timeline-date">28 April 2020</span>
                <p>Users the bot can no longer reach are removed automatically from the news-alert and all-users lists.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4</span>
                <span class="timeline-date">27 April 2020</span>
                <p>Broadcasts to users now pause briefly between messages and tell the admin when they finish.
                    Exceptions are logged to file.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.6</span>
                <span class="timeline-date">15 April 2020</span>
                <p>Added a function that logs exceptions to file (contributed by Ilyas).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.5</span>
                <span class="timeline-date">5 April 2020</span>
                <p>New /social command and button with the community&#x27;s social media links (contributed by Ilyas).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.4</span>
                <span class="timeline-date">26 November 2019</span>
                <p>Fixed bug #33: the bot now answers inline-button presses.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.3</span>
                <span class="timeline-date">20 October 2019</span>
                <p>Admins can download log files with /admin scarica YYYY MM DD.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.2</span>
                <span class="timeline-date">7 October 2019</span>
                <p>Fixed the /vademecumGenerale and /vademecumTecnico commands and updated the vademecum PDFs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.1</span>
                <span class="timeline-date">6 October 2019</span>
                <p>Bug fixes, including in the admin area. The rules link now points to the community copy, the code was
                    optimised and the vademecum PDFs were updated.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3</span>
                <span class="timeline-date">6 October 2019</span>
                <p>Simplified the help, renamed commands (call to meeting, prossimacall to prossimoMeeting) in camelCase
                    and added /aiuto. A new button opens the YouTube channel, and commands are no longer case-sensitive.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.8</span>
                <span class="timeline-date">12 April 2019</span>
                <p>Some improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.7</span>
                <span class="timeline-date">5 April 2019</span>
                <p>Added an admin &#x27;preview&#x27; command for checking messages before sending, and fixed managing
                    community projects in admin mode.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.6</span>
                <span class="timeline-date">19 March 2019</span>
                <p>Code-quality improvements: the Pylint score went from 6.55 to 7.40 out of 10.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.5</span>
                <span class="timeline-date">19 March 2019</span>
                <p>Code-quality improvements: the Pylint score went from 5.27 to 6.55 out of 10.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.4</span>
                <span class="timeline-date">19 March 2019</span>
                <p>Code-quality improvements: the Pylint score went from 3.82 to 5.27 out of 10. Giovanni Francesco
                    Solone revised the button texts.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.3</span>
                <span class="timeline-date">18 March 2019</span>
                <p>Some fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.2</span>
                <span class="timeline-date">17 March 2019</span>
                <p>More fixes. The bot shows a notice while it sends the vademecum PDFs, and the telegram_events module
                    was updated.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.0</span>
                <span class="timeline-date">9 March 2019</span>
                <p>Message-event handling moved to the shared telegram_events.py module. Giovanni Francesco Solone
                    revised the bot&#x27;s texts.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.9</span>
                <span class="timeline-date">8 March 2019</span>
                <p>More of the bot&#x27;s texts moved into frasi.json.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.8</span>
                <span class="timeline-date">8 March 2019</span>
                <p>The bot&#x27;s texts are now loaded from the frasi.json file.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.7</span>
                <span class="timeline-date">19 February 2019</span>
                <p>The vademecum PDFs are now sent straight from the bot instead of as links (#26). Some fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.6</span>
                <span class="timeline-date">17 February 2019</span>
                <p>Added an admin and polished several messages.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.5</span>
                <span class="timeline-date">17 February 2019</span>
                <p>New admins. Chat history is now saved as daily log files, and the 2017–2019 meeting etherpads were
                    added.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.4</span>
                <span class="timeline-date">17 February 2019</span>
                <p>Some improvements.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.3</span>
                <span class="timeline-date">5 February 2019</span>
                <p>Fixed /info. Some messages now use Markdown formatting.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.1</span>
                <span class="timeline-date">2 February 2019</span>
                <p>Markdown support in the bot&#x27;s messages and in admin alerts, and an updated warning for users
                    without a username.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.0</span>
                <span class="timeline-date">2 February 2019</span>
                <p>Monthly meeting recordings are now split by year, and the admin meeting commands take a year.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.4</span>
                <span class="timeline-date">1 February 2019</span>
                <p>Added emoji to buttons and messages. Minor fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.3</span>
                <span class="timeline-date">1 February 2019</span>
                <p>The bot was renamed from &#x27;MozIta Hub&#x27; to &#x27;MozItaBot&#x27;. Fixes and corrections.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.2</span>
                <span class="timeline-date">30 January 2019</span>
                <p>Added an &#x27;Avvisi&#x27; (news alerts) button and new icons.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.1</span>
                <span class="timeline-date">27 January 2019</span>
                <p>First 1.x version: encoding fixes for Linux, and the version is printed at startup.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.2.2 alpha</span>
                <span class="timeline-date">18 January 2019</span>
                <p>The bot token is now read from a config.ini file (#19, contributed by Daniele Scasciafratte). Other
                    updates.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.2.1 alpha</span>
                <span class="timeline-date">4 January 2019</span>
                <p>Added a footer to news-alert messages, plus small fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.2 alpha</span>
                <span class="timeline-date">17 December 2018</span>
                <p>Added exception handling and more detailed logs, plus news alerts users can switch on or off. Monthly
                    meetings and projects now load from JSON files, and admins can broadcast messages and manage
                    meetings and projects.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.5 alpha</span>
                <span class="timeline-date">10 November 2018</span>
                <p>Code improvements and a new contributor in the credits.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.4 alpha</span>
                <span class="timeline-date">5 November 2018</span>
                <p>/prossimacall now gives the exact date of the next monthly meeting (the first Friday of the month).
                    Added the November meeting and fixed the code.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.3 alpha</span>
                <span class="timeline-date">27 October 2018</span>
                <p>Small fixes and a link to the October 2018 meeting recording.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.2 alpha</span>
                <span class="timeline-date">26 October 2018</span>
                <p>Fixed some errors in the code.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.1 alpha</span>
                <span class="timeline-date">26 October 2018</span>
                <p>Fixed an error in the code.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.0 alpha</span>
                <span class="timeline-date">26 October 2018</span>
                <p>Added the active Mozilla and Mozilla Italia projects, the monthly meetings with an estimate of the
                    next one, and command fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0.3 alpha</span>
                <span class="timeline-date">1 October 2018</span>
                <p>Added FAQs and timestamps in the logs, and reorganised the commands (with contributions from Martin
                    Ligabue).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0.2 preview</span>
                <span class="timeline-date">29 September 2018</span>
                <p>Renamed commands (/azioni to /help, /support to /supporto) and reordered the menus, with
                    contributions from Martin Ligabue.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0.1 preview</span>
                <span class="timeline-date">7 September 2018</span>
                <p>First version of the &#x27;MozIta Hub&#x27; bot. Commands link to the Mozilla Italia Telegram groups
                    and News channel, support, the forum, the vademecum, feedback, how to contribute, and info.</p>
            </div>
        </div>
    </div>
</section>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
