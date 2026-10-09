<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    //header("Content-Length:".$post);
    $condition = isset($post["logged"]) && ($post["logged"] == 0 || $post["logged"] == 1) && isset($post["username"]) && isset($post["language"]) && isset($post["version"]) && isset($post["public"]);
    if ($condition) {
        $year = date('Y');
        $month = date("m");
        $day = date("d");

        $date = getGoodString(date("Y-m-d H:i:s"));
        $logged = getGoodString($post["logged"]);
        $username = getGoodString($post["username"]);
        $language = getGoodString($post["language"]);
        $version = getGoodString($post["version"]);
        $public = getGoodString($post["public"]);
        $source = "n.d.";
        if (isset($post["source"])) {
            $source = getGoodString($post["source"]);
        }

        //the username passed is "UserYYYYMMDDHHMMSSMMMM::CVAppSav"
        //this string is generated the first time you run the app, so it's unique

        //check today is already inserted that statistics with that username
        $sql = "SELECT `date` FROM statistics WHERE `username`='" . $username . "' AND YEAR(`date`)=" . $year . " AND MONTH(`date`)=" . $month . " AND DAY(`date`)=" . $day;
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 0) {
                //insert the data in the db
                $sql = "INSERT INTO statistics(`id`, `date`, `logged`, `username`, `language`, `version`, `public`, `source`) VALUES(NULL,'" . $date . "', '" . $logged . "', '" . $username . "', '" . $language . "', '" . $version . "', '" . $public . "', '" . $source . "')";
                if ($r = $c->query($sql)) {
                    response(200, "OK", "Record inserted correctly.");
                } else {
                    response(403, "Error", "Can't insert record on database.");
                }
            } else {
                response(400, "Error", "Record has already inserted today.");
            }
        } else {
            response(401, "Error", "Something was wrong in POST request.");
        }
    } else {
        response(402, "Error", "Parameters are wrong, check them.");
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