<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $post = json_decode(file_get_contents('php://input'), true);
    //header("Content-Length:".$post);
    $condition = isset($post["top-contributed"]) && isset($post["top-trending"]) && isset($post["token"]);
    if ($condition) {
        if (check_language(getGoodString($post["top-contributed"])) && check_language(getGoodString($post["top-trending"]))) {
            if (getGoodString($post["token"]) == "e1e8987dec7d6b663fe46d0d2ab9b410b4a311ec4e03e1d655cb8d0624f13679") {
                $year = date('Y', strtotime("this week"));
                $week = date("W", strtotime("this week"));

                $next_year = date('Y', strtotime("next week"));
                $next_week = date('W', strtotime("next week"));

                $link_top_contributed = "";
                if (isset($post["link-top-contributed"]) && getGoodUrl($post["link-top-contributed"]) != "") $link_top_contributed = getGoodUrl($post["link-top-contributed"]);
                $link_top_trending = "";
                if (isset($post["link-top-trending"]) && getGoodUrl($post["link-top-trending"]) != "") $link_top_trending = getGoodUrl($post["link-top-trending"]);

                $sql = "(SELECT * FROM `halloffame` WHERE `week` = '" . $week . "' AND `year` = '" . $year . "' LIMIT 2) UNION (SELECT * FROM `halloffame` WHERE `week` = '" . $next_week . "' AND `year` = '" . $next_year . "' LIMIT 2)";
                if ($r = $c->query($sql)) {
                    if ($r->num_rows < 4) {
                        $new_date = date("Y-m-d h:i:s");
                        $sql = "INSERT INTO `halloffame`(`id`, `week`, `year`, `type`, `language`, `link`, `added`, `updated`) VALUES (NULL, '" . $week . "', '" . $year . "', '0', '" . getGoodString($post["top-contributed"]) . "','" . $link_top_contributed . "', NULL, '" . $new_date . "'), (NULL, '" . $week . "', '" . $year . "', '1', '" . getGoodString($post["top-trending"]) . "','" . $link_top_trending . "', NULL, '" . $new_date . "'), (NULL, '" . $next_week . "', '" . $next_year . "', '0', '" . getGoodString($post["top-contributed"]) . "','" . $link_top_contributed . "', NULL, '" . $new_date . "'), (NULL, '" . $next_week . "', '" . $next_year . "', '1', '" . getGoodString($post["top-trending"]) . "','" . $link_top_trending . "', NULL, '" . $new_date . "')";
                        //echo $sql;
                        if ($r = $c->query($sql)) {
                            response(200, "OK", "Record inserted correctly.");
                        } else {
                            response(400, "Error", "Can't insert record on database.");
                        }
                    } else {
                        $counter = 0;
                        $new_date = date("Y-m-d h:i:s");
                        $sql = "UPDATE `halloffame` SET `language`= '" . getGoodString($post["top-contributed"]) . "', `updated`='" . $new_date . "' WHERE ((`week` = '" . $week . "' AND `year` = '" . $year . "') OR (`week` = '" . $next_week . "' AND `year` = '" . $next_year . "')) AND `type` = '0'";
                        if ($r = $c->query($sql)) {
                            $counter++;
                        }
                        $sql = "UPDATE `halloffame` SET `language`= '" . getGoodString($post["top-trending"]) . "', `updated`='" . $new_date . "' WHERE ((`week` = '" . $week . "' AND `year` = '" . $year . "') OR (`week` = '" . $next_week . "' AND `year` = '" . $next_year . "')) AND `type` = '1'";
                        if ($r = $c->query($sql)) {
                            $counter++;
                        }
                        if ($counter == 2) {
                            response(200, "OK", "Record replaced correctly.");
                        } else {
                            response(401, "Error", "Can't insert record on database.");
                        }
                    }
                } else {
                    response(402, "Error", "Unexpected error.");
                }
            } else {
                response(403, "Error", "Token is not valid.");
            }
        } else {
            response(404, "Error", "One language (or both) is not supported.");
        }
    } else {
        response(405, "Error", "Parameters are not enough or wrong.");
    }
} else {
    response(500, "Error", "Can't connect to the database.");
}

function response($response_code, $response_status, $response_description)
{
    $response['code'] = $response_code;
    $response['status'] = $response_status;
    $response['description'] = $response_description;

    $json_response = json_encode($response);
    echo $json_response;
}

function getGoodUrl($string)
{
    //return str_replace(" ", "", str_replace("`", "'", filter_var($string, FILTER_SANITIZE_URL, FILTER_FLAG_STRIP_HIGH)));
    //return str_replace("\\", "\\\\",str_replace("`", "'",$string));
    return getGoodString($string);
}

?>