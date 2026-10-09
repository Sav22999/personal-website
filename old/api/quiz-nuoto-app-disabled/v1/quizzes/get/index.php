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

    $sql = "SELECT `modified` AS `last-update` FROM `quizzes` ORDER BY `modified` DESC LIMIT 1";
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

    $sql = "SELECT `quiz`, `chapter`, `section`, `question`, `A`, `B`, `C`, `D`, `correct` FROM `quizzes` ORDER BY `chapter` ASC";
    if (isset($_GET["chapter"]) && $_GET["chapter"] != "") {
        $sql2 = "SELECT `chapter` FROM `chapters` ORDER BY `chapter` ASC";
        $chapters_list = array();
        if ($r = $c->query($sql2)) {
            while ($row = $r->fetch_array()) {
                array_push($chapters_list, $row["chapter"]);
            }
        }
        $chapter = $_GET["chapter"];
        if (!in_array($chapter, $chapters_list)) {
            echo_error(101, "Parameter specified incorrect: the chapter doesn't exist");
        } else {
            $end = false;
        }

        $sql = "SELECT `quiz`, `chapter`, `section`, `question`, `A`, `B`, `C`, `D`, `correct` FROM `quizzes` WHERE `chapter` = '" . $chapter . "' ORDER BY `chapter` ASC";
    } else if (isset($_GET["question"]) && $_GET["question"] != "") {
        $sql2 = "SELECT `quiz` FROM `quizzes` ORDER BY `quiz` ASC";
        $quizzes_list = array();
        if ($r = $c->query($sql2)) {
            while ($row = $r->fetch_array()) {
                array_push($quizzes_list, $row["quiz"]);
            }
        }
        $quiz = $_GET["question"];
        if (!in_array($quiz, $quizzes_list)) {
            echo_error(102, "Parameter specified incorrect: the question doesn't exist");
        } else {
            $end = false;
        }

        $sql = "SELECT `quiz`, `chapter`, `section`, `question`, `A`, `B`, `C`, `D`, `correct` FROM `quizzes` WHERE `quiz` = '" . $quiz . "' ORDER BY `chapter` ASC";
    }

    $response["questions"] = null;

    if (!$end && ($r = $c->query($sql))) {
        if ($r->num_rows > 0) {
            $end = false;
            $counter = 0;
            while ($row = $r->fetch_array()) {
                $response["questions"][$counter] = null;

                $response["questions"][$counter]["id"] = $row["quiz"];
                $response["questions"][$counter]["chapter"] = $row["chapter"];
                $response["questions"][$counter]["section"] = $row["section"];
                $response["questions"][$counter]["question"] = $row["question"];
                $response["questions"][$counter]["A"] = $row["A"];
                $response["questions"][$counter]["B"] = $row["B"];
                $response["questions"][$counter]["C"] = $row["C"];
                $response["questions"][$counter]["D"] = $row["D"];
                $response["questions"][$counter]["correct"] = $row["correct"];

                $counter++;
            }
        } else {
            echo_error(504, "Values returned not enough");
        }
    } else {
        echo_error(503, "Error query (2)");
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