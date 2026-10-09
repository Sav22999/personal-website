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

        $sql = "SELECT * FROM `users` WHERE `userid` = '" . $userid . "'";
        if ($c->query($sql)->num_rows != 1) {
            echo_error(401, "Parameter specified incorrect: the userid doesn't exist\n" . $userid);
            $end = true;
        }

        if (!$end && ($r = $c->query($sql))) {
            $row = $r->fetch_array();
            $response["code"] = 200;
            $response["description"] = "Request successful";
            $response["user_details"] = null;
            $response["user_details"]["userid"] = $userid;
            $response["user_details"]["username"] = $row["username"];
            $response["user_details"]["name"] = $row["name"];
            $response["user_details"]["surname"] = $row["surname"];
            $response["user_details"]["email"] = $row["email"];
            $response["user_details"]["born"] = $row["born"];
            $response["user_details"]["created"] = $row["created"];
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