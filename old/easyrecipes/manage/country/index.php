<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Nuovo Paese</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="new-country-page" class="hidden"></span>

<div id="pop-up-display-inserting" class="fullscreen-message pop-up-display-message">
    Inserimento Paese in corso...
</div>
<aside class="left">
</aside>
<main class="padding-10 border-box">
    <?php
    global $authorised;
    if (check_authorisation(7)) {
        ?>
        <h1>Nuovo Paese</h1>
        <div class="main-text">
            <input type="text" placeholder="Nome del Paese" class="textbox width-100-perc capitalize-text"
                   id="country-text" oncontextmenu="return false;" autofocus/>
        </div>
        <div class="main-buttons text-right">
            <input type="submit" value="Aggiungi" class="button submit no-margin-bottom no-margin-right"
                   onclick="finishAndAddCountry()"/>
        </div>
        <?php
    } else {
        echo("<div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
    }
    ?>
</main>
<aside class="right">
</aside>
<?php include_once($path_easyrepices . "/include/pop-up.php"); ?>
</body>
</html>
