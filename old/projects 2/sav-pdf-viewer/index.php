<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Timeline: Sav PDF Viewer Pro";
    $url_opengraph = "https://www.saveriomorelli.com/projects/sav-pdf-viewer/opengraph.png";
    $current_page = "details-project";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a> {{*{{separator}}*}} <a href='" . get_url("projects") . "' class='header_path'>Projects</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>

        <?php redirectTo("https://www.savpdfviewer.com/news/", 0); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
    <div class="center-content padding-default">
        <div class="text-center">
            <a href="https://play.google.com/store/apps/details?id=com.saverio.pdfviewer" class="just-link">
                <button class="margin-5 font-family-basic">See the project on Google Play</button>
            </a>
            <a href="https://github.com/Sav22999/sav-pdf-viewer-pro" class="just-link">
                <button class="margin-5 font-family-basic">See the project on GitHub</button>
            </a>
        </div>
    </div>
</div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="center-content padding-default">
        Redirecting to the news page of the app... If you are not redirected, click <a
                href="https://www.savpdfviewer.com/news/" class="just-link">here</a>.
    </div>
    <!--<div class="center-content clearfix">
        <div class="timeline">
            <ul>
                <li>
                    <div>
                        <time>v1.0 • 20 January 2021</time>
                        First release of the app
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.1.1 • 11 January 2021</time>
                        • Added "share" button
                        <br>
                        • Added "review" message dialog
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.2 • 1 May 2021</time>
                        Fixed a crash in Android 11
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.3 • 6 June 2021</time>
                        • Added "top bar"
                        <br>
                        • Added "full screen"
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.4 • 7 June 2021</time>
                        • Added "total pages"
                        <br>
                        • Fixed bug with orientation
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.1 • 8 June 2021</time>
                        Fixed bug with last position
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.4.2 • 8 June 2021</time>
                        Added "go to" feature
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.5 • 8 June 2021</time>
                        • Added support for PDF protected by password
                        <br>
                        • Added button "go to the top"
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.5.1 • 10 June 2021</time>
                        Added button "open new file"
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.6 • 11 June 2021</time>
                        Added the night light
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.6.1 • 21 June 2021</time>
                        Fixed fit width when change orientation
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.6.2 • 21 June 2021</time>
                        Added translations
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.6.4 • 30 August 2021</time>
                        • Fixed a bug (page is restored after orientation changing)
                        <br>
                        • Fixed a bug (the share feature didn't work correctly)
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.7 • 30 September 2021</time>
                        Top bar disappears after 5 seconds of inactivity
                    </div>
                </li>
                <li>
                    <div>
                        <time>v1.8 • 1 October 2021</time>
                        • Added "menu panel" to optimise the app for small-display devices as well
                        <br>
                        • Fixed a bug with "Night light"
                        <br>
                        • Improved performance
                    </div>
                </li>
                <li class="minor">
                    <div>
                        <time>v1.8.0.1 • 2 October 2021</time>
                        Fixed a small bug
                    </div>
                </li>

                <li class="minor">
                    <div>
                        <time>v1.8.0.2 • 12 October 2021</time>
                        Published on <a href="https://f-droid.org/it/packages/com.saverio.pdfviewer/">F-Droid</a>
                    </div>
                </li>

                <li>
                    <div>
                        <time>v1.9 • 24 April 2022</time>
                        • Added "Bookmarks": now you can add, remove and manage bookmarks
                        <br>
                        • Created the <a href="https://www.instagram.com/savpdfviewer/">Intagram account</a> of the app!
                        <br>
                        • Improved the backend code and the frontend UI
                    </div>
                </li>

                <li>
                    <div>
                        <time>v1.10 • 27 March 2023</time>
                        • Added "Scrollbar button" feature to navigate faster
                        <br>
                        • Fixed many bugs
                        <br>
                        • Improved animations (gestures) of bookmarks
                        <br>
                        • Disabled Instagram popup
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