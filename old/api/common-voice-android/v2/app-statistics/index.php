<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
//GET data
    $language = "";
    if (isset($_GET["language"]) && $_GET["language"] != "") {
        if ($_GET["language"] == "all") {
            $language = "null";
        } else if (check_language($_GET["language"])) {
            $language = getGoodString($_GET["language"]);
        }
    } else {
        $language = "null";
    }
    $year = date('Y');
    $month = date("m");
    $day = date("d");

    $sql = "SELECT * FROM (SELECT COUNT(*) AS `logged` FROM `statistics` WHERE YEAR(`date`)=" . $year . " AND MONTH(`date`)=" . $month . " AND DAY(`date`)=" . $day . " AND `language`='{{*{{language}}*}}' AND `logged`=1 AND `public`='true') AS logged_users, (SELECT COUNT(*) AS `users` FROM `statistics` WHERE YEAR(`date`)=" . $year . " AND MONTH(`date`)=" . $month . " AND DAY(`date`)=" . $day . " AND `language`='{{*{{language}}*}}' AND `public`='true') AS number_users";

    $response = null;

    if ($language == "null" || check_language($language)) {
        if ($language == "null") {
            foreach (get_supported_languages() as $language_tmp) {
                $sql_tmp = str_replace("{{*{{language}}*}}", $language_tmp, $sql);
                $r = $c->query($sql_tmp);
                if ($r->num_rows > 0) {
                    while ($row = $r->fetch_array()) {
                        $sub_response["users"] = $row["users"];
                        $sub_response["logged"] = $row["logged"];
                        $response[$language_tmp] = $sub_response;
                    }
                } else {
                    $sub_response["users"] = 0;
                    $sub_response["logged"] = 0;
                    $response[$language_tmp] = $sub_response;
                }
            }
        } else {
            $sql_tmp = str_replace("{{*{{language}}*}}", $language, $sql);
            $r = $c->query($sql_tmp);
            if ($r->num_rows > 0) {
                while ($row = $r->fetch_array()) {
                    $sub_response["users"] = $row["users"];
                    $sub_response["logged"] = $row["logged"];
                    $response[$language] = $sub_response;
                }
            } else {
                $sub_response["users"] = 0;
                $sub_response["logged"] = 0;
                $response[$language] = $sub_response;
            }
        }
    }

    echo json_encode($response);
} else {
    echo_null();
}

function response($response)
{
    $sub_response["users"] = 2;
    $sub_response["logged"] = 0;
    $response['en'] = $sub_response;
}

function echo_null()
{
    echo json_encode(null);
}

?>