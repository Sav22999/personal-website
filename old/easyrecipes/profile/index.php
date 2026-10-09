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
<main class="padding-10 border-box">
    <?php
    if (check_authorisation(2)) {
        ?>
        <div id="profile-image-dedicated-page"
             style="background-image:url('https://www.gravatar.com/avatar/<?php echo $_SESSION["user_email"]; ?>?s=1000&r=g')">
            <div id="background-profile-image-dedicated-page" onclick="location.href='https://it.gravatar.com/'">
                Per modificare l'immagine del profilo andare su Gravatar utilizzando lo stesso indirizzo email
                che usi per accedere al tuo account Easy Recipes.
            </div>
        </div>
        <?php
        $name = "";
        $surname = "";
        $email = "";
        $username = "";

        global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");
        $sql = "SELECT * FROM `users` WHERE `username`='" . $_SESSION["username"] . "'";
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();
                $name = $row["name"];
                $surname = $row["surname"];
                $username = $row["username"];
                $email = $row["email"];
            }
        }
        $c->close();
        ?>
        <div class="main-text">
            <h1 class="padding-10">Il mio profilo</h1>
            <div>
                <div class="row-div">
                    <div class="position-relative">
                        <div class="col-div width-20-perc">
                            Nome:
                        </div>
                        <div class="col-div width-70-perc">
                            <input type="text" placeholder="Nome" class="textbox no-box-shadow"
                                   value="<?php echo $name; ?>" readonly disabled autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row-div">
                    <div class="position-relative">
                        <div class="col-div width-20-perc">
                            Cognome:
                        </div>
                        <div class="col-div width-70-perc">
                            <input type="text" placeholder="Cognome" class="textbox no-box-shadow"
                                   value="<?php echo $surname; ?>" readonly disabled autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row-div">
                    <div class="position-relative">
                        <div class="col-div width-20-perc">
                            Email:
                        </div>
                        <div class="col-div width-70-perc">
                            <input type="email" placeholder="Email" class="textbox no-box-shadow"
                                   value="<?php echo $email; ?>" readonly disabled autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row-div">
                    <div class="position-relative">
                        <div class="col-div width-20-perc">
                            Username:
                        </div>
                        <div class="col-div width-70-perc">
                            <input type="text" placeholder="Username" class="textbox no-box-shadow"
                                   value="<?php echo $username; ?>" readonly disabled autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row-div">
                    <div class="position-relative">
                        <div class="col-div width-20-perc">
                            Password:
                        </div>
                        <div class="col-div width-70-perc">
                            <button class="button">Modifica password</button>
                        </div>
                    </div>
                </div>
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
