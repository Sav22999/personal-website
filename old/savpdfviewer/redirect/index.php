<html>
<head>
    <?php
    header('Access-Control-Allow-Origin: *');

    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
    global $path_wordoftheday;
    ?>
    <title>Sav PDF Viewer &#8211; Generate a new redirect link</title>
    <link rel="stylesheet" href="/old/style/admin-forms.css"/>
    <link rel="icon" href="/old/images/projects/sav-pdf-viewer-pro.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="./redirect-generator.js"></script>
</head>
<body>
<main class="padding-10 border-box">
    <?php
    global $authorised;
    //if (variables_permission_yes_or_not(9) || (isset($_GET["auth"]) && getGoodString($_GET["auth"]) == "roberto")) {
    if (variables_permission_yes_or_not(9)) {
        ?>
        <h1>Generate a new redirect link</h1>
        <div class="main-text">
            <input type="text" placeholder="CODICE" class="textbox width-100-perc" id="code-text"/>
        </div>
        <div class="main-buttons text-right">
            <input type="submit" value="Generate" class="button submit no-margin-bottom no-margin-right"
                   onclick="generate_redirect_link_savpdfviewer()"/>
        </div>
        <?php
    } else {
        echo("<div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
    }
    ?>
</main>

<?php show_admin_bar(); ?>
</body>
</html>
