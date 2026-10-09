<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_quiz_nuoto_api, $path;
header("Content-Type:application/json");

$response = null;

$end = false;
$message_printed = false;

if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_quiz_nuoto_api)) {
    $c->set_charset("utf8");

    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    $post = $_GET;//TODO: remove this

    //GET data

    if (isset($post["username_or_email"]) && getGoodString($post["username_or_email"]) != "" && isset($post["password"]) && getGoodString($post["password"]) != "") {
        $username_or_email = strtolower(getGoodString($post["username_or_email"]));
        $password = hash('sha256', getGoodString($post["password"]));

        $sql = "SELECT * FROM `users` WHERE (`username` = '" . $username_or_email . "' OR `email` = '" . $username_or_email . "') AND `password` = '" . $password . "'";
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_array();
                $response["code"] = 200;
                $response["description"] = "Successfully logged in";
                $response["userid"] = $row["userid"];
            } else {
                echo_error(402, "The username or email or password specified are not correct");
            }
        } else {
            echo_error(401, "Error in the query");
        }
    } else {
        echo_error(400, "Parameters passed not correct");
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