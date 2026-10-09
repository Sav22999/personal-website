<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Nuovo ingrediente</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="new-ingredient-page" class="hidden"></span>

<div id="pop-up-display-inserting" class="fullscreen-message pop-up-display-message">
    Inserimento ingrediente in corso...
</div>
<aside class="left">
</aside>
<main class="padding-10 border-box">
    <?php
    if (check_authorisation(7)) {
        if (!isset($_GET["multiple"])) {
            ?>
            <h1 class="display-block-inline">Nuovo ingrediente</h1>
            <small><i>Inserire tutti gli ingredienti al singolare</i></small>
            <div class="main-text">
                <input type="text" placeholder="Nome dell'ingrediente" class="textbox width-100-perc capitalize-text"
                       id="ingredient-text" oncontextmenu="return false;" autofocus/>
                <input class="textbox width-100-perc hidden" id="ingredient-type-text" placeholder="" disabled/>
                <span id="ingredient-type-container">
                    <button class="button ingredients-button button-start no-margin no-box-shadow first-element-choosing"
                            id="ingredients-button0"
                            onclick="setIngredientType(0,'g')">grammi
                    </button>
                    <button class="button ingredients-button margin-left-minus-5 no-margin no-box-shadow button-middle"
                            onclick="setIngredientType(1,'ml')">
                        millilitri
                    </button>
                    <button class="button ingredients-button margin-left-minus-5 button-end no-margin no-box-shadow"
                            onclick="setIngredientType(2,'n')">numero
                    </button>
                </span>
            </div>
            <div class="main-buttons text-right">
                <input type="submit" value="Aggiungi" class="button submit no-margin-bottom no-margin-right"
                       onclick="finishAndAddIngredient()"/>
            </div>
            <div class="padding-10 text-left">
                <a href="./?multiple">Modalità inserimento multipla</a>
            </div>
            <?php
        } else {
            ?>
            <h1 class="display-block-inline">Nuovi ingredienti</h1>
            <small><i>Inserire tutti gli ingredienti al singolare</i></small>
            <div class="main-text">
                <textarea placeholder="Ingredienti: [Nome ingrediente|Tipo(g,ml,n)]"
                          class="textarea width-100-perc capitalize-text"
                          id="ingredients-text" oncontextmenu="return false;" autofocus></textarea>
            </div>
            <div class="main-buttons text-right">
                <input type="submit" value="Aggiungi" class="button submit no-margin-bottom no-margin-right"
                       onclick="finishAndAddIngredients()"/>
            </div>
            <div class="padding-10 text-left">
                <a href="./">Modalità inserimento singolo</a>
            </div>
            <?php
        }
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
