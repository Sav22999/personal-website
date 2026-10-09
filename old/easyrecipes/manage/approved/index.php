<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Ricette convalidate</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="approved-page" class="hidden"></span>

<div id="pop-up-display-updating" class="fullscreen-message pop-up-display-message">
    Aggiornamento della ricetta in corso...
</div>

<aside class="left">
</aside>
<main class="padding-10 border-box">
    <h1>Ricette convalidate</h1>
    <div id="approved-section" class="margin-bottom-10">
        <div class="clearfix">
            <?php
            global $localhost_db, $username_db, $password_db, $database_easyrecipes_api, $authorised, $path_easyrepices, $path;
            if (check_authorisation(9)) {
                $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
                $c->set_charset("utf8");

                $sql = "SELECT * FROM `recipes` WHERE `status`=3 OR `status`=4 ORDER BY `date_approved` DESC";

                $r = $c->query($sql);
                if ($r->num_rows > 0) {
                    for ($i = 0; $i < $r->num_rows; $i++) {
                        $row = $r->fetch_assoc();
                        ?>
                        <div class="page-section-div" id="div-id-<?php echo $row["id"]; ?>">
                            <div class="div-approved-recipe-section">
                                <div class="div-cover"
                                     style="background-image: url('<?php echo $row["cover"]; ?>');"></div>
                                <h1 class="approved-recipe-title">
                                    <?php echo $row["title"]; ?>
                                </h1>
                                <div>
                                    <div class="clearfix">
                                        <div class="page-section-div">
                                            <div class="page-width-3 text-left margin-10 no-margin-bottom">
                                                <button id="accept-button"
                                                        class="button width-100-perc button-dark <?php if ($row["status"] == 3) {
                                                            echo "button-selected";
                                                        } ?>"
                                                        onclick="approveRecipeAlreadyValidated(<?php echo $row["id"]; ?>)">
                                                    Approva
                                                </button>
                                            </div>
                                            <div class="page-width-2 text-center margin-10 no-lateral-margin no-margin-bottom">
                                                <button class="button width-100-perc button-dark"
                                                        onclick="location.href='/easyrecipes/recipes/?id=<?php echo $row["id"]; ?>'">
                                                    Visualizza ricetta
                                                </button>
                                            </div>
                                            <div class="page-width-3 text-right margin-10 no-margin-bottom">
                                                <button id="reject-button"
                                                        class="button width-100-perc button-dark <?php if ($row["status"] == 4) {
                                                            echo "button-selected";
                                                        } ?>"
                                                        onclick="rejectRecipeAlreadyValidated(<?php echo $row["id"]; ?>)">
                                                    Rifiuta
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<h1 class='h1-section text-center text-color-light-black padding-10'>Non ci sono ricette approvate.</h1>";
                }

                $c->close();
            } else {
                echo("<div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
            }
            ?>
        </div>
    </div>
</main>
<aside class="right">
</aside>
</body>
</html>
