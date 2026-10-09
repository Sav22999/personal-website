<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Buy me a coffee";
    $current_page = "donate";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="center-content">
        <div class="clearfix margin-top-minus10">
            <div class="projects-div clearfix">
                <div class="project project-no-min-height project-50 background-white-color text-black-color padding-20"
                     onclick="location.href='https://liberapay.com/Sav22999/'">
                    <img src="/old/images/icons/liberapay.png" class="width30 height30">
                    <h1 class="project-h1 font-size-30">LiberaPay</h1>
                </div>
                <div class="project project-no-min-height project-50 background-white-color text-black-color padding-20"
                     onclick="location.href='https://www.paypal.me/saveriomorelli'">
                    <img src="/old/images/icons/paypal.png" class="width30 height30">
                    <h1 class="project-h1 font-size-30">PayPal</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->