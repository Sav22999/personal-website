<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Ricette inserite e approvate</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="recipes-inserted-and-approved-page" class="hidden"></span>

<aside class="left">
</aside>
<main class="padding-10 border-box">
    <h1>Ricette inserite e approvate</h1>
    <div class="container clearfix margin-bottom-20">
        <div id="list" class="section clearfix">
            <?php
            global $localhost_db, $username_db, $password_db, $database_easyrecipes_api, $authorised, $path_easyrepices, $path;
            if (check_authorisation(5)) {
                $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
                $c->set_charset("utf8");

                $sql = "SELECT * FROM `recipes` WHERE `status`=3 AND `user_inserted`='" . $_SESSION["session_id"] . "' ORDER BY `date_approved` DESC";

                $r = $c->query($sql);
                if ($r->num_rows > 0) {
                    for ($i = 0; $i < $r->num_rows; $i++) {
                        $row = $r->fetch_assoc();
                        ?>
                        <div class="item div-approved-page">
                            <div class="div-cover"
                                 style="background-image: url('<?php echo $row["cover"]; ?>');"></div>
                            <h1 class="approved-recipe-title">
                                <?php echo $row["title"]; ?>
                            </h1>
                            <div>
                                <div class="clearfix">
                                    <div class="page-section-div padding-10">
                                        <div class="page-width-1 text-center no-margin">
                                            <button class="button width-100-perc button-dark no-margin"
                                                    onclick="location.href='/easyrecipes/recipes/?id=<?php echo $row["id"]; ?>'">
                                                Visualizza o modifica questa ricetta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<h1 class='h1-section text-center text-color-light-black padding-10'>Nessuna tua ricetta è stata approvata.</h1>";
                }

                $c->close();
            } else {
                echo("<script>allowed=false;</script><div class='message'>Non hai i permessi per visualizzare questa pagina.</div>");
            }
            ?>
        </div>
    </div>
</main>
<aside class="right">
</aside>
</body>
</html>
