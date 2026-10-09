<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api)) {
    $c->set_charset("utf8mb4");
    //GET data
    $condition = isset($_GET["id"]) && $_GET["id"] != "";
    $user_id = null;
    $response = null;
    if ($condition) {
        $user_id = getGoodString($_GET["id"]);
        if (!is_in("User", $user_id)) {
            $user_id = "User" . $user_id;
        }
        if (!is_in("::CVAppSav", $user_id)) {
            $user_id = $user_id . "::CVAppSav";
        }
        $year = date('Y');
        $month = date("m");
        $day = date("d");
        $start_date_bool = false;
        $start_date_array["year"] = 0;
        $start_date_array["month"] = 0;
        $start_date_array["day"] = 0;
        $end_date_array["year"] = 0;
        $end_date_array["month"] = 0;
        $end_date_array["day"] = 0;
        //start date is set, otherwise i assume "always" by default
        if (isset($_GET["start_date"])) {
            $start_date_get = getGoodString($_GET["start_date"]);
            if ($start_date_get == "always") {
                //i don't need end date
                $start_date_bool = false;
            } else if ($start_date_get == "today" || !isAGoodDate(getGoodString($start_date_get))) {
                //today --> i don't need to know the end date (which is "today" by default)
                $start_date_bool = true;

                $start_date_array["year"] = $year;
                $start_date_array["month"] = $month;
                $start_date_array["day"] = $day;

                $end_date_array["year"] = $year;
                $end_date_array["month"] = $month;
                $end_date_array["day"] = $day;
            } else if (isAGoodDate(getGoodString($start_date_get))) {
                //start date is set
                $start_date_bool = true;
                //TODO: get the year, month and day by param

                $start_date_array["year"] = getYear(getGoodString($_GET["start_date"]));
                $start_date_array["month"] = getMonth(getGoodString($_GET["start_date"]));
                $start_date_array["day"] = getDay(getGoodString($_GET["start_date"]));

                if (!isset($_GET["end_date"]) || (isset($_GET["end_date"]) && getGoodString($_GET["end_date"]) == "today" || !isAGoodDate(getGoodString($_GET["end_date"])))) {
                    //the end date is not set, i assume it as "today" by default
                    //OR the end date is today

                    $end_date_array["year"] = $year;
                    $end_date_array["month"] = $month;
                    $end_date_array["day"] = $day;
                } else if (isAGoodDate(getGoodString($_GET["end_date"]))) {
                    //the end date is set
                    //TODO: get the year, month and day by param

                    $end_date_array["year"] = getYear(getGoodString($_GET["end_date"]));
                    $end_date_array["month"] = getMonth(getGoodString($_GET["end_date"]));
                    $end_date_array["day"] = getDay(getGoodString($_GET["end_date"]));
                }
            }
        }

        $start_date = " AND YEAR(`date`)>=" . $start_date_array["year"] . " AND MONTH(`date`)>=" . $start_date_array["month"] . " AND DAY(`date`)>=" . $start_date_array["day"] . "";
        $end_date = " AND YEAR(`date`)<=" . $end_date_array["year"] . " AND MONTH(`date`)<=" . $end_date_array["month"] . " AND DAY(`date`)<=" . $end_date_array["day"] . "";

        $date_to_add = "";
        if ($start_date_bool) {
            $date_to_add = $start_date . $end_date;
        }


        $sql = "SELECT * FROM (SELECT COUNT(*) AS `validated` FROM `usage` WHERE `username`='${user_id}' ${date_to_add} AND (`type`=0 OR `type`=1 OR `type`=2)) AS `L`, (SELECT COUNT(*) AS `rejected` FROM `usage` WHERE `username`='${user_id}' ${date_to_add} AND `type`=0) AS `L1`, (SELECT COUNT(*) AS `accepted` FROM `usage` WHERE `username`='${user_id}' ${date_to_add} AND `type`=1) AS `L2`, (SELECT COUNT(*) AS `reported_listen` FROM `usage` WHERE `username`='${user_id}' ${date_to_add} AND `type`=2) AS `L3`, (SELECT COUNT(*) AS `sent` FROM `usage` WHERE  `username`='${user_id}' ${date_to_add} AND `type`=3) AS `R1`, (SELECT COUNT(*) AS `reported_speak` FROM `usage` WHERE  `username`='${user_id}' ${date_to_add} AND `type`=4) AS `R2`";
        //echo $sql;

        $response = null;

        if ($user_id != null) {
            $r = $c->query($sql);
            if ($r->num_rows > 0) {
                while ($row = $r->fetch_array()) {
                    $sub_sub_response_listen["validated"] = (int)$row["validated"];
                    $sub_sub_response_listen["accepted"] = (int)$row["accepted"];
                    $sub_sub_response_listen["rejected"] = (int)$row["rejected"];
                    $sub_sub_response_listen["reported"] = (int)$row["reported_listen"];
                    $sub_sub_response_speak["sent"] = (int)$row["sent"];
                    $sub_sub_response_speak["reported"] = (int)$row["reported_speak"];
                    $sub_response["listen"] = $sub_sub_response_listen;
                    $sub_response["speak"] = $sub_sub_response_speak;
                    $response["user-stats"] = $sub_response;
                }
            } else {
                $sub_sub_response_listen["validated"] = 0;
                $sub_sub_response_listen["accepted"] = 0;
                $sub_sub_response_listen["rejected"] = 0;
                $sub_sub_response_listen["reported"] = 0;
                $sub_sub_response_speak["sent"] = 0;
                $sub_sub_response_speak["reported"] = 0;
                $sub_response["listen"] = $sub_sub_response_listen;
                $sub_response["speak"] = $sub_sub_response_speak;
                $response["user-stats"] = $sub_response;
            }
        }
    }

    echo json_encode($response);
} else {
    echo_null();
}

function echo_null()
{
    echo json_encode(null);
}

function isAGoodDate($date)
{
    $date_tmp = explode("-", $date);
    if (count($date_tmp) == 3) {
        //if there are exactly 3 elements (year, month and day)
        if (is_numeric($date_tmp[0]) && $date_tmp[0] >= 1900 && $date_tmp[0] <= date('Y')) {
            //year
            if (is_numeric($date_tmp[1]) && $date_tmp[1] >= 1 && $date_tmp[1] <= 12) {
                //month
                if (is_numeric($date_tmp[2]) && $date_tmp[2] >= 1 && $date_tmp[2] <= 31) {
                    return true;
                }
            }
        }
    }
    return false;
}

function getYear($date)
{
    return explode("-", $date)[0];
}

function getMonth($date)
{
    return explode("-", $date)[1];
}

function getDay($date)
{
    return explode("-", $date)[2];
}

/*
 * is_in($substring, $string)
 */
function is_in($substring, $string)
{
    return false !== strpos($string, $substring);
}

?>