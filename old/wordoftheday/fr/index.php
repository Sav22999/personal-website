<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
    global $path_wordoftheday;
    ?>
    <title>Word of the Day [fr]</title>
    <link rel="stylesheet" href="/old/wordoftheday/style.css"/>
    <link rel="icon" href="/old/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/wordoftheday/script.js"></script>
</head>
<body>
<div id="fullscreen-inserting-word" class="fullscreen-message pop-up-display-message">
    Inserting the word...
</div>
<main class="padding-10 border-box">
    <?php
    global $authorised;
    if (variables_permission_yes_or_not(10)) {
        ?>
        <input type="submit" value="Single word mode" class="button submit width-100-perc"
               onclick="location.href='./single/'"/>
        <input type="submit" value="Multiple words mode" class="button submit width-100-perc"
               onclick="location.href='./multiple/'"/>
        <input type="submit" value="All words" class="button submit width-100-perc"
               onclick="location.href='./all/'"/>
        <?php
    } else {
        echo("<div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
    }
    ?>
</main>

<?php show_admin_bar(); ?>
</body>
</html>
