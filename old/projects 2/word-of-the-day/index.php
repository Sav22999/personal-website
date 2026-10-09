<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Timeline: Word of the Day";
    $url_opengraph = "https://www.saveriomorelli.com/projects/word-of-the-day/opengraph.png";
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
            <a href="https://play.google.com/store/apps/details?id=com.saverio.wordoftheday_en" class="just-link">
                <button class="margin-5 font-family-basic">See the project on Google Play</button>
            </a>
            <a href="https://github.com/Sav22999/word-of-the-day" class="just-link">
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
                        <time>v1.0 • 20 January 2021</time>
                        The first release of the app (internal test).
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.1 • 21 January 2021</time>
                        • Added check Internet connection
                        <br>
                        • Option to disable ads in Settings
                        <br>
                        • Added "share" and "copy" buttons
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.2 • 26 January 2021</time>
                        • Added "source" field
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.3 • 29 January 2021</time>
                        • Added "push notification"
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.3.1 • 29 January 2021</time>
                        • Fixed some bugs
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.3.2 • 24 June 2021</time>
                        • Fixed a bug with notification
                        <br>
                        • Added a message when the device doesn't have an Internet connection
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.3.3 • 25 June 2021</time>
                        • Added the release number in Settings
                        <br>
                        • Fixed some bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.4 • 3 October 2021</time>
                        • Removed ads
                        <br>
                        • Improved some graphic details
                        <br>
                        • Improved notifications
                        <br>
                        • Fixed a bug
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.1 • 12 October 2021</time>
                        Published on <a href="https://f-droid.org/it/packages/com.saverio.wordoftheday_en/">F-Droid</a>
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.5 • 10 November 2023</time>
                        • Updated icon
                        <br>
                        • Some improvements
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