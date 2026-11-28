<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Notefox: First run";
    $url_opengraph = "https://www.saveriomorelli.com/projects/notefox/opengraph.png";
    $current_page = "details-project";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a> {{*{{separator}}*}} <a href='" . get_url("projects") . "' class='header_path'>Projects</a>  {{*{{separator}}*}} <a class='header_path'>Notefox</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();

    //redirect to https://www.notefox.eu/help/first-run/
    $url = "https://www.notefox.eu/help/first-run/";
    header("Location: $url");
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center">
    <a href="https://github.com/Sav22999/websites-notes"><img src="/old/images/socials/github.png"
                                                              class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://addons.mozilla.org/it/firefox/addon/websites-notes/"><img src="/old/images/icons/firefox-bw.svg"
                                                                               class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://crowdin.com/project/notefox"><img src="/old/images/icons/crowdin.png"
                                                       class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://t.me/sav_projects/7"><img src="/old/images/socials/telegram.png"
                                               class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://liberapay.com/Sav22999/"><img src="/old/images/icons/liberapay-bw.svg"
                                                   class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://www.paypal.me/saveriomorelli"><img src="/old/images/icons/paypal-bw.svg"
                                                        class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
</div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="center-content clearfix">
        <img src="tutorial1.png" width="100%"/>
        <img src="tutorial2.png" width="100%"/>
    </div>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->