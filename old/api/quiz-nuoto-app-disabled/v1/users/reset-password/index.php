<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_quiz_nuoto_api;
header("Content-Type:application/json");

$response = null;

$end = false;
$message_printed = false;

if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_quiz_nuoto_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    $post = $_GET;//TODO: remove this

    //header("Content-Length:".$post);
    $condition = isset($post["username_or_email"]) && getGoodString($post["username_or_email"]) != "" && isset($post["password"]) && getGoodString($post["password"]) != "";
    if ($condition) {
        $username_or_email = getGoodString($post["username_or_email"]);
        $new_password = hash('sha256', getGoodString($post["password"]));

        $sql = "UPDATE `users` SET `password`='" . $new_password . "' WHERE (`username` = '" . $username_or_email . "' OR `email` = '" . $username_or_email . "')";

        if (!$end && ($r = $c->query($sql))) {
            echo_error(200, "Password changed correctly");
        } else {
            echo_error(400, "Can't insert the record in the database");
        }
    } else {
        echo_error(402, "Parameters are not enough or are wrong.");
    }
} else {
    echo_error(500, "Can't connect to the database.");
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