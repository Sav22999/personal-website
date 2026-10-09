<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //GET data
    $limit = 100;
    if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
        if ($_GET["limit"] >= 1000) {
            $limit = 1000;
        } else if ($_GET["limit"] < 1000 && $_GET["limit"] > 0) {
            $limit = $_GET["limit"];
        }
    }

    $id = null;
    if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $id = $_GET["id"];
    }

    if ($id == null) {
        $sql = "SELECT * FROM `logs` ORDER BY `date` DESC LIMIT " . $limit;
    } else {
        $sql = "SELECT * FROM `logs` WHERE `id`='" . $id . "' ORDER BY `date` DESC";
    }

    $response = null;


    if ($r = $c->query($sql)) {
        $counter = 0;
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_array()) {
                $counter++;
                $sub_sub_response_general["id"] = $row["id"];
                $sub_sub_response_general["logDate"] = $row["logDate"];
                $sub_sub_response_general["date"] = $row["date"];
                $sub_sub_response_general["logged"] = $row["logged"];
                $sub_sub_response_general["language"] = $row["language"];
                $sub_sub_response_general["version"] = $row["version"];
                $sub_sub_response_general["source"] = $row["source"];
                $sub_sub_response_log["errorLevel"] = $row["errorLevel"];
                $sub_sub_response_log["tag"] = $row["tag"];
                $sub_sub_response_log["stackTrace"] = $row["stackTrace"];
                $sub_sub_response_log["additionalLogs"] = $row["additionalLogs"];
                $sub_response["general"] = $sub_sub_response_general;
                $sub_response["log"] = $sub_sub_response_log;
                $response[$counter] = $sub_response;
            }
            echo json_encode($response);
        } else {
            echo_null();
        }
    } else {
        echo_null();
    }
} else {
    echo_null();
}

function echo_null()
{
    echo json_encode(null);
}

?>