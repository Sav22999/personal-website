<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //GET data
    $message_id = 0;
    if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $message_id = $_GET["id"];
    }

    $details = "";
    if ($message_id != 0) {
        $details = " WHERE `id`='$message_id'";
    }
    $sql = "SELECT * FROM `messages`$details ORDER BY `id` DESC";

    $response = null;


    if ($r = $c->query($sql)) {
        $counter = 0;
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_array()) {
                $counter++;
                $sub_response["id"] = $row["id"];
                $sub_response["type"] = $row["type"];
                $sub_response["user"] = $row["user"];
                $sub_response["versionCode"] = $row["version_code"];
                $sub_response["language"] = $row["language"];
                $sub_response["source"] = $row["source"];
                $sub_response["startDate"] = $row["start_date"];
                $sub_response["endDate"] = $row["end_date"];
                $sub_response["text"] = $row["text"];
                $sub_response["ableToClose"] = (bool)$row["able_to_close"];
                $sub_response["button1"] = $row["button1"];
                $sub_response["button1Link"] = $row["button1_link"];
                $sub_response["button2"] = $row["button2"];
                $sub_response["button2Link"] = $row["button2_link"];
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