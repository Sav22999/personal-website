<?php
session_start();
session_regenerate_id();
$path_easyrepices = $_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/";

include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;

$your_privileges = 0;
function check_authorisation($privileges)
{
    global $your_privileges;
    global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
    if (isset($_SESSION["session_id"])) {
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");
        $sql = "SELECT `email`,`value` FROM `logs` WHERE `username`='" . $_SESSION["session_id"] . "' ORDER BY `date` DESC LIMIT 1";
        if ($r = $c->query($sql)) {
            $row = $r->fetch_assoc();
            if ($row["value"] == "login") {
                $saved_email = $row["email"];

                $sql = "SELECT `privilege` FROM `users` WHERE `email`='" . $saved_email . "'";
                if ($r = $c->query($sql)) {
                    if ($r->num_rows == 1) {
                        $row = $r->fetch_assoc();
                        $your_privileges = $row["privilege"];
                        if ($your_privileges >= $privileges) {
                            return true;
                        }
                    }
                }
            }
        }
        $c->close();
    }
    return false;
}

$authorised = check_authorisation(5);
?>