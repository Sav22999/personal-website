<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_quiz_nuoto_api, $path;
header("Content-Type:application/json");

$response = null;

$end = false;
$message_printed = false;

if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_quiz_nuoto_api)) {
    $c->set_charset("utf8");
    //GET data

    if (isset($_GET["section"]) && getGoodString($_GET["section"]) != "") {
        $section = getGoodString($_GET["section"]);
        $username = "";

        $sql2 = "SELECT `datetime` AS `last-update` FROM `messages` ORDER BY `datetime` DESC LIMIT 1";
        if ($r = $c->query($sql2)) {
            if ($r->num_rows > 0 && $r->num_rows == 1) {
                $end = false;
                $row = $r->fetch_array();
                $response["last-update"]["datetime"] = date("Y-m-d H:i:s", strtotime($row["last-update"]));
                $response["last-update"]["date"] = date("Y-m-d", strtotime($row["last-update"]));
                $response["last-update"]["time"] = date("H:i:s", strtotime($row["last-update"]));
            } else {
                echo_error(502, "Value returned incorrect");
            }
        } else {
            echo_error(501, "Error query (1)");
        }

        $sql = "SELECT `messages`.`id` AS `id`, `reply_to`, `messages`.`userid` AS `userid`, `section`, `text`, `messages`.`datetime`, `username`, `email` FROM `messages` INNER JOIN `users` ON `users`.`userid` = `messages`.`userid` WHERE `section` = '" . $section . "' ORDER BY `datetime` ASC";

        if (!$end && ($r = $c->query($sql))) {
            if ($r->num_rows > 0) {
                $end = false;
                $counter = 0;
                while ($row = $r->fetch_array()) {
                    $response["messages"][$counter] = null;

                    $response["messages"][$counter]["id"] = $row["id"];
                    $response["messages"][$counter]["reply_to"] = $row["reply_to"];
                    $response["messages"][$counter]["username"] = $row["username"];
                    $response["messages"][$counter]["email"] = $row["email"];
                    $response["messages"][$counter]["section"] = $row["section"];
                    $response["messages"][$counter]["text"] = $row["text"];
                    $response["messages"][$counter]["datetime"] = $row["datetime"];

                    $counter++;
                }
            }
        } else {
            echo_error(503, "Error query");
        }
    } else {
        echo_error(402, "Parameters are not enough or are wrong");
    }
} else {
    echo_error(500, "Connection to the database server failed");
}

if (!$end && !$message_printed) {
    $message_printed = true;
    echo json_encode($response);
}

function echo_error($error, $description)
{
    global $end, $message_printed;
    $end = true;
    if (!$message_printed) {
        $error_response = null;
        $error_response["code"] = $error;
        $error_response["description"] = $description;
        if ($error == 0) {
            $error_response = null;
        }
        $message_printed = true;
        echo json_encode($error_response);
    }
}

?>