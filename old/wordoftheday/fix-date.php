<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["id"]) && isset($post["language"]) && isSupportedLanguage($post["language"]);
    if (variables_permission_yes_or_not(8) && $condition) {
        $id = getGoodString($post["id"]);
        $language = getGoodString($post["language"]);
        $date = "";

        //get the latest date
        $sql = "SELECT `date` FROM `wordoftheday_${language}` ORDER BY `date` DESC LIMIT 1";

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $date_to_return = date('Y-m-d', strtotime(date("h:i:s") . ' +0 day'));
                $row = $r->fetch_assoc();
                $date_temp = $row["date"];
                if ($date_temp >= $date_to_return) {
                    $date_to_return = date('Y-m-d', strtotime($date_temp . ' +1 day'));
                }
                $date = $date_to_return;

                $sql = "UPDATE `wordoftheday_${language}` SET `date` = '${date}' WHERE `id` = ${id}";

                if (!$c->query($sql)) {
                    response(500, "Error", "Can't update the record");
                    return;
                }
            } else {
                response(501, "Error", "Date wrong");
                return;
            }
        }
        response(200, "OK", "Word updated in the database");
    } else {
        if (!variables_permission_yes_or_not(10)) {
            global $your_privileges;
            response(401, "Error", "You don't have enough privileges to add word. Your privileges: " . $your_privileges);
        } else if (!$condition) {
            response(400, "Error", "Parameters are not enough. Check all required fields.");
        } else {
            response(402, "Error", "Something was wrong in POST request");
        }
    }
    $c->close();
} else {
    response(505, "Error", "Can't connect to the database");
}

function response($response_code, $response_status, $response_description)
{
    $response['code'] = $response_code;
    $response['status'] = $response_status;
    $response['description'] = $response_description;

    http_response_code($response_code);

    $json_response = json_encode($response);
    echo $json_response;
}

function isAGoodDate($date)
{
    $date_tmp = explode("-", $date);
    if (count($date_tmp) == 3) {
        //if there are exactly 3 elements (year, month and day)
        if (is_numeric($date_tmp[0]) && $date_tmp[0] >= 1900 && $date_tmp[0] <= (date('Y') + 2)) {
            //year
            if (is_numeric($date_tmp[1]) && $date_tmp[1] >= 1 && $date_tmp[1] <= 12) {
                //month
                if (is_numeric($date_tmp[2]) && $date_tmp[2] >= 1 && $date_tmp[2] <= 31) {
                    return true;
                }
            }
        }
    }
    return false;
}

function getYear($date)
{
    return explode("-", $date)[0];
}

function getMonth($date)
{
    return explode("-", $date)[1];
}

function getDay($date)
{
    return explode("-", $date)[2];
}

function isSupportedLanguage($lang)
{
    return ($lang === "it" || $lang === "en" || $lang === "fr");
}

?>