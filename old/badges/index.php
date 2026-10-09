<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Badges “Saverio Morelli”";
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
<div class="text-center center width100 background-transparent-color text-black-color border-radius-0">
    <img src="/old/images/badges/a-project-of.png" width="60%" style="max-width: 300px; min-width: 200px"/>
    <br>
    <a class="just-link" href="/old/images/badges/a-project-of.png" download>
        <button>Scarica questo badge</button>
    </a><a class="just-link" href="/old/images/badges/a-project-of.svg" download>
        <button>SVG</button>
    </a>
    <hr class="margin-bottom30">
    <img src="/old/images/badges/realised-by.png" width="60%" style="max-width: 300px; min-width: 200px"/>
    <br>
    <a class="just-link" href="/old/images/badges/realised-by.png" download>
        <button>Scarica questo badge</button>
    </a><a class="just-link" href="/old/images/badges/realised-by.svg" download>
        <button>SVG</button>
    </a>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->