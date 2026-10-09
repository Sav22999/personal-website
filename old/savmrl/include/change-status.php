<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/savmrl/include/variables.php");
global $path_savmrl;
header("Content-Type:application/json");
$post = json_decode(file_get_contents('php://input'), true); //POST request

if (variables_permission_yes_or_not(10) && isset($post["id"])) {
    global $localhost_db, $username_db, $password_db, $database_savmrl_api;
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl_api);
    $c->set_charset("utf8");
    //prepare the statement
    //get the status ('reported' field) of the link and, if it is NULL, set to 1, else set to NULL
    //it's a phpMyAdmin (mySQL) database query

    $stmt = $c->prepare("UPDATE `redirect_savmrl` SET `reported` = IF(`reported` IS NULL, 1, NULL) WHERE `id` = ?");
    $stmt->bind_param("i", $post["id"]);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        //if the query was successful, return a success message
        echo json_encode(["status" => "success", "message" => "Status updated successfully."]);
    } else {
        //if the query failed, return an error message
        echo json_encode(["status" => "error", "message" => "Failed to update status or no changes made."]);
    }
    $stmt->close();
    $c->close();
} else {
    //if the user does not have permission to change the status, return an error message
    echo json_encode(["status" => "error", "message" => "You don't have enough permissions to change the status of this link."]);
}
?>