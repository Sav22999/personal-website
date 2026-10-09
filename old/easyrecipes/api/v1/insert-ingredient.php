<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["name"]) && isset($post["type"]);
    if (check_authorisation(7) && $condition) {
        $name = htmlspecialchars(strval(html_entity_decode($post["name"], ENT_HTML5)));
        $name = str_replace("'", "&#39;", $name);
        $type = $post["type"];

        $sql = "SELECT * FROM `ingredients` WHERE `value`='" . $name . "'";

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 0) {
                $sql = "INSERT INTO `ingredients`(`id`,`value`,`type`) VALUES(NULL,'" . $name . "','" . $type . "')";

                if (!$c->query($sql)) {
                    response(503, "Error", "Can't insert record in ingredients");
                    return;
                }
            } else {
                response(502, "Error", "Ingredient already exists");
                return;
            }
        } else {
            response(501, "Error", "Can't get the record from ingredients");
            return;
        }

        $author = $_SESSION["session_id"];
        $date = htmlspecialchars(date("Y-m-d H:i:s"));
        $sql = "INSERT INTO `points`(`id`,`user`,`points`,`details`,`date`) VALUES(NULL,'" . $author . "',+1,'new-ingredient','" . $date . "')";
        $c->query($sql);

        response(200, "OK", "Ingredient inserted in the database");
    } else {
        if (!check_authorisation(5)) {
            global $your_privileges;
            response(401, "Error", "You don't have enough privileges. Your privileges: " . $your_privileges);
        } else if (!$condition) {
            response(400, "Error", "Parameters are not enough. Check all required fields. Passed: " . $post["title"] . "");
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