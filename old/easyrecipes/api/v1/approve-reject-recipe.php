<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["recipe_id"]) && isset($post["status"]) && ($post["status"] == 3 || $post["status"] == 4);
    if (check_authorisation(7) && $condition) {
        $recipe_id = htmlspecialchars(strval($post["recipe_id"]), ENT_HTML5);
        $status = htmlspecialchars(strval($post["status"]), ENT_HTML5);

        $reviewer = $_SESSION["session_id"];
        $date = htmlspecialchars(date("Y-m-d H:i:s"));

        $sql = "SELECT * FROM `recipes` WHERE `id`='" . $recipe_id . "'";

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();
                $author = $row["user_inserted"];
                $old_status = $row["status"];
                if ($status != $old_status) {
                    $sql = "UPDATE `recipes` SET `status`='" . $status . "', `date_approved`='" . $date . "', `user_approved`='" . $reviewer . "' WHERE `id`='" . $recipe_id . "'";

                    if ($c->query($sql)) {
                        $sql = "INSERT INTO `points`(`id`,`user`,`points`,`details`,`date`) VALUES(NULL,'" . $reviewer . "',+1,'reviewed-recipe','" . $date . "')";
                        $c->query($sql);
                        $sql = "";
                        $sql2 = "";
                        if ($status == 3) {
                            response(200, "OK", "Recipe approved");
                            $sql = "INSERT INTO `points`(`id`,`user`,`points`,`details`,`date`) VALUES(NULL,'" . $author . "',+5,'recipe-approved','" . $date . "')";
                            $sql2 = "INSERT INTO `notifications`(`id`,`user`,`message`,`status`,`date`) VALUES(NULL,'" . $author . "','Una tua ricetta è stata approvata e pubblicata, quindi hai ricevuto <b>+5</b> punti.',0,'" . $date . "')";
                        } elseif ($status == 4) {
                            response(201, "OK", "Recipe rejected");
                            $sql = "INSERT INTO `points`(`id`,`user`,`points`,`details`,`date`) VALUES(NULL,'" . $author . "',-2,'recipe-rejected','" . $date . "')";
                            $sql2 = "INSERT INTO `notifications`(`id`,`user`,`message`,`status`,`date`) VALUES(NULL,'" . $author . "','Una tua ricetta è stata rifiutata pertanto sono stati scalati <b>-2</b> punti.',0,'" . $date . "')";
                        }
                        if ($sql != "") $c->query($sql);
                        if ($sql2 != "") $c->query($sql2);
                        return;
                    } else {
                        response(502, "Error", "Unknown error");
                        return;
                    }
                } else {
                    response(504, "Error", "The status is the same");
                    return;
                }
            } else {
                response(503, "Error", "Unknown error");
                return;
            }
        } else {
            response(501, "Error", "Can't get the record from recipes");
            return;
        }
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