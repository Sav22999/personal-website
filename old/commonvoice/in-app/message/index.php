<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
    global $path_wordoftheday;
    ?>
    <title>CV Project &#8211; Nuovo messaggio in-app</title>
    <link rel="stylesheet" href="/old/style/admin-forms.css"/>
    <link rel="icon" href="/old/images/projects/cv-android.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/commonvoice/js/message-banners.js"></script>
</head>
<body>
<div id="fullscreen-inserting-message" class="fullscreen-message pop-up-display-message">
    Inserting the message...
</div>
<main class="padding-10 border-box">
    <?php
    global $authorised;
    //if (variables_permission_yes_or_not(9) || (isset($_GET["auth"]) && getGoodString($_GET["auth"]) == "roberto")) {
    if (variables_permission_yes_or_not(9)) {
        ?>
        <h1>Nuovo messaggio in-app</h1>
        <div class="main-text">
            <input type="text" placeholder="Utente al quale inviare il messaggio (user ::CVAppSav)"
                   class="textbox width-100-perc"
                   id="user-text"/>
            <div class="width-100-perc display-block position-relative">
                <select class="textbox width-50-perc float-left" id="type-text" oncontextmenu="return false;">
                    <option value="" selected>Banner</option>
                    <option value="1">Pop-up normale</option>
                    <option value="5">Pop-up info</option>
                    <option value="6">Pop-up help</option>
                    <option value="7">Pop-up warning</option>
                    <option value="8">Pop-up news/changelog</option>
                    <option value="9">Pop-up tip</option>
                </select>
                <select class="textbox width-50-perc float-left" id="source-text" oncontextmenu="return false;">
                    <option value="" selected>Tutti gli store</option>
                    <option value="GPS">Google Play</option>
                    <option value="FDGH">F-Droid / GitHub</option>
                    <option value="HAG">Huawei AppGallery</option>
                    <option value="AAS">Amazon AppStore</option>
                </select>
            </div>
            <div class="width-100-perc display-block position-relative">
                <input type="number" placeholder="Version code" class="textbox width-20-perc float-left"
                       id="version-code-text" list="version-code-text-suggestions" oncontextmenu="return false;"/>
                <datalist id="version-code-text-suggestions">
                    <option value="130">
                    <option value="131">
                    <option value="132">
                    <option value="133">
                    <option value="134">
                    <option value="135">
                    <option value="136">
                    <option value="137">
                    <option value="138">
                    <option value="139">
                    <option value="140">
                    <option value="141">
                </datalist>
                <select class="textbox width-40-perc float-left" id="able-to-close-text" oncontextmenu="return false;">
                    <option value="true" selected>Permetti di nascondere messaggio</option>
                    <option value="false">Non permettere di nascondere messaggio</option>
                </select>
                <select class="textbox width-40-perc float-left" id="language-text" oncontextmenu="return false;">
                    <option value="" selected>Tutte le lingue</option>
                    <?php
                    $languages = get_supported_languages_with_full_native_name();
                    foreach ($languages as $code => $details) {
                        echo '<option value="' . $code . '">' . $code . ' - ' . $details["native"] . ' (' . $details["english"] . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="width-100-perc display-block position-relative">
                <input type="date" placeholder="Data (da)" class="textbox width-50-perc float-left"
                       id="date-start-text" oncontextmenu="return false;"/>
                <input type="date" placeholder="Data (a)" class="textbox width-50-perc float-left"
                       id="date-end-text" oncontextmenu="return false;"/>
            </div>
            <textarea placeholder="Testo del messaggio" class="textarea width-100-perc height-100"
                      id="text-text"></textarea>
            <hr>
            <input type="text" placeholder="Testo Button1" class="textbox width-100-perc"
                   id="button1-text"/>
            <input type="text" placeholder="Link Button1" class="textbox width-100-perc"
                   id="button1-link-text"/>
            <hr>
            <input type="text" placeholder="Testo Button2" class="textbox width-100-perc"
                   id="button2-text"/>
            <input type="text" placeholder="Link Button2" class="textbox width-100-perc"
                   id="button2-link-text"/>

            <input type="text" placeholder="Link Button2" class="textbox width-100-perc hidden"
                   id="get-auth" value="<?php if (isset($_GET['auth'])) {
                echo $_GET['auth'];
            } ?>" disabled readonly hidden/>
        </div>
        <div class="main-buttons text-right">
            <input type="submit" value="Aggiungi" class="button submit no-margin-bottom no-margin-right"
                   onclick="insert_message()"/>
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
