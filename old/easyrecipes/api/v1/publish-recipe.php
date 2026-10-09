<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["title"]);
    if (check_authorisation(5) && $condition) {
        $year = date('Y');
        $month = date("m");
        $day = date("d");

        $title = htmlspecialchars(strval($post["title"]));
        $author = $_SESSION["session_id"];

        $sql = "SELECT `id` FROM `recipes` WHERE `user_inserted`='" . $author . "' AND `title`='" . $title . "'";

        $recipe_id = null;

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();
                $recipe_id = $row["id"];
            } else {
                response(502, "Error", "Unknown error");
                return;
            }
        } else {
            response(501, "Error", "Can't get the record from recipes");
            return;
        }

        if ($recipe_id != null) {
            $sql = "UPDATE `recipes` SET `status`=2 WHERE `id`='" . $recipe_id . "')";
            $c->query($sql);

            $sql = "INSERT INTO `points`(`id`,`user`,`points`,`details`,`date`) VALUES(NULL,'" . $author . "',+1,'new-recipe','" . $date . "')";
            $sql2 = "INSERT INTO `notifications`(`id`,`user`,`message`,`status`,`date`) VALUES(NULL,'" . $author . "','Hai ricevuto <b>+1</b> punti perché hai inserito una ricetta.',0,'" . $date . "')";
            $c->query($sql);
            $c->query($sql2);
            response(200, "OK", "Recipe inserted in the database");
            return;
        }
        response(503, "Error", "Unknown error (503)");
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