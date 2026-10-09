<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Saverio Morelli</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>

    <?php
    global $localhost_db, $username_db, $password_db, $database_easyrecipes_api, $authorised, $path_easyrepices, $path;
    $description = "";
    $error_message = true;
    $otp_sent_message = false;
    $authorised = check_authorisation(2);
    if (!$authorised && isset($_POST["email"]) && isset($_POST["password"])) {
        $email = htmlspecialchars(strval(strtolower(trim($_POST["email"]))));
        $password = htmlspecialchars(strval(hash('sha256', $_POST["password"])));

        $email_md5 = "";
        $email_plain = $email;
        $email_sha256 = "";

        $otp_verification = false;

        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");

        $sql = "SELECT * FROM `users` WHERE `email`='" . $email . "' AND `privilege` >= 2";
        if ($c->query($sql)->num_rows == 1) {
            $sql = "SELECT * FROM `users` WHERE `email`='" . $email . "' AND `password`='" . $password . "'";
            if (($r = $c->query($sql))->num_rows == 1) {
                $row = $r->fetch_assoc();
                $date = $date = htmlspecialchars(date("Y-m-d H:i:s"));

                $minutes_to_add = 30;//30 minutes
                $time = new DateTime(date("Y-m-d H:i:s"));
                $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $date_expiring_otp = $time->format('Y-m-d H:i:s');
                $symbols = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $otp = "";
                for ($i = 0; $i < 5; $i++) {
                    $var = rand(0, 30);
                    $otp .= $symbols[$var];
                }

                $otp_to_save = hash('sha256', $otp);
                $email_to_save = hash('sha256', strtolower(trim($email)));
                $email_md5 = md5($email);
                $email_sha256 = hash('sha256', $email);

                $ip = get_user_ip();
                $sql = "INSERT INTO `otps`(`id`, `code`, `email`, `ip`, `date_creating`, `date_expiring`, `type`, `used`) VALUES(NULL,'" . $otp_to_save . "','" . $email_to_save . "','" . $ip . "','" . $date . "','" . $date_expiring_otp . "','login','false')";
                if ($c->query($sql)) {

                    $email_schema = file_get_contents($path . "/easyrecipes/emails/login.php");
                    $message_full = str_replace("{{*{{otp_code}}*}}", $otp, $email_schema);

                    $to = $email;
                    $subject = 'Codice per accedere a EasyRecipes';

                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=utf-8\r\n";
                    $headers .= 'From: noreply-easyrecipes@saveriomorelli.com';

                    mail($to, $subject, $message_full, $headers);

                    $_SESSION["username"] = $row["username"];

                    $otp_verification = true;
                    $description = "Codice OTP inviato sulla casella postale.<br>Verifica anche nella posta indesiderata (o spam).";
                    $otp_sent_message = true;
                    $error_message = false;
                } else {
                    $description = "Generazione OTP fallita.";
                }
            } else {
                $description = "Password errata.";
            }
        } else {
            $description = "Credenziali errate oppure non hai i permessi per poter accedere.";
        }
        $c->close();
    }


    if (isset($_GET["logout"]) && $authorised) {
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");
        $email = $_SESSION["session_id"];
        $date = htmlspecialchars(date("Y-m-d H:i:s"));

        $minutes_to_add = 60;//60 minutes
        $time = new DateTime(date("Y-m-d H:i:s"));
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $date_expiring = $time->format('Y-m-d H:i:s');

        $ip = get_user_ip();
        $sql = "INSERT INTO `logs`(`id`, `date`, `ip`, `value`, `username`, `email`, `date_expiring`) VALUES(NULL, '" . $date . "','" . $ip . "','logout','" . $email . "', '--','" . $date_expiring . "')";

        if ($c->query($sql)) {
            session_unset();
            session_destroy();
            $authorised = false;
        }
        $c->close();
    }

    if (!$authorised && isset($_POST["email"]) && isset($_POST["otp"]) && isset($_POST["email2"])) {
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");

        $email = htmlspecialchars(strval($_POST["email"]));
        $email_plain = htmlspecialchars(strval(strtolower(trim($_POST["email2"]))));

        $email_to_save = md5($email_plain);
        $email_sha256 = htmlspecialchars(strval($_POST["email"]));
        $email_md5 = md5($email_plain);
        $otp = htmlspecialchars(strval(hash('sha256', str_replace(" ", "", $_POST["otp"]))));

        $date = htmlspecialchars(date("Y-m-d H:i:s"));

        $minutes_to_add = 60;//60 minutes
        $time = new DateTime(date("Y-m-d H:i:s"));
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $date_expiring = $time->format('Y-m-d H:i:s');

        $sql = "SELECT * FROM `otps` WHERE `code`='" . $otp . "' AND `email`='" . $email . "' AND used='false' AND `type`='login' ORDER BY `date_expiring` DESC LIMIT 1";
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();

                if ($date < $row["date_expiring"]) {
                    $ip = get_user_ip();
                    $sql = "INSERT INTO `logs`(`id`, `date`, `ip`, `value`, `username`, `email`, `date_expiring`) VALUES(NULL, '" . $date . "','" . $ip . "','login','" . $email_sha256 . "', '" . $email_plain . "','" . $date_expiring . "')";
                    if ($c->query($sql)) {
                        $sql = "UPDATE `otps` SET `used`='true' WHERE `code`='" . $otp . "'";
                        if ($c->query($sql)) {
                            $_SESSION["session_id"] = $email;
                            $_SESSION["user_email"] = $email_to_save;
                            global $authorised;
                            $authorised = true;
                            $description = "Accesso eseguito correttamente.";
                            $error_message = false;
                        } else {
                            $description = "Errore aggiornamento accesso.";
                        }
                    } else {
                        $description = "Errore inserimento log.";
                    }
                } else {
                    $description = "Codice OTP scaduto, effettua nuovamente la procedura di accesso.";
                }
            } else {
                $description = "OTP errato, riprovare.";
                $otp_verification = true;
            }
        } else {
            $description = "Richiesta errata.";
        }
        $c->close();
    }
    ?>
