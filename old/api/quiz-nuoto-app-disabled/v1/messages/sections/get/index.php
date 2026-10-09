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

    if (isset($_GET["userid"]) && getGoodString($_GET["userid"]) != "") {
        $userid = getGoodString($_GET["userid"]);

        $sql = "SELECT DISTINCT `section`  FROM `messages` WHERE `userid` = '" . $userid . "' ORDER BY `section` ASC";

        //check the userid
        $sql2 = "SELECT `userid` FROM `users` WHERE `userid`='" . $userid . "'";
        if ($c->query($sql2)->num_rows != 1) {
            echo_error(401, "Parameter specified incorrect: the userid doesn't exist");
            $end = true;
        }

        if (!$end && ($r = $c->query($sql))) {
            $response["sections"] = array();
            if ($r->num_rows > 0) {
                $counter = 0;
                $end = false;
                while ($row = $r->fetch_array()) {
                    $response["sections"][$counter] = null;
                    $response["sections"][$counter]["section"] = $row["section"];
                    $counter++;
                }
            }
        } else {
            echo_error(503, "Error query");
        }
    } else {
        echo_error(400, "The userid specified is incorrect");
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