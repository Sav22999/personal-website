<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["date"]) && isAGoodDate(getGoodString($post["date"])) && isset($post["word"]) && isset($post["definition"]) && isset($post["etymology"]) && isset($post["type"]) && isset($post["phonetics"]) && isset($post["language"]) && isSupportedLanguage(getGoodString($post["language"]));
    if (variables_permission_yes_or_not(8) && $condition) {
        $word = getGoodString($post["word"]);
        $date = getGoodString($post["date"]);
        $definition = getGoodString($post["definition"]);
        $etymology = getGoodString($post["etymology"]);
        $type = getGoodString($post["type"]);
        //$phonetics = getGoodString($post["phonetics"]);
        $phonetics = $post["phonetics"];
        $phonetics = str_replace("/", "", $phonetics);
        $language = getGoodString($post["language"]);
        $source = "";
        if (isset($post["source"])) {
            $source = getGoodString($post["source"]);
        }

        //check word
        $sql = "SELECT `word` FROM `wordoftheday_${language}` WHERE `word`='${word}'";

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 0) {

                //check date
                $sql = "SELECT `date` FROM `wordoftheday_${language}` WHERE `date`='${date}'";

                if ($r = $c->query($sql)) {
                    if ($r->num_rows == 0) {

                        $sql = "INSERT INTO `wordoftheday_${language}`(`id`,`added_date`, `word`,`date`,`definition`,`type`,`phonetics`,`etymology`,`source`) VALUES(NULL, NULL, '${word}','${date}','${definition}','${type}','${phonetics}','${etymology}','${source}')";

                        if (!$c->query($sql)) {
                            response(500, "Error", "Can't insert the record");
                            return;
                        }
                    } else {
                        response(501, "Error", "Date already inserted");
                        return;
                    }
                } else {
                    response(502, "Error", "Can't get the record from the table");
                    return;
                }
            } else {
                response(503, "Error", "Word already exists");
                return;
            }
        } else {
            response(504, "Error", "Can't get the record from the table");
            return;
        }

        response(200, "OK", "Word inserted in the database");
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