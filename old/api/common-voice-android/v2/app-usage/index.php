<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    //header("Content-Length:".$post);
    $condition = isset($post["logged"]) && ($post["logged"] == 0 || $post["logged"] == 1) && isset($post["language"]) && isset($post["version"]) && is_numeric($post["version"]) && isset($post["source"]) && isset($post["type"]) && is_numeric($post["type"]) && isset($post["username"]) && isset($post["offline"]) && is_numeric($post["offline"]);
    if ($condition) {
        $year = date('Y');
        $month = date("m");
        $day = date("d");

        /*
        $date = htmlspecialchars(date("Y-m-d H:i:s"));
        $logged = htmlspecialchars(strval($post["logged"]));
        $language = getGoodString($post["language"]);
        $version = htmlspecialchars($post["version"]);
        $source = getGoodString($post["source"]);
        $type = (int)$post["type"];
        $username = getGoodString($post["username"]);
        $offline = (int)$post["offline"] == 1 ? "1" : "0";
        $sentence_id = isset($post["sentence_id"]) ? getGoodString($post["sentence_id"]) : "";
        $clip_id = isset($post["clip_id"]) ? getGoodString($post["clip_id"]) : "";
        $details = isset($post["details"]) ? getGoodString($post["details"]) : "";


        $sql = "INSERT INTO `usage`(`id`, `date`, `logged`, `language`, `version`, `source`, `type`, `username`, `offline`, `sentence_id`, `clip_id`, `details`) VALUES(NULL,'" . $date . "', '" . $logged . "', '" . $language . "', '" . $version . "', '" . $source . "','" . $type . "','" . $username . "','" . $offline . "','" . $sentence_id . "','" . $clip_id . "','" . $details . "')";
        if ($r = $c->query($sql)) {
            response(200, "OK", "Record inserted correctly.");
        } else {
            response(401, "Error", "Can't insert record on database.");
        }
        */

        $date = htmlspecialchars(date("Y-m-d H:i:s"));
        $logged = htmlspecialchars(strval($post["logged"]));
        $language = getGoodString($post["language"]);
        $version = htmlspecialchars($post["version"]);
        $source = getGoodString($post["source"]);
        $type = (int)$post["type"];
        $username = getGoodString($post["username"]);
        $offline = (int)$post["offline"] == 1 ? "1" : "0";
        $sentence_id = isset($post["sentence_id"]) ? getGoodString($post["sentence_id"]) : "";
        $clip_id = isset($post["clip_id"]) ? getGoodString($post["clip_id"]) : "";
        $details = isset($post["details"]) ? getGoodString($post["details"]) : "";

        //the username passed is "UserYYYYMMDDHHMMSSMMMM::CVAppSav"
        //this string is generated the first time you run the app, so it's unique
        $sql = "INSERT INTO `usage` 
            (`id`, `date`, `logged`, `language`, `version`, `source`, `type`, `username`, `offline`, `sentence_id`, `clip_id`, `details`) 
            VALUES 
            (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $c->prepare($sql);

        // Check if the prepare statement succeeded
        if ($stmt === false) {
            response(401, "Error", "Can't prepare statement.");
        }

        $stmt->bind_param('sssssisssss', $date, $logged, $language, $version, $source, $type, $username, $offline, $sentence_id, $clip_id, $details);

        // Execute the statement
        if ($stmt->execute()) {
            response(200, "OK", "Record inserted correctly.");
        } else {
            response(401, "Error", "Can't insert record on database.");
        }
    } else {
        response(402, "Error", "Parameters are not enough.");
    }
} else {
    response(500, "Error<<", "Can't connect to the database.");
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