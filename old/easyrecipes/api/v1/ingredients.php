<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
//GET data
    $sql = "SELECT * FROM ingredients";

    $response = array();

    $r = $c->query($sql);
    if ($r->num_rows > 0) {
        while ($row = $r->fetch_array()) {
            $sub_response["id"] = $row["id"];
            $sub_response["name"] = $row["value"];
            $sub_response["type"] = $row["type"];
            array_push($response, $sub_response);
        }
        usort($response, "cmp");
        echo json_encode($response);
    } else {
        echo_null();
    }

} else {
    echo_null();
}

function echo_null()
{
    echo json_encode("[]");
}

function cmp($a, $b)
{
    return strcmp(strtolower($a['name']), strtolower($b['name']));
}

?>