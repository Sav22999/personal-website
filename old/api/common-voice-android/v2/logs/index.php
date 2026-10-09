<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    //header("Content-Length:".$post);
    $condition = isset($post["logDate"]) && isset($post["logged"]) && ($post["logged"] == 0 || $post["logged"] == 1) && isset($post["language"]) && isset($post["version"]) && is_numeric($post["version"]) && isset($post["source"]) && isset($post["errorLevel"]) && isset($post["stackTrace"]);
    if ($condition) {
        $year = date('Y');
        $month = date("m");
        $day = date("d");

        $date = htmlspecialchars(date("Y-m-d H:i:s"));
        $logged = htmlspecialchars(strval($post["logged"]));
        $language = getGoodString($post["language"]);
        $version = htmlspecialchars($post["version"]);
        $source = getGoodString($post["source"]);
        $errorLevel = getGoodString($post["errorLevel"]);
        $logDate = getGoodString($post["logDate"]);
        $tag = "";
        if (isset($post["additionalLogs"])) {
            $tag = getGoodString($post["tag"]);
        }
        $stackTrace = getGoodString($post["stackTrace"]);
        $additionalLogs = "";
        if (isset($post["additionalLogs"])) {
            $additionalLogs = getGoodString($post["additionalLogs"]);
        }

        //the username passed is "UserYYYYMMDDHHMMSSMMMM::CVAppSav"
        //this string is generated the first time you run the app, so it's unique
        $sql = "INSERT INTO `logs`(`id`, `logDate`, `date`, `logged`, `language`, `version`, `source`, `errorLevel`, `tag`, `stackTrace`,`additionalLogs`) VALUES(NULL, '" . $logDate . "', '" . $date . "', '" . $logged . "', '" . $language . "', '" . $version . "', '" . $source . "','" . $errorLevel . "','" . $tag . "','" . $stackTrace . "','" . $additionalLogs . "')";
        if ($r = $c->query($sql)) {
            response(200, "OK", "Record inserted correctly.");
        } else {
            response(400, "Error", "Can't insert record on database.");
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

?>