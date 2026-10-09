<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Il mio profilo</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="my-profile-page" class="hidden"></span>

<aside class="left padding-10 border-box">
</aside>
<main class="border-box">
    <?php
    if (check_authorisation(2)) {
        ?>
        <script>
            loadNotificationsActual(true);
            readAllNotifications();
        </script>
        <div class="main-text">
            <h1 class="padding-10">Tutte le notifiche</h1>
            <div id="all-notifications">
            </div>
        </div>
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
