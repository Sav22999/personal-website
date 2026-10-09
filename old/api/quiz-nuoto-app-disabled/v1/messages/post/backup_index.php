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
    $condition = isset($post["userid"]) && $post["userid"] != "" && isset($post["reply_to"]) && $post["reply_to"] != "" && isset($post["section"]) && $post["section"] != "" && isset($post["text"]) && $post["text"] != "";
    if ($condition) {
        $userid = getGoodString($post["userid"]);
        $reply_to = getGoodString($post["reply_to"]);
        $section = getGoodString($post["section"]);
        $text = getGoodString($post["text"]);

        $sql = "INSERT INTO `messages`(`id`, `reply_to`, `userid`, `section`, `text`, `datetime`) VALUES(NULL, '" . $reply_to . "', '" . $userid . "', '" . $section . "', '" . $text . "', NULL)";

        //check the userid
        $sql2 = "SELECT `userid` FROM `users` WHERE `userid` = '" . $userid . "'";
        if ($c->query($sql2)->num_rows != 1) {
            echo_error(401, "Parameter specified incorrect: the userid doesn't exist");
            $end = true;
        }

        if ($reply_to != "-1") {
            $sql3 = "SELECT `id` FROM `messages` WHERE `id` = '" . $reply_to . "'";
            if ($c->query($sql3)->num_rows != 1) {
                echo_error(402, "Parameter specified incorrect: the message id (reply_to) doesn't exist");
                $end = true;
            }
        }

        $sql4 = "SELECT `section` FROM `sections` WHERE `section` = '" . $section . "'";
        if ($c->query($sql4)->num_rows != 1) {
            echo_error(403, "Parameter specified incorrect: the section doesn't exist");
            $end = true;
        }

        if (!$end && ($r = $c->query($sql))) {
            echo_error(200, "Message inserted correctly");

            $sql5 = "SELECT `id` FROM `messages` WHERE `userid` = '" . $userid . "' AND `section` = '" . $section . "' AND `text` = '" . $text . "' AND `reply_to` = '" . $reply_to . "'";
            if ($r = $c->query($sql5)->num_rows != 1) {
                echo_error(404, "Parameter specified incorrect: the section doesn't exist");
                $end = true;
            } else {
                $row = $r->fetch_array();
                $message_id = $row["id"];
                $response["code"] = 200;
                $response["description"] = "Successfully inserted";
                $response["message_id"] = $message_id;
            }
        } else {
            echo_error(400, "Can't insert the record in the database\n" . $sql);
        }
    } else {
        echo_error(405, "Parameters are not enough or are wrong");
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