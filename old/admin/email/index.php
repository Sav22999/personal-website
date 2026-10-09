<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Gestione email";
    $current_page = "manage-email";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>

    <script>
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
            <div id="admin-manage-email-sec">
                <?php
                if (permission_yes_or_not(9)) {
                    $sql_log = "INSERT INTO logs(`id`, `user`, `date`, `ip`, `status`, `type`) VALUES(NULL, '{{*{{id}}*}}','" . htmlspecialchars(date("Y-m-d H:i:s")) . "','" . htmlspecialchars(get_user_ip()) . "','{{*{{status}}*}}', '{{*{{type}}*}}')";
                    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                    $c->set_charset("utf8");
                    if (isset($_POST["email_from"]) && isset($_POST["message"]) && isset($_POST["ticket"])) {
                        $ticket = $_POST["ticket"];

                        $message_text = htmlspecialchars(strval($_POST["message"]));
                        $message_text = str_replace("'", "&#39;", $message_text);
                        $message_text = str_replace("\"", "&#34;", $message_text);
                        $message_text = str_replace("\\", "&#92;", $message_text);
                        $message_text = str_replace("\n", "<br>", $message_text);
                        $message_text = str_replace("è", "&#232;", $message_text);
                        $message_text = str_replace("é", "&#233;", $message_text);
                        $message_text = str_replace("à", "&#224;", $message_text);
                        $message_text = str_replace("ì", "&#236;", $message_text);
                        $message_text = str_replace("ò", "&#242;", $message_text);
                        $message_text = str_replace("ù", "&#249;", $message_text);
                        $message_text = str_replace("È", "&#200;", $message_text);
                        $message_text = str_replace("É", "&#201;", $message_text);
                        $message_text = str_replace("À", "&#192;", $message_text);
                        $message_text = str_replace("Ì", "&#204;", $message_text);
                        $message_text = str_replace("Ò", "&#210;", $message_text);
                        $message_text = str_replace("Ù", "&#217;", $message_text);
                        $pattern = "/[^0-9a-zA-Z\\\$\\%\\€\\s,.;\\#\\<\\>\\-\\'\\\"\\<\\>\\&\\/\\(\\)\\[\\]\\{\\}\\\\]/";
                        $message_text = preg_replace($pattern, "", $message_text);

                        $email_address = htmlentities($_POST["email_from"], ENT_QUOTES);

                        $sql = "INSERT INTO `emails`(`id`, `from`, `to`, `text`, `status`, `date`, `ticket`, `note`) VALUES(NULL, 'info@saveriomorelli.com', '" . $email_address . "', '" . $message_text . "', '1', '" . date("Y-m-d H:i:s") . "', '" . $ticket . "', 'SaverioMorelli.com')";
                        if ($c->query($sql)) {
                            $email_schema = file_get_contents($path . "/emails/email_reply.php");
                            $message_full = str_replace("{{*{{email}}*}}", $email_address, (str_replace("{{*{{ticket}}*}}", $ticket, $email_schema)));
                            $to = $email_address;
                            $subject = 'Risposta alla richiesta - SaverioMorelli.com';

                            $headers = "MIME-Version: 1.0\r\n";
                            $headers .= "Content-type: text/html; charset=utf-8\r\n";
                            $headers .= 'From: noreply@saveriomorelli.com';
                            mail($to, $subject, $message_full, $headers);
                            echo "<div class='alert'>Risposta inviata con successo.</div>";
                            $sql_to_use = $sql_log;
                            $sql_to_use = str_replace("{{*{{status}}*}}", "0", $sql_to_use);
                            $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);
                            $sql_to_use = str_replace("{{*{{type}}*}}", "reply-to-email", $sql_to_use);
                            $c->query($sql_to_use);
                        } else {
                            echo "<div class='alert'>Errore durante l'invio della risposta.</div>";
                            $sql_to_use = $sql_log;
                            $sql_to_use = str_replace("{{*{{status}}*}}", "1", $sql_to_use);
                            $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);
                            $sql_to_use = str_replace("{{*{{type}}*}}", "reply-to-email", $sql_to_use);
                            $c->query($sql_to_use);
                        }
                    }

                    if (isset($_GET["ticket"]) && $_GET["ticket"] != "") {
                        //show one email
                        $ticket = htmlspecialchars($_GET["ticket"]);
                        $email_from_user = "";
                        $status_temp = 0;
                        $sql = "SELECT * FROM emails WHERE ticket='" . $ticket . "' ORDER BY date ASC";
                        $r = $c->query($sql);
                        while ($row = $r->fetch_assoc()) {
                            $email_from = $row["from"];
                            $status_temp = $row["status"];
                            if ($email_from != "noreply@saveriomorelli.com" && $email_from != "info@saveriomorelli.com") {
                                $email_from_user = $row["from"];
                            }
                        }
                        $solved = false;
                        $deleted = false;
                        $sql_update = "UPDATE emails SET status='2' WHERE ticket='" . $ticket . "'";

                        if ($status_temp == "4") {
                            $solved = true;
                            $sql_update = "UPDATE emails SET status='4' WHERE ticket='" . $ticket . "'";
                        } else if ($status_temp == "3") {
                            $deleted = true;
                            $sql_update = "UPDATE emails SET status='3' WHERE ticket='" . $ticket . "'";
                        }
                        $c->query($sql_update);
                        $r = $c->query($sql);
                    if ($r->num_rows > 0) {
                        ?>
                        <p class="background-secondary-color border-radius-default text-black-color padding-default text-center ticket-status <?php if ($deleted) {
                            echo "background-red-color";
                        } ?>">
                            Ticket: <?php echo $ticket; ?>
                            <br>
                            <?php if ($solved) {
                                ?>
                                <style>
                                    #button-close-conversation {
                                        display: none;
                                    }

                                    .div-form-to-reply {
                                        display: none;
                                    }
                                </style>
                                <?php
                            } else if ($deleted) {
                                ?>
                                <style>
                                    #button-close-conversation {
                                        display: none;
                                    }

                                    #button-open-conversation {
                                        display: none;
                                    }

                                    #button-delete-conversation {
                                        display: none;
                                    }

                                    .div-form-to-reply {
                                        display: none;
                                    }
                                </style>
                                <?php
                            } else {
                                ?>
                                <style>
                                    #button-open-conversation {
                                        display: none;
                                    }

                                    .div-form-to-reply {
                                        display: block;
                                    }
                                </style>
                                <?php
                            } ?>
                            <button id="button-open-conversation"
                                    onclick="mark_as_reopened('<?php echo $ticket; ?>', '<?php echo $email_from_user; ?>')">
                                Riapri conversazione
                            </button>
                            <button id="button-close-conversation"
                                    onclick="mark_as_closed('<?php echo $ticket; ?>', '<?php echo $email_from_user; ?>')">
                                Chiudi conversazione
                            </button>
                            <button id="button-delete-conversation"
                                    onclick="mark_as_deleted('<?php echo $ticket; ?>', '<?php echo $email_from_user; ?>')">
                                Elimina conversazione
                            </button>
                        </p>
                    <?php
                    $email_from_user = "";
                    $email_from = "";
                    $ticket = $_GET["ticket"];
                    while ($row = $r->fetch_assoc()) {
                    $email_from = $row["from"];
                    $reply = "div-email-not-from-admin";
                    $date = $row["date"];
                    if ($email_from == "noreply@saveriomorelli.com" || $email_from == "info@saveriomorelli.com") {
                        $reply = "div-email-from-admin";
                    } else {
                        $email_from_user = $row["from"];
                    }
                    ?>
                        <div class="admin-email-message <?php echo $reply; ?>">
                            <p>
                                <?php echo html_entity_decode($row["text"]); ?>
                            </p>
                            <div class="from-date-email">
                                <p class="from-email">
                                    <?php echo $email_from; ?>
                                </p>
                                <p class="date-email">
                                    <?php echo $date; ?>
                                </p>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                        <div class="admin-email-message div-form-to-reply">
                            <form action="./?ticket=<?php echo $ticket; ?>" method="post">
                                <input type="hidden" name="email_from" value="<?php echo $email_from_user; ?>"
                                       required readonly/>
                                <input type="hidden" name="ticket" value="<?php echo $ticket; ?>" required
                                       readonly/>
                                <textarea placeholder="Rispondi all'email" name="message" id="reply-to-email"
                                          required></textarea>
                                <div class="container clearfix">
                                    <input type="submit" class="container-left clearfix" value="Rispondi"/>
                                    <input type="button" id="refresh" class="container-right clearfix"
                                           value=""
                                           onclick="location.href=''">
                                </div>
                            </form>
                        </div>
                        <script>
                            scroll_to_the_end_of_the_page();
                        </script>
                    <?php
                    } else {
                        echo "<div class='alert'>Si è verificato un errore. Riprovare.</div>";
                    }
                    } else {
                    $status = "";
                    if (isset($_GET["filter"])) {
                        $status = $_GET["filter"];
                        if ($status != "0" && $status != "1" && $status != "2" && $status != "4") {
                            $status = "";
                        } else {
                            $status = "WHERE `status`=" . htmlspecialchars($_GET["filter"]);
                        }
                    }
                    $sql_ticket_distinct = "SELECT DISTINCT `ticket` FROM `emails`";
                    $r = $c->query($sql_ticket_distinct);
                    $tickets = array();
                    if ($r->num_rows > 0) {
                        while ($row = $r->fetch_assoc()) {
                            array_push($tickets, $row["ticket"]);
                        }
                    }
                    $sql = "SELECT * FROM `emails` " . $status . " ORDER BY `date` DESC";
                    $r = $c->query($sql);
                    ?>
                        <div id="admin-all-emails">
                            <button onclick="location.href='./?filter='" id='status-all'
                                    class='admin-all-emails-button <?php if ((isset($_GET["filter"]) && $_GET["filter"] != "0" && $_GET["filter"] != "1" && $_GET["filter"] != "2" && $_GET["filter"] != "4") || !isset($_GET["filter"])) {
                                        echo "admin-all-emails-selected";
                                    } ?>'>
                                Tutte
                            </button>
                            <button onclick="location.href='./?filter=0'" id='status-0'
                                    class='admin-all-emails-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "0") {
                                        echo "admin-all-emails-selected";
                                    } ?>'>
                                Non definite
                            </button>
                            <button onclick="location.href='./?filter=1'" id='status-1'
                                    class='admin-all-emails-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "1") {
                                        echo "admin-all-emails-selected";
                                    } ?>'>
                                Non lette
                            </button>
                            <button onclick="location.href='./?filter=2'" id='status-2'
                                    class='admin-all-emails-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "2") {
                                        echo "admin-all-emails-selected";
                                    } ?>'>
                                Lette
                            </button>
                            <button onclick="location.href='./?filter=4'" id='status-3'
                                    class='admin-all-emails-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "4") {
                                        echo "admin-all-emails-selected";
                                    } ?>'>
                                Concluse
                            </button>
                        </div>
                        <?php
                    if ($r->num_rows > 0) {
                    while ($row = $r->fetch_assoc()) {
                    if (in_array($row["ticket"], $tickets)) {
                        $email_to_show = $row["from"];
                        $date = $row["date"];
                    if ($email_to_show != "info@saveriomorelli.com" && $email_to_show != "noreply@saveriomorelli.com") {
                        $tickets = array_diff($tickets, [$row["ticket"]]);
                        ?>
                        <div class="admin-email-message <?php echo "status-" . $row["status"]; ?>">
                            <p class="background-secondary-color border-radius-default text-black-color padding-default text-center">
                                Ticket: <?php echo $row["ticket"]; ?>
                            </p>
                            <p>
                                <?php $str_length = strlen($row["text"]); ?>
                                <?php
                                if ($str_length > 500) {
                                    echo html_entity_decode(substr($row["text"], 0, 499)) . "…";
                                } else {
                                    echo html_entity_decode($row["text"]);
                                }
                                ?>
                            </p>
                            <div class="from-date-email">
                                <p class="from-email">
                                    <?php echo $email_to_show; ?>
                                </p>
                                <p class="date-email">
                                    <?php echo $date; ?>
                                </p>
                            </div>
                            <a href="./?ticket=<?php echo $row["ticket"]; ?>">
                                <button>Leggi conversazione</button>
                            </a>
                        </div>
                        <?php
                    }
                    }
                    }
                    }
                    }
                    $c->close();
                    ?>
                <?php } else {
                    echo "<div class='alert'>" . show_no_permission_message() . "</div>";
                }
                ?>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->