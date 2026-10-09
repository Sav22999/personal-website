<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Redirect...";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();

    $redirect_to_link = "https://github.com/Sav22999/mycodeeditor";
    ?>

    <meta http-equiv="refresh" content="0; URL='<?php echo $redirect_to_link; ?>'"/>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<h1 class="text-center margin-top30 padding-default">
    Redirecting to <a class="just-link" href="<?php echo $redirect_to_link; ?>"><b
                class="text-black-color"><?php echo $redirect_to_link; ?></b></a>.
</h1>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->