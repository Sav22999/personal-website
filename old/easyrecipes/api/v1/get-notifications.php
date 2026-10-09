<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $get = $_GET;

    $condition = 1;
    if ($condition) {
        $username = "";
        if (isset($get["username"])) $username = $get["username"];
        if (check_authorisation(2)) $username = $_SESSION["session_id"];

        if ($username == "") {
            response(400, "Error", "Username invalid");
            return;
        }

        $total_json = json_decode("{}", true);

        $sql = "SELECT * FROM `notifications` WHERE `user`='" . $username . "' ORDER BY `date` DESC";

        if ($r = $c->query($sql)) {
            $total_json = "[";
            for ($i = 1; $i <= $r->num_rows; $i++) {
                $row = $r->fetch_assoc();

                if ($i > 1) $total_json .= ',';
                $total_json .= '{"id":' . $row["id"];
                $total_json .= ', "message":"' . $row["message"] . '"';
                $total_json .= ', "status":"' . $row["status"] . '"';
                $total_json .= ', "date":"' . $row["date"] . '"}';
            }
            $total_json .= "]";
            $total_json = json_decode($total_json, true);
        } else {
            response(501, "Error", "Can't get the record from recipes");
            return;
        }
        response(200, "OK", $total_json);
    } else {
        if (!$condition) {
            response(401, "Error", "Parameters are not enough. Check all required fields. Passed: " . $get["title"] . "");
        } else {
            response(402, "Error", "Something was wrong in POST request");
        }
    }
} else {
    response(500, "Error", "Can't connect to the database");
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