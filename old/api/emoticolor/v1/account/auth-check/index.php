<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/api/emoticolor/credentials.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/api/emoticolor/api-functions.php");
global $localhost_db, $username_db, $password_db, $name_db;
header("Content-Type:application/json");
$post = json_decode(file_get_contents('php://input'), true); //POST request
$get = $_GET; //GET request

$condition = isset($post[""]);
if ($condition) {
    $response = null;

    //Using prepared statements -> it's the safest way for MySQL queries
    if ($c = new mysqli($localhost_db, $username_db, $password_db, $name_db)) {
        $c->set_charset("utf8mb4");

        //if it's required to get the password using the token, then use the commented code below

        /*global $logins_table, $users_table, $tokens_table;
        $login_id = $post["login-id"];
        $token = $post["token"];

        $stmt = $c->prepare("SELECT * FROM $logins_table WHERE `login-id` = ? AND `status` = 1 AND (`expiry` > NOW() OR `expiry` IS NULL)");
        $stmt->bind_param("s", $login_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row["user-id"];

            $stmt = $c->prepare("SELECT * FROM $users_table WHERE `email` = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $password_from_users_table = $row["password"];

                $stmt = $c->prepare("SELECT * FROM $tokens_table WHERE `login-id` = ? AND `status` = 1 AND (`expiry` > NOW() OR `expiry` IS NULL)");
                $stmt->bind_param("s", $login_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();


                if ($result->num_rows > 0) {
                    $found = false;
                    $password_decrypted = null;
                    $password_encrypted = null;

                    while (($row = $result->fetch_assoc()) && !$found) {
                        $password_temp_decrypted = decryptTextWithPassword($row["password"], $token);

                        if (encryptHash($password_temp_decrypted) == $password_from_users_table) {
                            $found = true;
                            $password_decrypted = $password_temp_decrypted;
                            $password_encrypted = $row["password"];
                        }
                    }
                    $row = $result->fetch_assoc();

                    if ($found) {
                        $password_hash = encryptHash($password_decrypted);

                        //now it's found the password using token, login-id
                        // START of the code ====

                        //HERE!

                        //END of the code ====
                    } else {
                        $response = echo_error(405);
                    }
                } else {
                    $response = echo_error(404);
                }
            } else {
                $response = echo_error(403);
            }
        } else {
            $response = echo_error(402);
        }
        */

        responseSuccess(200);

        $c->close();
    } else {
        responseError(401);
    }
} else {
    responseError(400);
}

?>