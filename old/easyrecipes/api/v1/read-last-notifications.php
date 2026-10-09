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

        $sql = "SELECT `id` FROM `notifications` WHERE `user`='" . $username . "' AND `status`=0 ORDER BY `date` DESC LIMIT 5";
        $sql_to_add = "";

        if ($r = $c->query($sql)) {
            if ($r->num_rows > 0) {
                $sql_to_add = " AND";
                for ($i = 0; $i < $r->num_rows; $i++) {
                    $row = $r->fetch_assoc();
                    $sql_to_add .= " `id`='" . $row["id"] . "'";
                    if ($i < $r->num_rows - 1) $sql_to_add .= " OR";
                }
            }
        }

        $sql = "UPDATE `notifications` SET `status`=1 WHERE `user`='" . $username . "'" . $sql_to_add;

        if (!$c->query($sql)) {
            response(501, "Error", "Can't get the record from recipes");
            return;
        }
        response(200, "OK", "All notifications are now marked as read");
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