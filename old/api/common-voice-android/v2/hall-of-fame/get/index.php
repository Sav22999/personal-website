<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //GET data

    $sql = "SELECT * FROM `halloffame` ORDER BY `week` DESC, `added` ASC, `updated` ASC";

    $response = null;

    $type_to_string = array("0" => "top-contributed", "1" => "top-trending");

    if ($r = $c->query($sql)) {
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_array()) {
                $languages = get_supported_languages_with_full_native_name();

                //print_r($response);

                $response[$row["year"] . "-" . $row["week"]][$type_to_string[$row["type"]]]["language_name"] = $languages[$row["language"]]["english"];
                $response[$row["year"] . "-" . $row["week"]][$type_to_string[$row["type"]]]["language_code"] = $row["language"];
                $response[$row["year"] . "-" . $row["week"]][$type_to_string[$row["type"]]]["link"] = $row["link"];
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