<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Nuova ricetta</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="new-recipe-page" class="hidden"></span>

<div id="pop-up-display-too-small" class="fullscreen-message pop-up-display-message">
    Non è possibile utilizzare questa funzione perché il display è troppo piccolo.
</div>
<div id="pop-up-display-uploading" class="fullscreen-message pop-up-display-message">
    Caricamento immagine in corso...
</div>
<div id="pop-up-display-inserting" class="fullscreen-message pop-up-display-message">
    Inserimento della ricetta in corso...
</div>
<aside class="left padding-10 border-box">
    <?php
    global $authorised;
    if ($authorised) {
        ?>
        <!-- Images inserted -->
        <div class="inserted-images-section section-new-recipe">
            <span id="images-uploaded"></span>
            <input type="file" name="image" id="upload-image" hidden/>
            <input type="button" value="Carica nuova immagine"
                   class="button add-new-image plus width-100-perc margin-top-10 no-margin-bottom"
                   id="plus-images" onclick="uploadImage()"/>
        </div>

        <!-- Cover -->
        <div class="cover-section section-new-recipe">
            <input type="button" value="+" class="button plus no-margin" id="plus-cover"
                   onclick="showPopUp('cover','left')"/>
            <input type="button" class="cover-image margin-top-10" id="cover-image" value=""/>
        </div>

        <!-- Types (categories) -->
        <div class="types-section section-new-recipe">
            <div>
                <input type="radio" value="u" name="type" onchange="changedTypes(0)" checked required hidden/>
                <input type="radio" value="v1" name="type" onchange="changedTypes(1)" required hidden/>
                <input type="radio" value="v2" name="type" onchange="changedTypes(2)" required hidden/>
                <input type="button" value="Non definita" onclick="setTypes(0)"
                       class="button types-button types-button-radio not-defined button-start no-box-shadow"/>
                <input type="button" value="Vegetariana" onclick="setTypes(1)"
                       class="button types-button types-button-radio vegetarian button-middle no-box-shadow"/>
                <input type="button" value="Vegana" onclick="setTypes(2)"
                       class="button types-button types-button-radio vegan button-end no-box-shadow"/>
            </div>
            <hr>
            <div class="margin-top-10">
                <input type="checkbox" value="g" onchange="toggleGlutenFree()" name="gluten-free" hidden>
                <input type="button" value="Senza glutine" class="button types-button gluten-free"
                       onclick="setGlutenFree()"/>
                <br>
                <input type="checkbox" value="d" onchange="toggleDairyFree()" name="dairy-free" hidden>
                <input type="button" value="Senza latte e derivati" class="button types-button dairy-free"
                       onclick="setDairyFree()"/>
            </div>
        </div>

        <!-- Ingredients -->
        <div class="ingredients-section section-new-recipe">
            <span id="all-ingredients"></span>
            <input type="button" value="+" class="button inline-block plus" id="plus-ingredients"
                   onclick="resetIngredientsPopUp();showPopUp('ingredients','left');"/>
        </div>
        <?php
    }
    ?>
</aside>
<main class="padding-10 border-box">
    <?php
    global $authorised;
    if ($authorised) {
        ?>
        <div class="main-text">
            <input type="text" placeholder="Titolo/Nome della ricetta" class="textbox title-recipe" id="title-recipe"
                   oncontextmenu="return false;" autofocus/>
            <textarea placeholder="Procedimento" class="textbox textarea preparation-recipe"
                      id="preparation-recipe" oncontextmenu="return false;"></textarea>
        </div>

        <div class="main-buttons">
            <!-- TODO Auto-save as draft every 5 minutes (ajax) -->
            <input type="button" value="Salva come bozza" class="button save-draft no-margin-bottom"
                   onclick="saveDraft(false)"/>
            <input type="submit" value="Salva e pubblica" class="button submit insert no-margin-bottom"
                   onclick="if(window.confirm('Sei sicuro di voler pubblicare questa ricetta? Successivamente non potrai apportare modifiche.')){finishAndInsert();}"/>
        </div>
        <?php
    } else {
        echo("<div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
    }
    ?>
</main>
<aside class="right padding-10 border-box">
    <?php
    global $authorised;
    if ($authorised) {
        ?>
        <!-- Preparation time -->
        <div class="preparation-time-section section-new-recipe">
            <div class="text-center">
                <input type="number" min="0" value="" placeholder="Minuti"
                       class="textbox preparation-time-value no-margin" onchange="onChangePreparationTime()"
                       oncontextmenu="return false;"/>
            </div>
        </div>

        <!-- Difficulty -->
        <div class="difficulty-section section-new-recipe">
            <input type="radio" value="1" name="difficulty" onchange="changedDifficulty(0)" hidden required/>
            <input type="radio" value="2" name="difficulty" onchange="changedDifficulty(1)" hidden required/>
            <input type="radio" value="3" name="difficulty" onchange="changedDifficulty(2)" hidden required/>
            <input type="radio" value="4" name="difficulty" onchange="changedDifficulty(3)" hidden required/>
            <input type="radio" value="5" name="difficulty" onchange="changedDifficulty(4)" hidden required/>
            <input type="radio" value="6" name="difficulty" onchange="changedDifficulty(5)" hidden required/>
            <input type="radio" value="7" name="difficulty" onchange="changedDifficulty(6)" hidden required/>

            <input type="button" value="1"
                   class="button difficulty-button button-start no-box-shadow first-element-choosing"
                   onclick="setDifficulty(0)"/>
            <input type="button" value="2"
                   class="button difficulty-button margin-left-minus-5 no-box-shadow button-middle"
                   onclick="setDifficulty(1)"/>
            <input type="button" value="3"
                   class="button difficulty-button margin-left-minus-5 no-box-shadow button-middle"
                   onclick="setDifficulty(2)"/>
            <input type="button" value="4"
                   class="button difficulty-button margin-left-minus-5 no-box-shadow button-middle"
                   onclick="setDifficulty(3)"/>
            <input type="button" value="5"
                   class="button difficulty-button margin-left-minus-5 no-box-shadow button-middle"
                   onclick="setDifficulty(4)"/>
            <input type="button" value="6"
                   class="button difficulty-button margin-left-minus-5 no-box-shadow button-middle"
                   onclick="setDifficulty(5)"/>
            <input type="button" value="7"
                   class="button difficulty-button margin-left-minus-5  button-end no-box-shadow"
                   onclick="setDifficulty(6)"/>
        </div>

        <!-- Calories -->
        <div class="calories-section section-new-recipe">
            <div class="text-center">
                <input type="number" min="0" step="0.1" value="" placeholder="Calories"
                       class="textbox calories-value no-margin" oncontextmenu="return false;"
                       onchange="onChangeCalories()"/>
            </div>
        </div>

        <!-- Origin country -->
        <div class="origin-country-section section-new-recipe">
            <input type="button" value="+" class="button plus no-margin" id="plus-country"
                   onclick="showPopUp('origin-country','right')"/>
        </div>

        <!-- Tags -->
        <div class="tags-section section-new-recipe">
            <span id="all-tags"></span>
            <input type="button" value="+" class="button plus" id="plus-tags"
                   onclick="resetTagsPopUp();showPopUp('tags','right');"/>
        </div>
        <?php
    }
    ?>
</aside>
<?php include_once($path_easyrepices . "/include/pop-up.php"); ?>
</body>
</html>
