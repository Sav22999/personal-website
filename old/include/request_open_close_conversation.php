<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");

global $localhost_db, $username_db, $password_db, $database_db, $path;

if (isset($_POST["status"]) && isset($_POST["ticket"]) && isset($_POST["to_email"]) && ($_POST["status"] == 2 || $_POST["status"] == 4) || $_POST["status"] == 3) {
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
    $c->set_charset("utf8");
    $sql_log = "INSERT INTO logs(`id`, `user`, `date`, `ip`, `status`, `type`) VALUES(NULL, '{{*{{id}}*}}','" . htmlspecialchars(date("Y-m-d H:i:s")) . "','" . htmlspecialchars(get_user_ip()) . "','{{*{{status}}*}}', '{{*{{type}}*}}')";
    $sql = "SELECT * FROM `emails` WHERE `from`='noreply@saveriomorelli.com' AND `ticket`='" . $_POST["ticket"] . "' AND `status`='3'";
    $deleted = false;
    if ($r = $c->query($sql)) {
        if ($r->num_rows > 0) {
            $deleted = true;
        }
    }

    if (!$deleted) {
        $sql = "UPDATE `emails` SET `status` = '" . htmlspecialchars($_POST["status"]) . "' WHERE `ticket`='" . htmlspecialchars($_POST["ticket"]) . "'";
        if ($c->query($sql)) {
            if (variables_permission_yes_or_not(8)) {
                $sql_to_use = $sql_log;
                $sql_to_use = str_replace("{{*{{status}}*}}", "0", $sql_to_use);
                $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);
                if ($_POST["status"] == 2) {
                    $sql_to_use = str_replace("{{*{{type}}*}}", "reopen-conversation", $sql_to_use);
                    reopen_ticket($_POST["ticket"], $_POST["to_email"]);
                } else if ($_POST["status"] == 4) {
                    $sql_to_use = str_replace("{{*{{type}}*}}", "close-conversation", $sql_to_use);
                    close_ticket($_POST["ticket"], $_POST["to_email"]);
                } else if ($_POST["status"] == 3) {
                    $sql_to_use = str_replace("{{*{{type}}*}}", "delete-conversation", $sql_to_use);
                    delete_ticket($_POST["ticket"], $_POST["to_email"]);
                }
                $c->query($sql_to_use);
            } else {
                if ($_POST["status"] == 2) {
                    reopen_ticket($_POST["ticket"], $_POST["to_email"]);
                } else if ($_POST["status"] == 4) {
                    close_ticket($_POST["ticket"], $_POST["to_email"]);
                }
            }
            echo "true";
        } else {
            if (variables_permission_yes_or_not(8)) {
                $sql_to_use = $sql_log;
                $sql_to_use = str_replace("{{*{{status}}*}}", "1", $sql_to_use);
                $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);
                if ($_POST["status"] == 2) $sql_to_use = str_replace("{{*{{type}}*}}", "reopen-conversation", $sql_to_use);
                else if ($_POST["status"] == 4) $sql_to_use = str_replace("{{*{{type}}*}}", "close-conversation", $sql_to_use);
                else if ($_POST["status"] == 3) $sql_to_use = str_replace("{{*{{type}}*}}", "delete-conversation", $sql_to_use);
                $c->query($sql_to_use);
            }
            echo "false";
        }
    } else {
        echo "deleted";
    }
    $c->close();
} else {
    echo "false";
}

function reopen_ticket($ticket, $email_from)
{
    global $localhost_db, $username_db, $password_db, $database_db, $path;
    if (variables_permission_yes_or_not(8)) {
        $message = "(Auto) Conversazione riaperta da un moderatore.";
    } else {
        $message = "USER(Auto) Conversazione riaperta dall'utente.";
    }
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
    $c->set_charset("utf8");
    $sql = "INSERT INTO `emails`(`id`, `from`, `to`, `text`, `status`, `date`, `ticket`, `note`) VALUES(NULL, 'noreply@saveriomorelli.com', '" . htmlentities($email_from, ENT_QUOTES) . "','" . htmlentities($message, ENT_QUOTES) . "', '2', '" . date("Y-m-d H:i:s") . "', '" . $ticket . "', 'SaverioMorelli.com')";
    if ($c->query($sql)) {
        if (variables_permission_yes_or_not(8)) {
            $email_schema = file_get_contents($path . "/emails/email_request_reopened.php");
            $message_full = str_replace("{{*{{email}}*}}", $email_from, (str_replace("{{*{{ticket}}*}}", $ticket, $email_schema)));
            $to = $email_from;
            $subject = 'Conversazione riaperta - SaverioMorelli.com';

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= 'From: noreply@saveriomorelli.com';
            mail($to, $subject, $message_full, $headers);
        }
    }
    $c->close();
}

function close_ticket($ticket, $email_from)
{
    global $localhost_db, $username_db, $password_db, $database_db, $path;
    if (variables_permission_yes_or_not(8)) {
        $message = "(Auto) Conversazione chiusa da un moderatore.";
    } else {
        $message = "USER(Auto) Conversazione chiusa dall'utente.";
    }
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
    $c->set_charset("utf8");
    $sql = "INSERT INTO `emails`(`id`, `from`, `to`, `text`, `status`, `date`, `ticket`, `note`) VALUES(NULL, 'noreply@saveriomorelli.com', '" . htmlentities($email_from, ENT_QUOTES) . "','" . htmlentities($message, ENT_QUOTES) . "', '4', '" . date("Y-m-d H:i:s") . "', '" . $ticket . "', 'SaverioMorelli.com')";
    if ($c->query($sql)) {
        if (variables_permission_yes_or_not(8)) {
            $email_schema = file_get_contents($path . "/emails/email_request_closed.php");
            $message_full = str_replace("{{*{{email}}*}}", $email_from, (str_replace("{{*{{ticket}}*}}", $ticket, $email_schema)));
            $to = $email_from;
            $subject = 'Conversazione conclusa - SaverioMorelli.com';

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= 'From: noreply@saveriomorelli.com';
            mail($to, $subject, $message_full, $headers);
        }
    }
    $c->close();
}

function delete_ticket($ticket, $email_from)
{
    if (variables_permission_yes_or_not(8)) {
        global $localhost_db, $username_db, $password_db, $database_db, $path;
        $message = "(Auto) Conversazione eliminata da un moderatore.";
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
        $c->set_charset("utf8");
        $sql = "INSERT INTO `emails`(`id`, `from`, `to`, `text`, `status`, `date`, `ticket`, `note`) VALUES(NULL, 'noreply@saveriomorelli.com', '" . htmlentities($email_from, ENT_QUOTES) . "','" . htmlentities($message, ENT_QUOTES) . "', '3', '" . date("Y-m-d H:i:s") . "', '" . $ticket . "', 'SaverioMorelli.com')";
        if ($c->query($sql)) {
            $email_schema = file_get_contents($path . "/emails/email_request_deleted.php");
            $message_full = str_replace("{{*{{email}}*}}", $email_from, (str_replace("{{*{{ticket}}*}}", $ticket, $email_schema)));
            $to = $email_from;
            $subject = 'Conversazione eliminata - SaverioMorelli.com';

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= 'From: noreply@saveriomorelli.com';
            mail($to, $subject, $message_full, $headers);
        }
        $c->close();
    }
}

?>