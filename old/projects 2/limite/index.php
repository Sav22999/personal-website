<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Timeline: Limite";
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
            <a href="https://github.com/Sav22999/limite" class="just-link">
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
                        <time>v1.0 • 25 May 2021</time>
                        The very first release of the add-on
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.0.2 • 3 June 2021</time>
                        Fixed some important bugs
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.1 • 10 July 2021</time>
                        • New icon
                        <br>
                        • Implemented notification
                        <br>
                        • Implemented badge
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.1.1 • 19 July 2021</time>
                        Fixed a bug with notification and icon badge
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.2 • 3 August 2021</time>
                        • Added the webpage to show all websites and all time spent
                        <br>
                        • Fixed a bug with a check
                        <br>
                        • Added "Import", "Export", "Delete data" features
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.0 (v2.0.0.1, v2.0.1) • 13 September 2023</time>
                        • Revisited the UI of "All time spent"
                        <br>
                        • Show only 7 days per time, so it's easier show websites
                        <br>
                        • Added icons in each buttons
                        <br>
                        • Added the toggle (switch) of the status directly from "All time spent"
                        <br>
                        • Added "categories"
                        <br>
                        • Many improvements and some bugs fixed!
                        <br>
                        • When the browser lost focus (or minimized) the timer has been paused
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.1 • 8 October 2023</time>
                        • Fixed some bugs
                        <br>
                        • Added "search" feature in "All websites"
                        <br>
                        • Added "cloud" category
                        <br>
                        • Added new websites in categories
                        <br>
                        • Added "sort-by" feature: tap on the column title to sort by that
                        <br>
                        • Some improvements back-end
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.1.1 • 8 October 2023</time>
                        • Fixed a bug with sorting (now the "sort-by" choose remains also when navigate in days, when
                        search and when refresh data)
                        <br>
                        • Fixed a screenshot
                        <br>
                        • Some minor improvements
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v2.1.2 • 8 October 2023</time>
                        • Show up to 46 characters as url to avoid UI problems (it's present the "title" hover the url
                        to
                        see the full url)
                        <br>
                        • Some minor improvements
                    </div>
                </li>
                <li>
                    <div>
                        <time>v2.2 • 16 October 2023</time>
                        • Added new column: average
                        <br>
                        • Fixed a bug with sort-by-category
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