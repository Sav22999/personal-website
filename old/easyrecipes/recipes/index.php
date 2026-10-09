<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="my-profile-page" class="hidden"></span>


<div id="pop-up-display-seeing" class="fullscreen-message pop-up-display-message">
    Caricamento dell'anteprima della ricetta in corso...
</div>

<aside class="left padding-10 border-box">
</aside>
<main class="padding-10 border-box">
    <?php
    if (check_authorisation(2)) {
        if (!isset($_GET["id"])) {
            echo("<div class='message'>Inserire l'id della ricetta che si desidera visualizzare</div>");
            return;
        }
        global $your_privileges;

        global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");

        $recipe_id = $_GET["id"];
        $status = -1;

        $sql = "SELECT * FROM `recipes` WHERE `id`='" . $recipe_id . "' AND `user_inserted`='" . $_SESSION["session_id"] . "'";
        if ($your_privileges >= 8) {
            $sql = "SELECT * FROM `recipes` WHERE `id`='" . $_GET["id"] . "'";
        }
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
            } else {
                //Error
                return;
            }
        }
        ?>
        <script>loadRecipePreview(<?php echo $recipe_id;?>, -1);</script>
        <div id="cover-recipe-preview">
        </div>
        <div class="main-text">
            <h1 class="padding-10" id="title-recipe-preview"></h1>
            <div id="ingredients-information-recipe-preview" class="grid-container">
                <div class="grid-child">
                    <div id="ingredients-recipe-preview">
                        <h1>Ingredienti</h1>
                        <ul>
                            <li>: <span class="quantity-recipe-preview"></span></li>
                        </ul>
                    </div>
                </div>
                <div class="grid-child">
                    <div id="information-recipe-preview">
                        <ul>
                            <li>: <span class="quantity-recipe-preview"></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="preparation-preview-section">
                <p class="preparation-recipe-preview"></p>
                <div class="image-recipe-preview"></div>
            </div>
            <div id="tags-recipe-preview"
                 class="padding-10 background-color-secondary margin-minus-10 no-margin-bottom no-margin-top text-white-color"></div>
            <div id="others-recipe-preview"
                 class="padding-10 background-color-primary margin-minus-10 no-margin-top text-white-color"></div>
        </div>
        <?php
        $c->close();
        ?>
        <?php
    } else {
        echo("<div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
    }
    ?>
</main>
<aside class="right padding-10 border-box">
</aside>
<?php include_once($path_easyrepices . "/include/pop-up.php"); ?>
</body>
</html>
