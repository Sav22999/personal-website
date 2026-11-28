<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Privacy policy – Word of the Day";
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
<div class="background-transparent-color text-black-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
    <div class="center-content padding-default">
        <div class="text-justify">
            <h1>Word of the Day <b>doesn't</b> collect any personal data.</h1>
            <br>
            If you use Google Play, you must read Google Privacy Policy.
            <br>
            In the edition for Google Play you can find Advertising: this depends on Google, so you should read those
            terms.
            <br><br>
            To get more detailed about the app, read the file <a class="just-link"
                                                                 href="https://github.com/Sav22999/word-of-the-day/README.md">"READ
                ME" on GitHub</a>.
        </div>
    </div>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->