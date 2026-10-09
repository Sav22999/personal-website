<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Timeline: CV Project";
    $url_opengraph = "https://www.saveriomorelli.com/projects/cv-project/opengraph.png";
    $current_page = "details-project";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a> {{*{{separator}}*}} <a href='" . get_url("projects") . "' class='header_path'>Projects</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
    <div class="center-content padding-default">
        <div class="text-center">
            <a href="https://github.com/Sav22999/common-voice-android" class="just-link">
                <button class="margin-5 font-family-basic">See the project on GitHub</button>
            </a>
        </div>
    </div>
</div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="center-content clearfix">
        <div class="timeline">
            <ul>
                <li>
                    <div>
                        <time>v0.0.1 alpha • 29 November 2019</time>
                        First release of the app (just a draft and internal test)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.0 beta • 20 April 2020</time>
                        • Implemented Speak section
                        <br>
                        • Updated translations
                        <br>
                        • Added Anonymous statistics
                        <br>
                        • Fixed many bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.1 beta • 22 April 2020</time>
                        • Implemented "Daily goal"
                        <br>
                        • Added message dialog when a new version is available
                        <br>
                        • Added Turkish translation
                        <br>
                        • Updated English strings
                        <br>
                        • Updated all languages
                        <br>
                        • Fixed some bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.1.1 • 23 April 2020</time>
                        First stable release
                        <br>
                        • App promoted to "Stable" channel
                        <br>
                        • Removed Aptoide
                        <br>
                        • Message dialogs (and daily goal dialog) now follow app theme (dark/light)
                        <br>
                        • Improvements to the UI
                        <br>
                        • Fixed bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.2 • 26 April 2020</time>
                        • Added "Recording indicator sound", if turned on it reproduce a sound when you start/stop to
                        record a sentence (in Speak)
                        <br>
                        • Added "Check for updates" option in Settings
                        <br>
                        • Added "Experimental featues" in Settings
                        <br>
                        • Added shortcut in Settings: "Translate the app on Crowdin" and "See app statistics"
                        <br>
                        • Removed "Donate to the developer" because it's agains google play store policy
                        <br>
                        • Added "Abort confirmation dialogs in Settings" in Settings
                        <br>
                        • Added "Report" button
                        <br>
                        • Added "Record again" icon in Speak after stop the recording
                        <br>
                        • Fixed some bugs in the UI (ladscape in Listen and Speak redisegned)
                        <br>
                        • Hide "Experimental features" for now
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.2.1 • 30 April 2020</time>
                        • Updated translations
                        <br>
                        • Fixed some bugs (check updates, button "listen" not correctly anchored, ect.)
                        <br>
                        • Optimised some code
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.2.2 • 2 May 2020</time>
                        Updated languages (French and Interlingua now are completely translated),
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.2.3 • 3 May 2020</time>
                        • Some improvements
                        <br>
                        • Updated Anonymous Statistics (v2 API) -> now it's sent as POST request (anonymous statistics
                        now
                        are caught also if they are disabled -> if they are turned off app sent "public=false" otherwise
                        "public=true").
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.3.1 • 7 May 2020</time>
                        • Fixed a bug in Dashboard -> You must do the log in to set daily goal
                        <br>
                        • When you log out, it reset also daily goal
                        <br>
                        • Improved "check for updates"
                        <br>
                        • Updated languages
                        <br>
                        • Improved performance
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.4 • 9 May 2020</time>
                        • Updated languages
                        <br>
                        • Implemented gestures #74
                        <br>
                        • Implemented "skip recording confirmation" #75
                        <br>
                        • Fixed bug in daily goal (for android 7).
                        <br>
                        • Added seekbar in First-run speak & listen
                        <br>
                        • Updated screenshots
                        <br>
                        • Added Persian (and Estonian)
                        <br>
                        • Updated screenshots
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.1 • 11 May 2020</time>
                        Fixed a bug in Dashboard (for Statistics)
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.2 • 17 May 2020</time>
                        • Fixed many bugs
                        <br>
                        • Added "Buy me a coffee" in F-Droid and GitHub version
                        <br>
                        • Added message "Review the app on Google Play Store" for Play Store version
                        <br>
                        • Added message "The app is not (completed) translated …"
                        <br>
                        • Added check when you run the app if the session id of the user is expired
                        <br>
                        • Fix a bug with gestures
                        <br>
                        • Added Arabic and Catalan
                        <br>
                        • Translation in Tamil (ta) completed.
                        <br>
                        • Updated all translations
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.3 • 18 May 2020</time>
                        • Listen section now works again: Mozilla changed API Response so the previously versions don't
                        work!
                        <br>
                        • Updated Arabian translation
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.4 • 18 May 2020</time>
                        Mozilla restored the old API Response (the app now supports both systems)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.0 • 19 June 2020</time>
                        • The app was developed from zero.
                        <br>
                        • New UI
                        <br>
                        • Offline mode
                        <br>
                        • Top contributors (of Common Voice)
                        <br>
                        • App statistics
                        <br>
                        • New options in Settings
                        <br>
                        • Sending of recordings/validations in background
                        <br>
                        • Fixed many bugs
                        <br>
                        • Telegram group: https://t.me/sav_projects/6
                        <br>
                        • New animations
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.0.2 • 8 July 2020</time>
                        • Renamed to "CV Android"
                        <br>
                        • Added "Review on Google Play" for Google Play version
                        <br>
                        • Added "Report website bugs" message
                        <br>
                        • Added "Read the Mozilla Common Voice Terms of Service"
                        <br>
                        • Added "Read the guidelines for validations"
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.0.3 • 13 July 2020</time>
                        • Fixed a bug that caused the app to crash in Listen
                        <br>
                        • Some general improvements
                        <br>
                        • Fixed some other bugs
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.0.4 • 1 August 2020</time>
                        Replaced the domain: voice.mozilla.org -> commonvoice.mozilla.org
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.1 • 24 September 2020</time>
                        • Updated/New badges
                        <br>
                        • General improvements
                        <br>
                        • Fixed many bugs
                        <br>
                        • Login with verification code
                        <br>
                        • Clips validation guidelines button also in the first-run tutorial
                        <br>
                        • Improved the Profile section
                        <br>
                        • Added Splash-screen
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.2 • 6 January 2021</time>
                        • Added "daily goal progress bar", in Listen and Speak
                        <br>
                        • Added the option which able to share a message when you achieve the daily goal
                        <br>
                        • Updated "Top contributors"
                        <br>
                        • Show the sentence of the clip just when you finish to listen to it
                        <br>
                        • Reorganised Settings
                        <br>
                        • Now you can show an icon instead of the Report button, in Speak and Listen
                        <br>
                        • Android 11 (API 30) are now natively supported
                        <br>
                        • Updated Georgian code and added FI (Finnish), LG (Luganda) and LT (Lithuanian)
                        <br>
                        • Daily goal increased to 500 (before was 200)
                        <br>
                        • Added the "app usage" (anonymously)
                        <br>
                        • You can now disable Offline mode
                        <br>
                        • Added "Auto" theme (based on hours), Light and Dark continue to be available
                        <br>
                        • You can now reset app data in the app
                        <br>
                        • Added animation in Listen and improved that in Speak
                        <br>
                        • App now available also on Huawei AppGallery
                        <br>
                        • Added log feature, so you can attach the log file
                        <br>
                        • You can see your the app string which identify you
                        <br>
                        • General improvements and fixed many bugs
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.2.1 • 7 January 2021</time>
                        Fixed some bugs in 2.2 about language
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.2.2 • 14 January 2021</time>
                        • Fixed some bugs (radiobuttons animation, message dialog for "language changed" in "en",
                        offline
                        mode bug, etc.)
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.2.3 • 23 January 2021</time>
                        • Fixed some bugs (radiobuttons animation, message dialog for "language changed" in "en",
                        offline
                        mode bug, etc.)
                        <br>
                        • Updated languages
                        <br>
                        • Version code 131: fixed an issue with F-Droid caused by Google Core libraries
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.3 • 8 March 2021</time>
                        • App available also on Amazon AppStore
                        <br>
                        • Added ad banner in the Google Play version (GPS)
                        <br>
                        • Text-size customisation
                        <br>
                        • Added message banner
                        <br>
                        • Languages now are loaded from server
                        <br>
                        • New icon and new name
                        <br>
                        • Improved Dailygoal message dialog
                        <br>
                        • Fixed some bugs
                        <br>
                        • Added “Common Voice Playbook” and “Sentence Collector” in Useful links
                        <br>
                        • Daily progressbar now can be “coloured”
                        <br>
                        • Added “Copy” button when is shown the userid-app string
                        <br>
                        • Added a new icon for external links in useful links
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/release/2-3/" class="just-link">⇾ Release
                            notes</a>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.3.1 • 16 March 2021</time>
                        Fixed some bugs and issues with the 2.3 version
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.3.2 • 1 April 2021</time>
                        • Fixed some bugs
                        <br>
                        • Updated languages
                        <br>
                        • New message boxes
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.3.4 • 8 April 2021</time>
                        • Added motivational sentences in Speak and Listen
                        <br>
                        • When you are not logged in and you try to set a daily goal, now the app shows to you also a
                        button to “Log in now”
                        <br>
                        • To avoid to Reset app data, now the app shows to you a message where you need to confirm to
                        reset data
                        <br>
                        • To avoid to lose recording in Speak now it’s shown a confirmation message when you recorded a
                        sentence and you’re try to “go back” or “skip”
                        <br>
                        • The “reject” button in Listen now is shown after about 1 second (exactly 900ms)
                        <br>
                        • Fixed a bug: The “Ad Banner” options (in Settings > Advanced) are now shown just in the Google
                        Play version
                        <br>
                        • Updated PayPal link for donation
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/release/2-3-4/" class="just-link">⇾ Release
                            notes</a>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.3.4.3 • 10 April 2021</time>
                        • Fixed the bug with the daily goal
                        <br>
                        • Fixed a bug in Speak and Listen (about the progress bar)
                        <br>
                        • Fixed a bug in Listen (about the sentence hidden)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.3.5 • 17 April 2021</time>
                        • Improved animations and fixed a UI bug in Listen
                        <br>
                        • Improved the SplahScreen
                        <br>
                        • Introduced a "Dynamically Min-Height" in Speak and Listen, based on display device
                        <br>
                        • Fixed a bug in Speak and Listen about the progress bar, in addition now the progress bar is
                        black if the daily goal is not set, if the day contributions are "0", so the progress bar is
                        hidden
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.3.6 • 27 April 2021</time>
                        • Added speed control in Speak and Listen
                        <br>
                        • Fixed two bugs in Listen and Speak
                        <br>
                        • Fixed a bug with the Experimental feature (Light sentence box for the Light theme)
                        <br>
                        • Fixed other minor bugs
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.3.6.1 • 28 April 2021</time>
                        Fixed a bug with fy-NL, sv-SE and zh-CN
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.3.6.5 • 16 May 2021</time>
                        • Speak and Listen sentence-boxes font-family now is "Roboto" (a serif font-family), to improve
                        the read experience
                        <br>
                        • Fixed a UI bug with the experimental feature
                        <br>
                        • Updated all languages and strings
                        <br>
                        • Copy feature for "Info dialog" (in Speak and Listen)
                        <br>
                        • "Skip prevention" dialog, in Speak also during the "recording"
                    </div>
                </li>
                <li>
                    <div>
                        <time>Donation campaign • 21 May 2021</time>
                        <script>
                            document.write(
                                twemoji.parse("Launched the \"Get free stickers with donation\" initiative 😍 😍, to encourage users to donate 💪‍")
                            );
                        </script>
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/get-stickers/" class="just-link">⇾ Learn
                            more about the initiative</a>
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.3.7 • 28 May 2021</time>
                        <script>
                            document.write(
                                twemoji.parse("• ✨ New: Now you can customise the number of the clips/sentences when you are in Offline mode (from 10 to 500).") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: All requests from the app are “marked” as “source: sav_android” (as Mozilla required)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Added information for the app-usage statistics, to improve the experience") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: The ad-banner in Listen and Speak now is at the bottom, in this way should be less annoying") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Improved the UI in Speak and Listen, in fact the progress bar now is thinner") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Updated all languages and translations") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Bugs with the UI in Listen and Speak") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: General improvements and fixed minor bugs")
                            );
                        </script>
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/release/2-3-7/" class="just-link">⇾ Release
                            notes</a>
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.3.8 • 7 June 2021</time>
                        <script>
                            document.write(
                                twemoji.parse("• ✨ New: You can now customise gestures in Listen and Speak. (Settings > Customise gestures)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Added “Opened the app X days in a row”") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Improved gestures in Speak and Listen: now you have a visual-guide when during the gesture and you can see the action icon as well (see image below)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Added “InfoMessageDialog”, “WarningMessageDialog”, “StandardMessageDialog” and “OtherMessageDialog”") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Improved the Speak section (added an alert when you try to report a sentence and you started to recorded)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Updated badges (updated with the new icon)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Updated gestures icons") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Fixed the bug with the daily goal (when you achieved it, it would stop the current recording or clip)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Fixed a bug in Speak and Listen (the “loading at loop”)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Fixed some bugs in the UI (in Listen)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Level restored in Profile") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Fixed other bugs in Listen and Speak") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: General improvements and fixed minor bugs")
                            );
                        </script>
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/release/2-3-8/" class="just-link">⇾ Release
                            notes</a>
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.4 • 12 September 2021</time>
                        <script>
                            document.write(
                                twemoji.parse("• ‼️ Important: Since this release, the app won't be supported for Android 6.0, Android 7.0 and Android 7.1 anymore. The app will support just Android 8.0 or later (API level 26 or higher)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Generic notifications (for example, when you get a new badge or when you level up)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Daily goal notifications (you can set up to two different alerts in a day!)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: The app is now also \"accessibility-friendly\"") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: \"Push to talk\" feature in Speak (in Settings > Experimental features)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: \"Contribution criteria\" icon in Speak and Listen, and the link in Useful links") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Added new gestures for Speak and Listen") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Customise the destination API server (Advanced action. Be careful!)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Removed the button \"Send recording\" which is replaced by the icon in Speak. This isan optimisation especially for small-display devices") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Changed some icons and improved the experience in Speak and Listen (for example:now the \"Stop listening\" icon is green, the \"Stop recording\" icon is red)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Updated all languages and translations") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Replaced all \"bit.ly\" links with the direct ones") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Now it's sent also \"API level\" and \"Android version\" when you contribute via the app") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Now \"Level up\" and (generic) \"New badge\" have a different message") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: The title style of standard message dialogs is different now (bold and not italic)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Improved spinners style") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: Some problems with icons (\"report\", \"offline mode\" and \"telegram\")") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: General improvements and fixed minor bugs")
                            );
                        </script>
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/release/2-4/" class="just-link">⇾ Release
                            notes</a>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.4.0.1 • 15 December 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• 🔄 Updated: All languages") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Some links")
                            );
                        </script>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.4.0.2 • 10 February 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• ✨ New: Inserted a new parameter in actionDetails") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: All languages") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: General improvements and fixed minor bugs")
                            );
                        </script>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.4.0.3 • 8 May 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• 🔄 Updated: All languages")
                            );
                        </script>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.4.0.4 • 23 May 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• 🐞 Fixed: General improvements and fixed minor bugs") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: All languages")
                            );
                        </script>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.4.0.5 • 31 May 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• 🐞 Fixed: A bug for API 31+ devices")
                            );
                        </script>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.4.0.6 • 6 September 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• 🔄 Updated: All languages")
                            );
                        </script>
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.5 • 12 September 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• ✨ New: Added progress bar of downloaded clips and sentences in Settimgs > Offline mode") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Added button «Clear offline data» to clear downloaded data in Settings > Advanced") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Upload and download only when WiFi is connected in Settimgs > Experimental") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Languages") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Moved some features from Experimental to UI") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: bug in Settings") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: bug with Offline sentences and clips") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: General improvements and fixed minor bugs")
                            );
                        </script>
                        <br>
                        <br>
                        <a href="https://www.saveriomorelli.com/commonvoice/release/2-5/" class="just-link">⇾ Release
                            notes</a>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.5.0.1 • 27 October 2022</time>
                        <script>
                            document.write(
                                twemoji.parse("• 🔄 Updated: Improved Offline mode section in Settings") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Improved download of offline sentences and clips")
                            );
                        </script>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.5.1 • 18 May 2023</time>
                        <script>
                            document.write(
                                twemoji.parse("• ✨ New: Invert \"Yes\" and \"No\" buttons (Settings > Listen)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• ✨ New: Enable \"long press\" to accept or reject a clip to avoid errors (Settings > Listen)") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🐞 Fixed: A bug Android 12 and permissions") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Updated all languages") +
                                twemoji.parse("<br>") +
                                twemoji.parse("• 🔄 Updated: Various improvements")
                            );
                        </script>
                    </div>
                </li>
                <li>
                    <div>
                        <time>8 July 2026</time>
                        App discontinued.
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->