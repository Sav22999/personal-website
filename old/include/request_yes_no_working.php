<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");

global $localhost_db, $username_db, $password_db, $database_db;

if (variables_permission_yes_or_not(9) && isset($_POST["value"])) {
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
    $c->set_charset("utf8");
    $sql_log = "INSERT INTO logs(`id`, `user`, `date`, `ip`, `status`, `type`) VALUES(NULL, '{{*{{id}}*}}','" . htmlspecialchars(date("Y-m-d H:i:s")) . "','" . htmlspecialchars(get_user_ip()) . "','{{*{{status}}*}}', '{{*{{type}}*}}')";
    $sql = "INSERT INTO maintenances(id, user, status, date) VALUES(NULL, '" . htmlspecialchars($_SESSION["user_id"]) . "', " . htmlspecialchars($_POST["value"]) . ",'" . date("Y-m-d H:i:s") . "')";
    if ($c->query($sql)) {
        if (variables_permission_yes_or_not(8)) {
            $sql_to_use = $sql_log;
            $sql_to_use = str_replace("{{*{{status}}*}}", "0", $sql_to_use);
            $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);
            if ($_POST["value"] == 0) $sql_to_use = str_replace("{{*{{type}}*}}", "turn-off-maintenance", $sql_to_use);
            else if ($_POST["value"] == 1) $sql_to_use = str_replace("{{*{{type}}*}}", "turn-on-maintenance", $sql_to_use);
            $c->query($sql_to_use);
        }
        echo "true";
    } else {
        if (variables_permission_yes_or_not(8)) {
            $sql_to_use = $sql_log;
            $sql_to_use = str_replace("{{*{{status}}*}}", "1", $sql_to_use);
            $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);
            if ($_POST["value"] == 0) $sql_to_use = str_replace("{{*{{type}}*}}", "turn-off-maintenance", $sql_to_use);
            else if ($_POST["value"] == 1) $sql_to_use = str_replace("{{*{{type}}*}}", "turn-on-maintenance", $sql_to_use);
            $c->query($sql_to_use);
        }
        echo "false";
    }
    $c->close();
}
?>