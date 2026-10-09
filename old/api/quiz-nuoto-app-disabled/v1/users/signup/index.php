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
    $condition = isset($post["username"]) && getGoodString($post["username"]) != "" && isset($post["name"]) && getGoodString($post["name"]) != "" && isset($post["surname"]) && getGoodString($post["surname"]) != "" && isset($post["email"]) && getGoodString($post["email"]) != "" && isset($post["password"]) && getGoodString($post["password"]) != "" && isset($post["born"]) && getGoodString($post["born"]) != "";
    if ($condition) {
        $username = strtolower(getGoodString($post["username"]));
        $name = getGoodString($post["name"]);
        $surname = getGoodString($post["surname"]);
        $email = strtolower(getGoodString($post["email"]));
        $password = hash('sha256', getGoodString($post["password"]));
        $born = getGoodString($post["born"]);
        $userid = hash('sha256', (rand() . ($username . $email)));

        //check the username
        $sql2 = "SELECT `username` FROM `users` WHERE `username` = '" . $username . "'";
        if ($c->query($sql2)->num_rows == 1) {
            echo_error(401, "Parameter specified incorrect: the username already exists");
            $end = true;
        }

        //check the email
        $sql2 = "SELECT `email` FROM `users` WHERE `email` = '" . $email . "'";
        if ($c->query($sql2)->num_rows == 1) {
            echo_error(403, "Parameter specified incorrect: the email already exists");
            $end = true;
        }

        $sql = "INSERT INTO `users`(`id`, `name`, `surname`, `email`, `password`, `born`, `userid`, `username`, `created`) VALUES(NULL, '" . $name . "', '" . $surname . "', '" . $email . "', '" . $password . "', '" . $born . "', '" . $userid . "', '" . $username . "',NULL)";

        if (!$end && ($r = $c->query($sql))) {
            echo_error(200, "User inserted correctly");
        } else {
            echo_error(400, "Can't insert the record in the database");
        }
    } else {
        echo_error(402, "Parameters are not enough or are wrong");
        //print_r($post);
    }
} else {
    echo_error(500, "Can't connect to the database");
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