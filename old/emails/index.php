<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $temp_title = "";
    if (isset($_GET["ticket"])) {
        $temp_title = "Ticket: " . $_GET["ticket"];
    }
    $title = $temp_title;
    $current_page = "";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>

    <script>
        function start_timeout_refresh(ticket, email) {
            var time = new Date().getTime();
            $(document.body).bind("mousemove keypress", function (e) {
                time = new Date().getTime();
            });

            function refresh() {
                if (new Date().getTime() - time >= 10000)
                    window.location.href = "/emails/?ticket=" + ticket + "&email=" + email;
                else
                    setTimeout(refresh, 100000);
            }

            setTimeout(refresh, 100000);
        }

        function scroll_to_the_end_of_the_page() {
            $('html, body').animate({
                scrollTop: $('.div-form-to-reply').offset().top
            }, 'slow');
        }
    </script>
</head>
<body>
<main class="clearfix">
    <aside>
        <?php show_aside(); ?>
    </aside>

    <section>
        <article>
            <div id="user-manage-email-sec">
                <?php
                $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                $c->set_charset("utf8");
                if (isset($_POST["email_from"]) && isset($_POST["message"]) && isset($_POST["ticket"]) && isset($_GET["ticket"]) && isset($_GET["email"]) && $_GET["ticket"] != "" && $_GET["email"] != "") {
                    $ticket = $_POST["ticket"];

                    $message = htmlspecialchars($_POST["message"]);
                    $message = str_replace("'", "&#39;", $message);
                    $message = str_replace("\"", "&#34;", $message);
                    $message = str_replace("\\", "&#92;", $message);
                    $message = str_replace("\n", "<br>", $message);

                    $email_from = htmlentities($_POST["email_from"], ENT_QUOTES);

                    if ($email_from != "info@saveriomorelli.com" && $email_from != "noreply@saveriomorelli.com") {
                        $sql = "INSERT INTO `emails`(`id`, `from`, `to`, `text`, `status`, `date`, `ticket`, `note`) VALUES(NULL, '" . $email_from . "','info@saveriomorelli.com', '" . htmlentities($message, ENT_QUOTES) . "', '1', '" . date("Y-m-d H:i:s") . "', '" . $ticket . "', 'SaverioMorelli.com')";
                        if ($c->query($sql)) {
                            $email_schema = file_get_contents($path . "/emails/email_reply.php");

                            $message_to_admin = "-- Dettagli email --<hr>Email: " . $email_from . "<br>Link richiesta: <a href='https://saveriomorelli.com/admin/email/?ticket=" . $ticket . "'>https://saveriomorelli.com/admin/email/?ticket=" . $ticket . "</a><br>Messaggio:<br>" . $message;
                            $subject = 'E-mail da SaverioMorelli';
                            $headers = "MIME-Version: 1.0\r\n";
                            $headers .= "Content-type: text/html; charset=utf-8\r\n";
                            $headers .= 'From: ' . $email_from;
                            mail("info@saveriomorelli.com", $subject, $message_to_admin, $headers);
                            echo "<div class='alert'>Risposta inviata con successo.</div>";
                        } else {
                            echo "<div class='alert'>Errore durante l'invio della risposta.</div>";
                        }
                    } else {
                        echo "<div class='alert'>Errore durante l'invio della risposta.</div>";
                    }
                }

                $message_error = "Spiacente. Non è stata trovata questa richiesta.";
                if (isset($_GET["ticket"]) && isset($_GET["email"]) && $_GET["ticket"] != "" && $_GET["email"] != "") {
                    $sql = "SELECT * FROM `emails` WHERE `ticket`='" . $_GET["ticket"] . "' AND `from`='" . $_GET["email"] . "' AND NOT (`from`='info@saveriomorelli.com' OR `from`='noreply@saveriomorelli.com') ORDER BY date ASC";
                    $r = $c->query($sql);
                    $solved = false;
                    $deleted = false;
                    //$sql_update = "UPDATE emails SET status='2' WHERE ticket='" . $_GET["ticket"] . "'";
                    $status_temp = $r->fetch_assoc()["status"];

                    $r = $c->query($sql);
                    $no_result = true;
                    if ($r->num_rows > 0) $no_result = false;

                    $sql = "SELECT * FROM `emails` WHERE `ticket`='" . $_GET["ticket"] . "' AND (`from`='" . $_GET["email"] . "' OR `from`='info@saveriomorelli.com' OR `from`='noreply@saveriomorelli.com') ORDER BY date ASC";
                    $r = $c->query($sql);

                    if (!$no_result && $r->num_rows > 0) {
                        //if there are messages associated to that email, so it shows every message
                        if ($status_temp == "4") {
                            $solved = true;
                            $sql_update = "UPDATE emails SET status='4' WHERE ticket='" . $_GET["ticket"] . "'";
                        } else if ($status_temp == "3") {
                            $deleted = true;
                            $sql_update = "UPDATE emails SET status='3' WHERE ticket='" . $_GET["ticket"] . "'";
                        }
                        $c->query($sql_update);

                        ?>
                        <p class="background-secondary-color border-radius-default text-black-color padding-default text-center <?php if ($deleted) {
                            echo "background-red-color";
                        } ?>">
                            <span class='no-margin no-padding text-status-conversation'>
                            <?php
                            if ($solved) {
                                echo "Conversazione conclusa";
                            } else if ($deleted) {
                                echo "Conversazione eliminata";
                            } else {
                                echo "Conversazione non conclusa";
                            }
                            ?>
                            </span>
                        </p>
                    <?php
                    $email_from = $_GET["email"];
                    $ticket = $_GET["ticket"];
                    ?>
                        <script>
                            start_timeout_refresh("<?php echo $ticket; ?>", "<?php echo $email_from; ?>");
                        </script>
                    <?php
                    $admin = false;
                    while ($row = $r->fetch_assoc()) {
                    $email_from = $row["from"];
                    $reply = "div-email-not-from-admin";
                    $date = $row["date"];
                    $message_to_view = html_entity_decode($row["text"]);
                    $admin = false;
                    $to_show = true;
                    if ($email_from == "noreply@saveriomorelli.com" || $email_from == "info@saveriomorelli.com") {
                        $reply = "div-email-from-admin";
                        $admin = true;
                        $admin_to_show = true;
                        if ($email_from == "noreply@saveriomorelli.com" && substr($row["text"], 0, 6) == "(Auto)") {
                            $to_show = false;
                        } else if ($email_from == "noreply@saveriomorelli.com" && substr($row["text"], 0, 10) == "USER(Auto)") {
                            $message_to_view = substr($message_to_view, 11);
                            $admin_to_show = false;
                        }
                    }
                    if ($to_show) {
                    ?>
                        <div class="user-email-message <?php echo $reply; ?>">
                            <p>
                                <b>
                                    <?php if ($admin && $admin_to_show) { ?>
                                        Admin:
                                    <?php } else if (!$admin) { ?>
                                        Tu:
                                    <?php } ?>
                                </b>
                            </p>
                            <p>
                                <?php echo $message_to_view; ?>
                            </p>
                            <div class="from-date-email">
                                <p class="date-email">
                                    <?php echo $date; ?>
                                </p>
                            </div>
                        </div>
                    <?php
                    }
                    }
                    ?>

                    <?php
                    $email_from = $_GET["email"];
                    if (!$admin && !$solved && !$deleted) { ?>
                        <div class="user-email-message div-email-from-admin">
                            <p>
                                <b>
                                    Admin:
                                </b>
                            </p>
                            <p>
                                Ho ricevuto la tua richiesta e provvederò a rispondere non appena possibile.
                                <br>
                                Puoi aggiungere altri dettagli alla richiesta compilando il modulo di risposta che
                                segue.
                            </p>
                        </div>
                    <?php } else if ($admin && !$solved & !$deleted) { ?>
                        <div class="user-email-message div-email-from-admin reply-to-hide">
                            <p>
                                <b>
                                    Admin:
                                </b>
                            </p>
                            <p>
                                Se questa risposta ha risolto la tua richiesta, per favore premi sul seguente pulsante:
                            <center>
                                <button id="button-close-conversation"
                                        onclick="mark_as_closed('<?php echo $ticket; ?>', '<?php echo $email_from; ?>')">
                                    Richiesta risolta
                                </button>
                            </center>
                            </p>
                        </div>
                    <?php } else if ($deleted) { ?>
                        <div class="user-email-message div-email-from-admin reply-to-hide">
                            <p>
                                <b>
                                    Admin:
                                </b>
                            </p>
                            <p>
                                Questo ticket è stato chiuso ed eliminato da un moderatore.
                                <br>
                                Potrai comunque continuare a leggere la conversazione, ma non potrai riaprirla.
                            </p>
                        </div>
                    <?php } else if ($solved) { ?>
                        <script>
                            $(".div-form-to-reply").css("display", "none");
                        </script>
                        <div class="user-email-message div-email-from-admin reply-to-hide">
                            <p>
                                <b>
                                    Admin:
                                </b>
                            </p>
                            <p>
                                Questa richiesta è stata segnata come <i>Risolta</i>.
                                <br>
                                Se lo desideri, puoi aprire un'ulteriore richiesta compilando il modulo in <i><a
                                            href="https://www.saveriomorelli.com/contact-me/">Contatti</a></i>
                                oppure puoi riaprire questo ticket premendo il seguente pulsante:
                                <br>
                            <center>
                                <button id="button-open-conversation"
                                        onclick="mark_as_reopened('<?php echo $ticket; ?>', '<?php echo $email_from; ?>')">
                                    Riapri conversazione
                                </button>
                            </center>
                            </p>
                        </div>
                    <?php } ?>
                    <?php if (!$deleted) { ?>
                        <div class="user-email-message div-form-to-reply">
                            <form action="./?ticket=<?php echo $ticket; ?>&email=<?php echo $email_from; ?>"
                                  method="post">
                                <input type="hidden" name="email_from" value="<?php echo $email_from; ?>"
                                       required readonly/>
                                <input type="hidden" name="ticket" value="<?php echo $ticket; ?>" required
                                       readonly/>
                                <textarea placeholder="Rispondi all'email" name="message" id="reply-to-email"
                                          required></textarea>
                                <div class="container clearfix">
                                    <input type="submit" class="container-left clearfix" value="Rispondi"/>
                                    <input type="button" id="refresh" class="container-right clearfix" value=""
                                           onclick="location.href=''">
                                </div>
                            </form>
                        </div>
                    <?php } ?>
                    <?php if ($solved) { ?>
                        <script>
                            $(".div-form-to-reply").css("display", "none");
                        </script>
                    <?php } ?>
                        <script>
                            scroll_to_the_end_of_the_page();
                        </script>
                        <?php
                    } else {
                        echo "<div class='alert'>" . $message_error . "</div>";
                    }
                } else {
                    echo "<div class='alert'>" . $message_error . "</div>";
                }
                $c->close();
                ?>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->