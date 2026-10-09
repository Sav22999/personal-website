<?php
$current_page = 'projects';
$title = 'MozIta Antispam';
$description = 'MozIta Antispam — telegram bot that kept spam out of the Mozilla Italia community groups by requiring new members to read the rules and be confirmed by a verified member. A discontinued project by Saverio Morelli.';
$canonical = '/projects/mozita-antispam/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "MozIta Antispam",
            "applicationCategory": "CommunicationApplication",
            "operatingSystem": "Telegram",
            "author": {
                "@type": "Person",
                "name": "Saverio Morelli",
                "url": "https://www.saveriomorelli.com/"
            },
            "url": "https://github.com/MozillaItalia/mozitaantispam_bot",
            "softwareVersion": "1.6.5",
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
    <h1 class="section-title">MozIta Antispam</h1>
</div>

<section class="section">
    <div class="addon-hero">
        <div class="addon-icon">
            <img src="/images/projects/mozita-antispam.png" alt="MozIta Antispam icon">
        </div>
        <div class="addon-info">
            <p class="addon-description">MozIta Antispam (@mozita_antispam_bot) guarded the official Mozilla Italia
                Telegram groups. New members could not post until they had read the rules and another verified member
                had confirmed them. Spam accounts were kicked and banned from every enabled group at once, and the bot
                could also filter banned words. Admins managed the shared user lists, banned words, enabled groups,
                broadcasts and log downloads through text commands in a private chat with the bot. It was written in
                Python 3 with the telepot library.</p>
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
                    <span class="book-meta-value">v1.6.5</span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Active</span>
                    <span class="book-meta-value">2018 – 2020 <span class="book-meta-note">· 2 years</span></span>
                </div>
                <div class="book-meta-item">
                    <span class="book-meta-label">Role</span>
                    <span class="book-meta-value">Creator and lead developer, with contributions from the Mozilla Italia community</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="https://github.com/MozillaItalia/mozitaantispam_bot" target="_blank" rel="noopener"
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
                <span class="timeline-version">v1.6.5</span>
                <span class="timeline-date">20 June 2020</span>
                <p>Quick fix: the bot works in supergroups again, not only in basic groups and private chats.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.4</span>
                <span class="timeline-date">14 June 2020</span>
                <p>The bot now acts only in private chats and groups (not in channels, for example), and it now handles
                    pinned-message events, contributed by Damiano Gualandri (dag7dev).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.3</span>
                <span class="timeline-date">12 June 2020</span>
                <p>Polls are now accepted events (contributed by Damiano Gualandri), plus typo and indentation fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.2</span>
                <span class="timeline-date">15 April 2020</span>
                <p>Fixed messages being reported as unrecognised when they contained a user mention (issue #22,
                    contributed by marcodenisi).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6.1</span>
                <span class="timeline-date">7 April 2020</span>
                <p>Fixed GIF detection (#20) and improved error logging.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.6</span>
                <span class="timeline-date">26 March 2020</span>
                <p>After reading the rules, a new member now has to wait at least 30 seconds before anyone can confirm
                    them (#16, contributed by Ilyas).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.5</span>
                <span class="timeline-date">28 January 2020</span>
                <p>Bug fixes and an updated icon (#17).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.13</span>
                <span class="timeline-date">26 November 2019</span>
                <p>The bot now answers inline-button presses, which fixes the endless loading indicator (issue #14), and
                    shows feedback messages on buttons.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.12</span>
                <span class="timeline-date">16 November 2019</span>
                <p>Fixed the welcome message showing up for users who were already verified and not showing up for users
                    added by someone else. The admin alert &#x27;Cacciato&#x27; is now &#x27;Utente rimosso&#x27;.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.11</span>
                <span class="timeline-date">21 October 2019</span>
                <p>Admins can download log files straight from the bot. The admin command list is clearer and the code
                    was improved.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.10.2</span>
                <span class="timeline-date">6 September 2019</span>
                <p>Updated the rules link and added a &#x27;Read the full rules&#x27; button to the confirmation
                    message.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.10.1</span>
                <span class="timeline-date">22 July 2019</span>
                <p>Users waiting for confirmation can now send stickers and GIFs as well as text.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.10</span>
                <span class="timeline-date">1 June 2019</span>
                <p>Fixed a bug: users on the spam list no longer get a welcome message when they rejoin.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.9</span>
                <span class="timeline-date">15 May 2019</span>
                <p>Fixed the &#x27;removed&#x27; and &#x27;blocked and removed&#x27; events both firing at the same
                    time.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.8</span>
                <span class="timeline-date">14 May 2019</span>
                <p>Fixed a problem with handling users blocked through the &#x27;Block user&#x27; button.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.7</span>
                <span class="timeline-date">10 May 2019</span>
                <p>Fixed handling of users who are not on any list yet. Their messages are now deleted.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.6</span>
                <span class="timeline-date">5 May 2019</span>
                <p>Improved how users are blocked and removed (/bloccautente).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.5</span>
                <span class="timeline-date">5 May 2019</span>
                <p>Improved the user confirmation flow for both &#x27;Show rules&#x27; and &#x27;Confirm user&#x27;, and
                    updated the exceptions list.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.4</span>
                <span class="timeline-date">4 May 2019</span>
                <p>The bot no longer posts a message in the group when a user is removed.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.3</span>
                <span class="timeline-date">23 April 2019</span>
                <p>Added a check to the user-blocking procedure.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.2</span>
                <span class="timeline-date">23 April 2019</span>
                <p>Fixed a problem with the &#x27;Block user&#x27; button.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.1</span>
                <span class="timeline-date">15 April 2019</span>
                <p>Small changes to 1.4.0.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.4.0</span>
                <span class="timeline-date">15 April 2019</span>
                <p>New /benvenuto command to show the welcome message again. Blocked users can report a possible mistake
                    once, through a private chat with the bot.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.9</span>
                <span class="timeline-date">13 April 2019</span>
                <p>Fixed a syntax error introduced in 1.3.8.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.8</span>
                <span class="timeline-date">12 April 2019</span>
                <p>Bug fix for the admin &#x27;invia messaggio&#x27; (broadcast) command. The messages file was
                    reformatted with indentation (contributed by Damiano Gualandri).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.7</span>
                <span class="timeline-date">22 March 2019</span>
                <p>Updated the bot&#x27;s messages (frasi.json) and renamed some variables.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.6</span>
                <span class="timeline-date">19 March 2019</span>
                <p>Code-quality improvements: the Pylint score went from 2.92 to 7.09 out of 10.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.5</span>
                <span class="timeline-date">17 March 2019</span>
                <p>Bug fix: button presses from new or unverified users are no longer handled as regular messages.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.4</span>
                <span class="timeline-date">17 March 2019</span>
                <p>Fixes to the bot and to the telegram_events module.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.3</span>
                <span class="timeline-date">17 March 2019</span>
                <p>Various fixes. The telegram_events module was updated to 1.1.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.2</span>
                <span class="timeline-date">16 March 2019</span>
                <p>Fixed a bug in the new message-deletion function.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.1</span>
                <span class="timeline-date">16 March 2019</span>
                <p>Code split into functions and commented. The banned-words filter was switched off after a community
                    poll.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.3.0</span>
                <span class="timeline-date">9 March 2019</span>
                <p>Message-event detection moved into a new reusable module, telegram_events.py. Giovanni Francesco
                    Solone revised the bot&#x27;s texts.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.6</span>
                <span class="timeline-date">8 March 2019</span>
                <p>Bug fix.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.5</span>
                <span class="timeline-date">8 March 2019</span>
                <p>The welcome and &#x27;rules read&#x27; messages are now deleted when someone presses their buttons
                    (show rules, confirm user, block user).</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.4</span>
                <span class="timeline-date">8 March 2019</span>
                <p>Various fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.3</span>
                <span class="timeline-date">8 March 2019</span>
                <p>Removed the test commands and added private-chat admin commands to show or clear the white, black,
                    temp and spam lists.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.2</span>
                <span class="timeline-date">8 March 2019</span>
                <p>Usernames in the bot&#x27;s messages are now clickable mentions shown with the user ID.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.1</span>
                <span class="timeline-date">8 March 2019</span>
                <p>The bot&#x27;s messages and button labels now live in an editable frasi.json file.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.2.0</span>
                <span class="timeline-date">21 February 2019</span>
                <p>Messages now use HTML formatting instead of Markdown, and the welcome message has a new &#x27;Block
                    user&#x27; button.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.9</span>
                <span class="timeline-date">20 February 2019</span>
                <p>Added some test commands for admins in private chat.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.8</span>
                <span class="timeline-date">19 February 2019</span>
                <p>Messages with banned words are now deleted with a warning instead of the sender being banned. Admins
                    get private alerts when a user is removed or uses banned words.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.7</span>
                <span class="timeline-date">17 February 2019</span>
                <p>Chat history is now saved as daily log files in the history_mozitaantispam folder.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.6</span>
                <span class="timeline-date">17 February 2019</span>
                <p>Removed the IRC-bot check, which did nothing because bots cannot see other bots&#x27; messages, and
                    fixed some problems.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.5</span>
                <span class="timeline-date">11 February 2019</span>
                <p>Fixed an error introduced in 1.1.4.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.4</span>
                <span class="timeline-date">11 February 2019</span>
                <p>Handles group-photo changes and tells apart users who join or leave from users who are added or
                    removed. Other small fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.3</span>
                <span class="timeline-date">5 February 2019</span>
                <p>Small update: the confirmation message now links to the rules.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.2</span>
                <span class="timeline-date">2 February 2019</span>
                <p>Admins can see the version and last-update date by sending /help in private chat.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.1</span>
                <span class="timeline-date">2 February 2019</span>
                <p>Bug fix.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.1.0</span>
                <span class="timeline-date">2 February 2019</span>
                <p>Users without a Telegram username are no longer kicked. They are identified by their user ID instead.
                    Bot messages now support Markdown.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.3</span>
                <span class="timeline-date">1 February 2019</span>
                <p>Added emoji and made minor fixes.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.2</span>
                <span class="timeline-date">31 January 2019</span>
                <p>Spelling fix in the welcome message.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v1.0.1</span>
                <span class="timeline-date">27 January 2019</span>
                <p>First 1.x version. It prints the version and last-update date at startup.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.6 alpha</span>
                <span class="timeline-date">19 January 2019</span>
                <p>Verified users without a username are no longer banned, and the removal message is clearer. Forbidden
                    messages coming from the IRC bridge bot are deleted without a ban.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.5 alpha</span>
                <span class="timeline-date">18 January 2019</span>
                <p>Stickers now go through the banned-words filter using their emoji. The bot token moved to a
                    config.ini file.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.4 alpha</span>
                <span class="timeline-date">17 December 2018</span>
                <p>Private chats and non-enabled groups are now told apart. Added an ECCEZIONI.md list of handled
                    exceptions and an icon, and renamed the main file to antispam_mozita.py.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.3 alpha</span>
                <span class="timeline-date">4 December 2018</span>
                <p>Small fix: users without a username are handled separately from spam users.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.2 alpha</span>
                <span class="timeline-date">4 December 2018</span>
                <p>Added a small check and updated the welcome messages.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.1 alpha</span>
                <span class="timeline-date">25 November 2018</span>
                <p>Fixed some errors, handled some exceptions, and private chats are now logged too.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.3.0 alpha</span>
                <span class="timeline-date">25 November 2018</span>
                <p>Finished the admin commands: manage users, banned words and enabled groups, and send a message to
                    every enabled group. Users without a username are banned automatically.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.2.1 alpha</span>
                <span class="timeline-date">24 November 2018</span>
                <p>Fixed list import and export. The spam-list check now runs first, and join/leave messages are always
                    shown.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.2.0 preview</span>
                <span class="timeline-date">24 November 2018</span>
                <p>Lists are now imported from and exported to JSON files.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.1 preview</span>
                <span class="timeline-date">22 November 2018</span>
                <p>Edited messages and replies are now marked. The bot tells enabled groups apart from private chats,
                    and the first manual controls for admins were added.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.1.0 preview</span>
                <span class="timeline-date">20 November 2018</span>
                <p>Added the AdminList, WhiteList, BlackList, TempList and SpamList. Members must read the rules before
                    they can be confirmed. Also added a banned-words filter, chat-history logging and per-group welcome
                    messages.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0.2 preview</span>
                <span class="timeline-date">16 November 2018</span>
                <p>Added the &#x27;Confirm user&#x27; procedure. Only new members get the welcome message, and messages
                    from unverified users are deleted automatically.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0.5 preview</span>
                <span class="timeline-date">16 November 2018</span>
                <p>Early preview: the bot deletes messages from unverified users, bans spam users and welcomes new
                    members.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-version">v0.0.1 preview</span>
                <span class="timeline-date">15 November 2018</span>
                <p>First code of the antispam bot.</p>
            </div>
        </div>
    </div>
</section>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

</body>
</html>