</head>
<body>
<span id="home-page" class="hidden"></span>

<?php include_once($path_easyrepices . "/include/menu.php"); ?>
<aside class="left padding-10 border-box">
    <div class="section-index">
        <h1 class="h1-section">
            Hai bisogno di aiuto?
        </h1>
        <a href="/old/easyrecipes/support">
            <button class="button width-100-perc no-margin-bottom">
                Consulta la sezione Supporto
            </button>
        </a>
    </div>
</aside>
<main>
    <?php
    if ($description != "") {
        if (!$error_message && !$otp_sent_message) {
            echo("<script>showMessageBottom('" . $description . "');</script>");
        } else if (!$error_message && $otp_sent_message) {
            echo("<script>showMessageBottom('" . $description . "',-1,1);</script>");
        } else {
            echo("<script>showMessageBottom('" . $description . "',-1,2);</script>");
        }
    }
    if (!$authorised && !$otp_verification) {
        ?>
        <form action="." method="post" class="form-login">
            <input type="email" placeholder="Email" name="email" class="textbox login"/>
            <input type="password" placeholder="Password" name="password" class="textbox login"/>
            <input type="submit" value="Accedi" class="button submit login"/>
            <h3 id="forget-password" class="link" onclick="alert('Funzione non ancora disponibile')">
                Password dimenticata?
            </h3>
        </form>
        <?php
    } else if ($authorised && !$otp_verification) {
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");
        ?>
        <div id="main-index" class="clearfix">
            <div class="clearfix">
                <div class="home-page-section-div">
                    <?php
                    if (check_authorisation(5)) {
                        ?>
                        <button class="button-home-page button-home-page-start button-home-page-3"
                                onclick="location.href='/easyrecipes/recipes/new'">
                            Inserisci una nuova ricetta
                        </button>
                        <?php
                    }
                    if (check_authorisation(7)) {
                        ?>
                        <button class="button-home-page button-home-page-3"
                                onclick="location.href='/easyrecipes/manage/ingredient'">
                            Inserisci un nuovo ingrediente
                        </button>

                        <button class="button-home-page button-home-page-end button-home-page-3"
                                onclick="location.href='/easyrecipes/manage/country'">
                            Inserisci un nuovo Paese
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="main-index-admin" class="clearfix">
            <div class="clearfix">
                <div class="home-page-section-div">
                    <?php
                    if (check_authorisation(9)) {
                        ?>
                        <button class="button-home-page button-home-page-start button-home-page-2 no-margin-right"
                                onclick="location.href='/easyrecipes/manage/approved/'">
                            Gestisci ricette convalidate (approvate e rifiutate)
                        </button>
                        <button class="button-home-page button-home-page-end button-home-page-2"
                                onclick="location.href='/easyrecipes/manage/to-approve/'">
                            Gestisci ricette da approvare
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="recipes-saved-as-draft-index">
            <?php
            $no_recipes = false;
            $user = $_SESSION["session_id"];
            $sql = "SELECT * FROM `recipes` WHERE `user_inserted`='" . $user . "' AND `status`=1 ORDER BY `date_inserted` DESC";

            if ($r = $c->query($sql)) {
                if ($r->num_rows > 0) {
                    $n_rows = 0;
                    if ($r->num_rows == 1) {
                        $n_rows = 1;
                    } else {
                        $n_rows = 2;
                    }
                    ?>
                    <div class="clearfix">
                        <div class="home-page-section-div">
                            <?php
                            for ($index = 0; $index < $n_rows; $index++) {
                                $row = $r->fetch_assoc();
                                ?>
                                <div class="page-section-div margin-10">
                                    <div class="div-home-page">
                                        <div class="div-cover"
                                             style="background-image: url('<?php echo $row["cover"]; ?>');"></div>
                                        <h1 class="home-recipe-title">
                                            <?php echo $row["title"]; ?>
                                        </h1>
                                        <div>
                                            <div class="clearfix">
                                                <div class="page-section-div padding-10">
                                                    <div class="page-width-1 text-center no-margin">
                                                        <button class="button width-100-perc button-dark no-margin"
                                                                onclick="location.href='/easyrecipes/recipes/edit/?id=<?php echo $row["id"]; ?>'">
                                                            Modifica questa ricetta
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <div class="home-page-section-div">
                            <button class="button-home-page button-home-page-start button-home-page-end button-home-page-1 button-home-page-no-min-height"
                                    onclick="location.href='/easyrecipes/recipes/drafted/'">
                                Vedi tutte le bozze
                            </button>
                        </div>
                    </div>
                    <?php
                } else {
                    $no_recipes = true;
                }
            } else {
                $no_recipes = true;
            }
            if ($no_recipes) {
                echo '<h1 class="h1-section text-center text-color-light-black padding-10">Nessuna ricetta salvata come bozza
                    trovata</h1>';
            }
            ?>
        </div>

        <div id="recipes-inserted-index" class="margin-bottom-10">
            <?php
            $no_recipes = false;
            $user = $_SESSION["session_id"];
            $sql = "SELECT * FROM `recipes` WHERE `user_inserted`='" . $user . "' AND `status`=2 ORDER BY `date_inserted` DESC";

            if ($r = $c->query($sql)) {
                if ($r->num_rows > 0) {
                    $n_rows = 0;
                    if ($r->num_rows == 1) {
                        $n_rows = 1;
                    } else {
                        $n_rows = 2;
                    }
                    ?>
                    <div class="clearfix">
                        <div class="home-page-section-div">
                            <?php
                            for ($index = 0; $index < $n_rows; $index++) {
                                $row = $r->fetch_assoc();
                                ?>
                                <div class="page-section-div margin-10">
                                    <div class="div-home-page">
                                        <div class="div-cover"
                                             style="background-image: url('<?php echo $row["cover"]; ?>');"></div>
                                        <h1 class="home-recipe-title">
                                            <?php echo $row["title"]; ?>
                                        </h1>
                                        <div>
                                            <div class="clearfix">
                                                <div class="page-section-div padding-10">
                                                    <div class="page-width-1 text-center no-margin">
                                                        <button class="button width-100-perc button-dark no-margin"
                                                                onclick="location.href='/easyrecipes/recipes/?id=<?php echo $row["id"]; ?>'">
                                                            Visualizza questa ricetta
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <div class="home-page-section-div">
                            <button class="button-home-page button-home-page-start button-home-page-end button-home-page-1 button-home-page-no-min-height"
                                    onclick="location.href='/easyrecipes/recipes/inserted/'">
                                Vedi tutte le ricette inserite e non ancora approvate
                            </button>
                        </div>
                    </div>
                    <?php
                } else {
                    $no_recipes = true;
                }
            } else {
                $no_recipes = true;
            }
            if ($no_recipes) {
                echo '<h1 class="h1-section text-center text-color-light-black padding-10">Nessuna ricetta è stata inserita (o sono state tutte approvate)</h1>';
            }
            ?>
        </div>

        <div id="recipes-approved-index" class="margin-bottom-10">
            <?php
            $no_recipes = false;
            $user = $_SESSION["session_id"];
            $sql = "SELECT * FROM `recipes` WHERE `user_inserted`='" . $user . "' AND `status`=3 ORDER BY `date_approved` DESC";

            if ($r = $c->query($sql)) {
                if ($r->num_rows > 0) {
                    $n_rows = 0;
                    if ($r->num_rows == 1) {
                        $n_rows = 1;
                    } else {
                        $n_rows = 2;
                    }
                    ?>
                    <div class="clearfix">
                        <div class="home-page-section-div">
                            <?php
                            for ($index = 0; $index < $n_rows; $index++) {
                                $row = $r->fetch_assoc();
                                ?>
                                <div class="page-section-div margin-10">
                                    <div class="div-home-page">
                                        <div class="div-cover"
                                             style="background-image: url('<?php echo $row["cover"]; ?>');"></div>
                                        <h1 class="home-recipe-title">
                                            <?php echo $row["title"]; ?>
                                        </h1>
                                        <div>
                                            <div class="clearfix">
                                                <div class="page-section-div padding-10">
                                                    <div class="page-width-1 text-center no-margin">
                                                        <button class="button width-100-perc button-dark no-margin"
                                                                onclick="location.href='/easyrecipes/recipes/?id=<?php echo $row["id"]; ?>'">
                                                            Visualizza questa ricetta
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <div class="home-page-section-div">
                            <button class="button-home-page button-home-page-start button-home-page-end button-home-page-1 button-home-page-no-min-height"
                                    onclick="location.href='/easyrecipes/recipes/approved/'">
                                Vedi tutte le ricette inserite e pubblicate
                            </button>
                        </div>
                    </div>
                    <?php
                } else {
                    $no_recipes = true;
                }
            } else {
                $no_recipes = true;
            }
            if ($no_recipes) {
                echo '<h1 class="h1-section text-center text-color-light-black padding-10">Nessuna ricetta inserita è stata approvata</h1>';
            }
            ?>
        </div>
        <?php
        $c->close();
    } else if (!$authorised && $otp_verification) {
        ?>
        <form action="." method="post" class="form-login">
            <input type="hidden" value="<?php echo $email_sha256; ?>" name="email" class="textbox login" hidden
                   readonly/>
            <input type="hidden" value="<?php echo $email_plain; ?>" name="email2" class="textbox login" hidden
                   readonly/>
            <input type="text" placeholder="Codice OTP" name="otp" class="textbox login" autocomplete="off" autofocus/>
            <input type="submit" value="Avanti" class="button submit login"/>
        </form>
        <?php
    }
    ?>
</main>
<aside class="right">
</aside>
</body>
</html>
