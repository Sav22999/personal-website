<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    //header("Content-Length:".$post);
    $condition = isset($post["text"]) && isset($post["type"]) && isset($post["user"]) && isset($post["source"]) && isset($post["versionCode"]) && isset($post["language"]) && isset($post["startDate"]) && isset($post["endDate"]) && isset($post["ableToClose"]) && isset($post["button1"]) && isset($post["button1Link"]) && isset($post["button2"]) && isset($post["button2Link"]);
    //if ((variables_permission_yes_or_not(9) || (isset($post["auth"]) && getGoodString($post["auth"]) == "roberto")) && $condition) {
    if (variables_permission_yes_or_not(9) && $condition) {
        $start_date = getGoodString(isAGoodDate($post["startDate"])) ? "'" . getGoodString($post["startDate"]) . "'" : "NULL";
        $end_date = getGoodString(isAGoodDate($post["endDate"])) ? "'" . getGoodString($post["endDate"]) . "'" : "NULL";
        $text = "'" . getGoodString($post["text"]) . "'";
        //$text = "'".mb_convert_encoding($post["text"], 'UTF-32', 'UTF-8')."'";//this (probably) doesn't work
        $type = getGoodString($post["type"]) != "" ? "'" . getGoodString($post["type"]) . "'" : "NULL";
        $language = check_language(getGoodString($post["language"])) ? "'" . getGoodString($post["language"]) . "'" : "NULL";
        $source = getGoodString($post["source"]) != "" ? "'" . getGoodString($post["source"]) . "'" : "NULL";
        $version_code = (is_numeric(getGoodString($post["versionCode"])) && (getGoodString($post["versionCode"]) != "")) ? "'" . getGoodString($post["versionCode"]) . "'" : "NULL";

        $able_to_close = getGoodString($post["ableToClose"]) == "true" ? "true" : "false";

        $user = getGoodString($post["user"]) != "" ? "'" . getGoodString($post["user"]) . "'" : "NULL";

        $button1 = getGoodString($post["button1"]) != "" ? "'" . getGoodString($post["button1"]) . "'" : "NULL";
        $button1_link = getGoodString($post["button1Link"]) != "" ? "'" . getGoodString($post["button1Link"]) . "'" : "NULL";

        $button2 = getGoodString($post["button2"]) != "" ? "'" . getGoodString($post["button2"]) . "'" : "NULL";
        $button2_link = getGoodString($post["button2Link"]) != "" ? "'" . getGoodString($post["button2Link"]) . "'" : "NULL";

        //the username passed is "UserYYYYMMDDHHMMSSMMMM::CVAppSav"
        //this string is generated the first time you run the app, so it's unique
        $sql = "INSERT INTO `messages`(`id`, `start_date`, `end_date`, `text`, `type`, `user`, `language`, `source`, `version_code`, `able_to_close`, `button1`, `button1_link`, `button2`, `button2_link`,`added_date`) VALUES(NULL, " . $start_date . ", " . $end_date . ", " . $text . ", " . $type . ", " . $user . ", " . $language . ", " . $source . ", " . $version_code . ", " . $able_to_close . "," . $button1 . ", " . $button1_link . ", " . $button2 . ", " . $button2_link . ", CURRENT_TIMESTAMP)";
        if ($r = $c->query($sql)) {
            response(200, "OK", "Record inserted correctly.");
        } else {
            response(400, "Error", "Can't insert record on database.<br>" . $sql);
        }
    } else {
        response(401, "Error", "Parameters are not enough.");
    }
} else {
    response(500, "Error", "Can't connect to the database.");
}

function response($response_code, $response_status, $response_description)
{
    $response['code'] = $response_code;
    $response['status'] = $response_status;
    $response['description'] = $response_description;

    $json_response = json_encode($response);
    echo $json_response;
}

function isAGoodDate($date)
{
    $date_tmp = explode("-", $date);
    if (count($date_tmp) == 3) {
        //if there are exactly 3 elements (year, month and day)
        if (is_numeric($date_tmp[0]) && $date_tmp[0] >= 1900 && $date_tmp[0] <= date('Y')) {
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

?>