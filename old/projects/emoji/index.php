<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Timeline: Emoji";
    $url_opengraph = "https://www.saveriomorelli.com/projects/emoji/opengraph.png";
    $current_page = "details-project";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a> {{*{{separator}}*}} <a href='" . get_url("projects") . "' class='header_path'>Projects</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>

        <?php redirectTo("https://www.emojiaddon.com/news", 0); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
    <div class="center-content padding-default">
        <div class="text-center">
            <a href="https://github.com/Sav22999/emoji" class="just-link">
                <button class="margin-5 font-family-basic">See the project on GitHub</button>
            </a>
        </div>
    </div>
</div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="width100 background-transparent-color text-black-color border-radius-0">
        <div class="center-content padding-default">
            Redirecting to the news page of the app... If you are not redirected, click <a
                    href="https://www.emojiaddon.com/news/" class="just-link">here</a>.
        </div>
    </div>
    <!--<div class="center-content clearfix">
        <div class="timeline">
            <ul>
                <li>
                    <div>
                        <time>v1.0 • 29 November 2019</time>
                        The first release of the add-on
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.0 • 29 January 2020</time>
                        • Added sections
                        <br>
                        • Added hundreds new emojis (now there are 905 emojis totally)!
                        <br>
                        • Improvements to the code
                        <br>
                        • Changed the font ("Source code pro" -> "Twemoji")
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.0.1 • 30 January 2020</time>
                        Removed a duplicate
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.1 • 8 April 2020</time>
                        • Added "Copied" message
                        <br>
                        • Some improvements
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.0 • 25 June 2020</time>
                        • New icon
                        <br>
                        • Added 274 new emojis (and 2 new sections)
                        <br>
                        • New feature: Searchbox to search emojis easily with keyword (in English)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.1 • 16 July 2020</time>
                        • ★★ New feature: Most used emojis ★★ Now the addon saves your most used emojis and it shows you
                        <br>
                        • them in the relative tab, so you can find and copy easily those emojis!
                        <br>
                        • 2 new emojis in Technologies
                        <br>
                        • 25 new emojis in Other
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.1.3 • 19 July 2020</time>
                        • 496 new emojis!
                        <br>
                        • Added "Sorry, I don't want" button in review message
                        <br>
                        • General improvements
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.2 • 23 July 2020</time>
                        News:
                        <br>
                        • Dark theme (or Light theme)
                        <br>
                        • Added Settings: change
                        <br>
                        • Improved UI
                        <br>
                        <br>
                        Bug fixed:
                        <br>
                        • Fixed some minor bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.4 • 4 August 2020</time>
                        • Added "Close pop-up after emoji is copied" and "Font family" (for now it's supported just
                        "Twitter" (twemoji) for Firefox Add-ons
                        <br>
                        • Published also on Microsoft Edge Addons Store, here the font-family is not "twitter" but it's
                        "Google" (noto-color-emoji)
                        <br>
                        • General improvements
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.5 • 22 August 2020</time>
                        • Added skin-tones* 💪🏻💪🏼💪🏽💪🏾💪🏿
                        <br>
                        • Added "OpenMoji" and "OS emoji font"
                        <br>
                        <br>
                        *you can change the skin-tone in Settings
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.5.2 • 29 August 2020</time>
                        Fixed some bugs in the 3.5 and 3.5.1 versions
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.7.1 • 10 October 2020</time>
                        • New 62 emojis: UNICODE 13 supported (Twitter)
                        <br>
                        • Updated OpenMoji Color
                        <br>
                        • Added a message in Settings, when you select a non-Twitter font family
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.7.2 • 19 October 2020</time>
                        • New font: OpenMoji Black (but it's always recommended the Twitter "twemoji")
                        <br>
                        • Improved UI/UX in Settings: for Skin-tone and for Auto-close
                        <br>
                        • Some other improvements
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.8 • 24 December 2020</time>
                        Added "Multi-copy" feature: you can copy more emojis per times (you need to turn on in Settings)
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.8.1 • 15 January 2021</time>
                        Added 🍀 emoji
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.8.2 • 16 January 2021</time>
                        Fixed the store name (now it shows correctly Firefox Add-ons / Microsoft Edge Add-ons / Google
                        Chrome Web Store: depends on web browser used)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.9 • 18 January 2021</time>
                        • Added shortcut Ctrl/Cmd+Alt+A
                        <br>
                        • Added buttons in Settings to buy the developer a coffee
                        <br>
                        • Improved search: click "Enter" to focus the results
                        <br>
                        • Focus the first emoji when change section
                        <br>
                        • Added the "Release notes" message
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.9.1 • 18 January 2021</time>
                        Fixed a bug in the 3.9
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.9.2 • 30 January 2021</time>
                        Added message to support my work when you opened 1'000, 10'000, 100'000, 1'000'000 and
                        10'000'000 times the addon coffee
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.10 • 5 February 2021</time>
                        • Added tooltip
                        <br>
                        • Added "I need help" button
                        <br>
                        • Added auto-save feature in Settings
                        <br>
                        • Added version number in Settings
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.10.1 • 5 February 2021</time>
                        • Fixed some bugs
                        <br>
                        • Added tooltip also in Most used section
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.10.2 • 27 February 2021</time>
                        • Improved the speed to show emojis
                        <br>
                        • Now you can change skin-tone without restart pop-up
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.10.4 • 6 April 2021</time>
                        • Improved the UI in Settings
                        <br>
                        • Fixed some bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.11 • 1 May 2021</time>
                        • Fixed a bug in "multi-copy"
                        <br>
                        • Added easter egg (write "Sav22999" or "Saverio" in the textbox)
                        <br>
                        • Added description tooltip also for the sections title
                        <br>
                        • Improved the UX/UI
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.11.1 • 30 June 2021</time>
                        Fixed a bug with "Small" > "Columns"
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.11.3 • 7 July 2021</time>
                        • Added extension-icon customisation in Settings
                        <br>
                        • Updated fonts
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.11.4 • 9 July 2021</time>
                        • Improved backend source
                        <br>
                        • Some minor fixes
                        <br>
                        • Fixed a bug with the extension-icon customisation
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.12 • 22 July 2021</time>
                        • Added quick choice of the skin-tone: press <i>Right-click</i> on an emoji and choose your
                        needed skin-tone! (warning: the skin-tone in the emoji-picker will remain with the selected
                        skin-tone, change it in Settings)
                        <br>
                        • Disabled the context-menu everywhere, except in the search-box
                        <br>
                        • Fixed minor bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.13 • 26 July 2021</time>
                        • The add-on is now translatable on <a href="https://crowdin.com/project/emoji-sav/">Crowdin</a>!
                        <br>
                        • Fixed an important bug
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.1 • 27 July 2021</time>
                        • Added translations in Arabic (ar) 🇦🇪 🇪🇭 🇸🇦, Chinese (zh-CN) 🇨🇳, Czech (cs) 🇨🇿,
                        Danish (da) 🇩🇰, Dutch (nl) 🇳🇱, Finnish (fi) 🇫🇮, French (fr) 🇫🇷, German (de) 🇩🇪, Greek
                        (el) 🇬🇷, Italian (it) 🇮🇹, Japanese (jp) 🇯🇵, Norwegian (no) 🇳🇴, Polish (pl) 🇵🇱,
                        Portuguese (pt-PT and pt-BR) 🇵🇹 🇧🇷, Romanian (ro) 🇷🇴, Russian (ru) 🇷🇺, Spanish (es-ES)
                        🇪🇸, Swedish (sv-SE) 🇸🇪, Ukrainian (uk) 🇺🇦
                        <br>
                        • Fixed some bugs
                        <br>
                        • Improved back-end code
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.2 • 28 July 2021</time>
                        • Merged pt-PT and pt-BR
                        <br>
                        • Fixed an important bug
                        <br>
                        • Updated files
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.3 • 29 July 2021</time>
                        • Fixed an issue with PT
                        <br>
                        • Added "translate" button in Settings
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.4 • 1 August 2021</time>
                        • Added more flags
                        <br>
                        • Fixed a UI (theme) bug
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.6 • 30 August 2021</time>
                        • Updated languages
                        <br>
                        • Added Korean language (ko-KR 🇰🇷)
                    </div>
                </li>
                <li>
                    <div>
                        <time>2 September 2021</time>
                        Mozilla announced the add-on is now "Recommended" and has that badge!
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.6.1 • 4 September 2021</time>
                        Added 🔮
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.6.4 • 15 November 2021</time>
                        • Updated languages
                        <br>
                        • Added 💎, 💸, 😮‍💨 and 😵‍💫
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.13.7 • 12 February 2022</time>
                        • Updated languages
                        <br>
                        • Added a new option in Settings "Insert a space between emojis" (when copy them and multi-copy
                        is enabled)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.14 • 17 March 2022</time>
                        • Added a new option in Settings "Also insert directly the emoji" (if it's enabled, the add-on
                        is able to insert in the active textarea/input the emoji directly, instead of only copy it)
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.14.2 • 10 July 2022</time>
                        • "Insert directly emoji": permissions are optional and required only after you turn on the
                        feature
                        <br>
                        • Fixed some issue with emojis
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.14.4 • 25 August 2022</time>
                        • Updated emojis to Unicode 14
                        <br>
                        • Enabled England, Wales and Scotland flags as well
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.16 • 20 March 2023</time>
                        • Added the option to customise the keyboard shortcut to open the popup
                        <br>
                        • Added some new emojis
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.16.1 • 20 March 2023</time>
                        • Improved Settings (the choosing of "emoji-size" now shows a button preview)
                        <br>
                        • Updated languages
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v3.17 • 24 March 2023</time>
                        • Improved the feature 'Insert directly the emoji' (now support also many other elements)
                        <br>
                        • Updated languages
                    </div>
                </li>
            </ul>
        </div>
    </div>-->
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->