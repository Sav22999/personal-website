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
    $condition = isset($post["userid"]) && $post["userid"] != "" && isset($post["type"]) && $post["type"] != "" && isset($post["datetime"]) && $post["datetime"] != "" && isset($post["correct_answer"]) && $post["correct_answer"] != "" && isset($post["user_answer"]) && $post["user_answer"] != "" && isset($post["question_id"]) && $post["question_id"] != "" && isset($post["milliseconds"]) && $post["milliseconds"] != "";
    if ($condition) {
        $userid = getGoodString($post["userid"]);
        $type = (int)getGoodString($post["type"]);
        $datetime = getGoodString($post["datetime"]);
        $correct_answer = getGoodString($post["correct_answer"]);
        $user_answer = getGoodString($post["user_answer"]);
        $question_id = (int)getGoodString($post["question_id"]);
        $milliseconds = (int)getGoodString($post["milliseconds"]);

        $sql = "INSERT INTO `statistics`(`id`, `userid`, `type`, `datetime`, `correct_answer`, `user_answer`, `question_id`, `milliseconds`) VALUES(NULL, '" . $userid . "', '" . $type . "', '" . $datetime . "', '" . $correct_answer . "', '" . $user_answer . "', '" . $question_id . "', '" . $milliseconds . "')";
        $sql_update = "UPDATE `statistics` SET `datetime` = '" . $datetime . "', `correct_answer` = '" . $correct_answer . "', `user_answer` = '" . $user_answer . "', `milliseconds` = '" . $milliseconds . "'";
        $sql3 = "SELECT * FROM `statistics` WHERE `userid` = '" . $userid . "' AND `question_id` = '" . $question_id . "' AND `type` = '" . $type . "'";

        //check the userid
        $sql2 = "SELECT `userid` FROM `users` WHERE `userid` = '" . $userid . "'";
        if ($c->query($sql2)->num_rows != 1) {
            echo_error(401, "Parameter specified incorrect: the userid doesn't exist");
            $end = true;
        }

        if ($c->query($sql3)->num_rows == 0) {
            //to add
            if (!$end && ($r = $c->query($sql))) {
                echo_error(200, "Statistics inserted correctly");
            } else {
                echo_error(400, "Can't insert the record in the database\n" . $sql);
            }
        } else {
            //already exist, so update it
            if (!$end && ($r = $c->query($sql_update))) {
                echo_error(200, "Statistics updated correctly");
            } else {
                echo_error(400, "Can't insert the record in the database\n" . $sql);
            }
        }
    } else {
        echo_error(402, "Parameters are not enough or are wrong");
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