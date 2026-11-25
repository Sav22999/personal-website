<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Timeline: Accented letters";
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
            <a href="https://github.com/Sav22999/accented-letters-addons" class="just-link">
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
                        <time>v1.0 • 4 June 2019</time>
                        First release of the add-on
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.2 • 20 December 2019</time>
                        Added translations for the titles in many languages (English, French, Spanish, Italian, German,
                        Portuguese, Chinese, Indian, Japanese, Arabic)
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.0 • 16 July 2020</time>
                        • New icon
                        <br>
                        • The add-on now supports 40 new characters (total 66)
                        <br>
                        • Some improvements
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.1 • 28 June 2021</time>
                        Improvements to the UI
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.1 [Chromium-based] • 16 December 2023</time>
                        Published on Google Chrome Web Store and on Microsoft Edge Addons
                    </div>
                </li>
                <li>
                    <div>
                        <time>v3.0 • 26 May 2026</time>
                        A fully refreshed version with new features and improvements: customisable experience, new
                        characters, and improved performance.
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