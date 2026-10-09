<?php
session_start();
session_regenerate_id();
$path_wordoftheday = $_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/";

include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");

function getLatestDate($language)
{
    $date_to_return = date('Y-m-d', strtotime(date("h:i:s") . ' +0 day'));
    global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api)) {
        $c->set_charset("utf8");

        $sql = "SELECT `date` FROM `wordoftheday_${language}` ORDER BY `date` DESC LIMIT 1";

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();
                $date_temp = $row["date"];
                if ($date_temp >= $date_to_return) {
                    $date_to_return = date('Y-m-d', strtotime($date_temp . ' +1 day'));
                }
            }
        }
        $c->close();
    }
    return $date_to_return;
}

function getLatestDate2($language)
{
    $date_to_return = date('Y-m-d', strtotime(date("h:i:s") . ' +0 day'));
    global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api)) {
        $c->set_charset("utf8");

        $sql = "SELECT `date` FROM `wordoftheday_${language}` ORDER BY `date` DESC LIMIT 1";

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();
                $date_temp = $row["date"];
                if ($date_temp >= $date_to_return) {
                    $date_to_return = date('Y/F_j', strtotime($date_temp . ' +1 day'));
                }
            }
        }
        $c->close();
    }
    return $date_to_return;
}

?>