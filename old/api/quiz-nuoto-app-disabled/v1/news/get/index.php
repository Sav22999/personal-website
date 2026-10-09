<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_quiz_nuoto_api, $path;
header("Content-Type:application/json");

$response = null;
$response["last-update"]["datetime"] = date("Y-m-d H:i:s");
$response["last-update"]["date"] = date("Y-m-d");
$response["last-update"]["time"] = date("H:i:s");

$end = false;
$message_printed = false;

if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_quiz_nuoto_api)) {
    $c->set_charset("utf8");
    //GET data

    $sql = "SELECT `modified` AS `last-update` FROM `news` ORDER BY `modified` DESC LIMIT 1";
    if ($r = $c->query($sql)) {
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
    $limit = 100;
    if (isset($_GET["limit"]) && $_GET["limit"] != "" && is_int((int)$_GET["limit"]) && $_GET["limit"] > 0) $limit = $_GET["limit"];
    $sql = "SELECT `id`, `type`, `datetime`, `title`, `image`, `link`, `text`  FROM `news` ORDER BY `datetime` DESC, `id` DESC LIMIT " . $limit;
    if (isset($_GET["type"]) && $_GET["type"] != "") {
        $sql2 = "SELECT DISTINCT `type` FROM `news` ORDER BY `type` ASC";
        $types_list = array();
        if ($r = $c->query($sql2)) {
            while ($row = $r->fetch_array()) {
                array_push($types_list, $row["type"]);
            }
        }
        $type = $_GET["type"];
        if (!in_array($type, $types_list)) {
            echo_error(101, "Parameter specified incorrect: the type doesn't exist");
        } else {
            $end = false;
        }

        $sql = "SELECT `id`, `type`, `datetime`, `title`, `image`, `link`, `text` FROM `news` WHERE `type` = '" . $type . "' ORDER BY `datetime` DESC, `id` DESC LIMIT " . $limit;
    }

    $response["news"] = null;

    if (!$end && ($r = $c->query($sql))) {
        if ($r->num_rows > 0) {
            $end = false;
            $counter = 0;
            while ($row = $r->fetch_array()) {
                $response["news"][$counter] = null;

                $response["news"][$counter]["id"] = $row["id"];
                $response["news"][$counter]["type"] = $row["type"];
                $response["news"][$counter]["date"] = $row["datetime"];
                $response["news"][$counter]["title"] = $row["title"];
                $response["news"][$counter]["image"] = $row["image"];
                $response["news"][$counter]["link"] = $row["link"];
                $response["news"][$counter]["text"] = $row["text"];

                $counter++;
            }
        } else {
            echo_error(504, "Values returned not enough");
        }
    } else {
        echo_error(503, "Error query(2)");
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