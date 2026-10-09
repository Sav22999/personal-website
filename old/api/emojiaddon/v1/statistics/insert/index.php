<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_emojiaddon_api;
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_emojiaddon_api)) {
    $c->set_charset("utf8");
    $condition = true;
    if ($condition) {
        $ip = getGoodString(getClientIpAddress());
        $action = "";
        if (isset($_GET["action"])) {
            $action = getGoodString($_GET["action"]);
        }

        $sql = "INSERT INTO `emojiaddon`(`id`, `datetime`, `ip_address`, `action`) VALUES(NULL, NULL, '" . $ip . "', '" . $action . "')";
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

function getClientIpAddress()
{
    $ipaddress = "";
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

?>