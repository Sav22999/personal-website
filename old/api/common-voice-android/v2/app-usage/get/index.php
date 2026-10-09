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

    $date_disabled = false;
    $year_disabled = false;
    $start_end_date_bool = false;
    $start_date = array();
    $end_date = array();
    if (isset($_GET["filter"])) {
        if ($_GET["filter"] == "always") {
            $date_disabled = true;
            $year_disabled = true;
        } else if ($_GET["filter"] == "yesterday") {
            $new_date_yesterday = date('Y-m-d', strtotime(("${year}-${month}-${day}") . ' -1 day'));
            $new_date_yesterday_parts = explode("-", $new_date_yesterday);
            $year = $new_date_yesterday_parts[0];
            $month = $new_date_yesterday_parts[1];
            $day = $new_date_yesterday_parts[2];
        } else if ($_GET["filter"] == "year" && isset($_GET["year"]) && is_numeric($_GET["year"])) {
            $date_disabled = true;
            $year_disabled = false;
            $year = $_GET["year"];
        } else if ($_GET["filter"] == "date") {
            if (isset($_GET["start_date"]) && isAGoodDate(getGoodString($_GET["start_date"]))) {
                $start_end_date_bool = true;
                $start_date["d"] = getDay(getGoodString($_GET["start_date"]));
                $start_date["m"] = getMonth(getGoodString($_GET["start_date"]));
                $start_date["y"] = getYear(getGoodString($_GET["start_date"]));

                $end_date["d"] = $start_date["d"];
                $end_date["m"] = $start_date["m"];
                $end_date["y"] = $start_date["y"];

                if (isset($_GET["end_date"]) && isAGoodDate(getGoodString($_GET["end_date"]))) {
                    $end_date["d"] = getDay(getGoodString($_GET["end_date"]));
                    $end_date["m"] = getMonth(getGoodString($_GET["end_date"]));
                    $end_date["y"] = getYear(getGoodString($_GET["end_date"]));
                }
            }
        }
    }

    $today_or_yesterday_date = "YEAR(`date`)=" . $year . " AND MONTH(`date`)=" . $month . " AND DAY(`date`)=" . $day . " AND ";
    $without_date = "";
    $year_date = "YEAR(`date`)=" . $year . " AND ";
    $start_end_date = "YEAR(`date`)>=" . $start_date["y"] . " AND MONTH(`date`)>=" . $start_date["m"] . " AND DAY(`date`)>=" . $start_date["d"] . " AND YEAR(`date`)<=" . $end_date["y"] . " AND MONTH(`date`)<=" . $end_date["m"] . " AND DAY(`date`)<=" . $end_date["d"] . " AND ";

    $language_sql = "`language`='{{*{{language}}*}}' AND ";

    $sql = "SELECT * FROM (SELECT COUNT(*) AS `validated` FROM `usage` WHERE {{*{{date}}*}}{{*{{language_sql}}*}}(`type`=0 OR `type`=1 OR `type`=2)) AS `L`, (SELECT COUNT(*) AS `rejected` FROM `usage` WHERE {{*{{date}}*}}{{*{{language_sql}}*}}`type`=0) AS `L1`, (SELECT COUNT(*) AS `accepted` FROM `usage` WHERE {{*{{date}}*}}{{*{{language_sql}}*}}`type`=1) AS `L2`, (SELECT COUNT(*) AS `reported_listen` FROM `usage` WHERE {{*{{date}}*}}{{*{{language_sql}}*}}`type`=2) AS `L3`, (SELECT COUNT(*) AS `sent` FROM `usage` WHERE {{*{{date}}*}}{{*{{language_sql}}*}}`type`=3) AS `R1`, (SELECT COUNT(*) AS `reported_speak` FROM `usage` WHERE {{*{{date}}*}}{{*{{language_sql}}*}}`type`=4) AS `R2`";
    if ($date_disabled && $year_disabled && !$start_end_date_bool) {
        //without date
        $sql = str_replace("{{*{{date}}*}}", $without_date, $sql);
    } else if ($date_disabled && !$year_disabled && !$start_end_date_bool) {
        //just with year
        $sql = str_replace("{{*{{date}}*}}", $year_date, $sql);
    } else if (!$date_disabled && !$year_disabled && $start_end_date_bool) {
        //specified the date (start_date, and in case end_date)
        $sql = str_replace("{{*{{date}}*}}", $start_end_date, $sql);
    } else {
        //today date OR yesterday date
        $sql = str_replace("{{*{{date}}*}}", $today_or_yesterday_date, $sql);
    }

    $response = null;

    if ($language == "null" || check_language($language)) {
        if ($language == "null") {
            //"all"
            $sql_tmp = str_replace("{{*{{language_sql}}*}}", "", $sql);
            $r = $c->query($sql_tmp);
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
                    $response["all"] = $sub_response;
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
                $response["all"] = $sub_response;
            }

            /*
            //each language
            $sql = str_replace("{{*{{language_sql}}*}}", $language_sql, $sql);
            foreach (get_supported_languages() as $language_tmp) {
                $sql_tmp = str_replace("{{*{{language}}*}}", $language_tmp, $sql);
                $sql_tmp = str_replace("{{*{{language}}*}}", $language_tmp, $sql);
                $r = $c->query($sql_tmp);
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
                        $response[$language_tmp] = $sub_response;
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
                    $response[$language_tmp] = $sub_response;
                }
            }
            */
        } else {
            $sql = str_replace("{{*{{language_sql}}*}}", $language_sql, $sql);
            $sql_tmp = str_replace("{{*{{language}}*}}", $language, $sql);
            $r = $c->query($sql_tmp);
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
                    $response[$language] = $sub_response;
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
                $response[$language] = $sub_response;
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

?>