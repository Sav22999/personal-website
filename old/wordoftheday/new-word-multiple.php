<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api)) {
    $c->set_charset("utf8mb4");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["word"]) && isset($post["definition"]) && isset($post["etymology"]) && isset($post["type"]) && isset($post["phonetics"]) && isset($post["language"]) && isSupportedLanguage(getGoodString($post["language"]));
    if (variables_permission_yes_or_not(8) && $condition) {
        $word = getGoodString($post["word"]);
        //$date = getGoodString($post["date"]);
        $date = "";
        $definition = getGoodString($post["definition"]);
        $etymology = getGoodString($post["etymology"]);
        $type = getGoodString($post["type"]);
        //$phonetics = getGoodString($post["phonetics"]);
        $phonetics = $post["phonetics"];
        $phonetics = str_replace("/", "", $phonetics);
        $language = getGoodString($post["language"]);
        $source = getGoodString($post["source"]);

        //check word

        //Using prepared statements -> it's the safest way for MySQL queries
        /*$sql = "SELECT `word` FROM `wordoftheday_${language}` WHERE `word`='${word}'";
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 0) {

                try {
                    $c->begin_transaction();

                    //lock table
                    $c->query("LOCK TABLES `wordoftheday_${language}` READ, `wordoftheday_${language}` WRITE");

                    //get latest date
                    $sql = "SELECT `date` FROM `wordoftheday_${language}` ORDER BY `date` DESC LIMIT 1";

                    if ($r = $c->query($sql)) {
                        if ($r->num_rows == 1) {
                            $date_to_return = date('Y-m-d', strtotime(date("h:i:s") . ' +0 day'));
                            $row = $r->fetch_assoc();
                            $date_temp = $row["date"];
                            if ($date_temp >= $date_to_return) {
                                $date_to_return = date('Y-m-d', strtotime($date_temp . ' +1 day'));
                            }
                            $date = $date_to_return;

                            $sql = "INSERT INTO `wordoftheday_${language}`(`id`,`added_date`, `word`,`date`,`definition`,`type`,`phonetics`,`etymology`,`source`) VALUES(NULL, NULL, '${word}','${date}','${definition}','${type}','${phonetics}','${etymology}','${source}')";

                            if (!$c->query($sql)) {
                                response(500, "Error", "Can't insert the record");
                                return;
                            }
                        } else {
                            response(501, "Error", "Date wrong inserted");
                            return;
                        }
                    } else {
                        response(502, "Error", "Can't get the record from the table");
                        return;
                    }
                    //Unlock table
                    $c->query("UNLOCK TABLES");

                    $c->commit();
                } catch (Exception $e) {
                    // An error occurred, rollback the transaction
                    $c->rollback();

                    response(505, "Error", "Transaction failed!");
                    return;
                }
            } else {
                response(503, "Error", "Word already exists");
                return;
            }
        } else {
            response(504, "Error", "Can't get the record from the table");
            return;
        }
        response(200, "OK", "Word inserted in the database");*/

        $sql = "SELECT `word` FROM `wordoftheday_${language}` WHERE `word`=?";
        $stmt = $c->prepare($sql);
        $stmt->bind_param("s", $word);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $stmt->close();
                $c->begin_transaction();
                $c->query("LOCK TABLES `wordoftheday_${language}` READ, `wordoftheday_${language}` WRITE");
                $sql = "SELECT `date` FROM `wordoftheday_${language}` ORDER BY `date` DESC LIMIT 1";
                $stmt = $c->prepare($sql);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    $date_num_rows = $stmt->num_rows;
                    if ($date_num_rows == 1 || $date_num_rows == 0) {
                        $stmt->bind_result($date_temp);
                        $stmt->fetch();
                        $date_to_return = date('Y-m-d', strtotime(date("h:i:s") . ' +0 day'));
                        if ($date_num_rows > 0 && $date_temp >= $date_to_return) {
                            $date_to_return = date('Y-m-d', strtotime($date_temp . ' +1 day'));
                        }
                        $date = $date_to_return;
                        $stmt->close();
                        $sql = "INSERT INTO `wordoftheday_${language}`(`id`,`added_date`, `word`,`date`,`definition`,`type`,`phonetics`,`etymology`,`source`) VALUES(NULL, NULL, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $c->prepare($sql);
                        if ($stmt) {
                            $stmt->bind_param("sssssss", $word, $date, $definition, $type, $phonetics, $etymology, $source);
                            if ($stmt->execute()) {
                                $stmt->close();
                                $c->query("UNLOCK TABLES");
                                $c->commit();
                                response(200, "OK", "Word inserted in the database");
                            } else {
                                $stmt->close();
                                $c->query("UNLOCK TABLES");
                                $c->rollback();
                                response(505, "Error", "Can't insert the record");
                            }
                        } else {
                            $error = $c->error;
                            $c->query("UNLOCK TABLES");
                            $c->rollback();
                            response(506, "Error", "Can't prepare the statement.\n" . $error);
                        }
                    } else {
                        $stmt->close();
                        $c->query("UNLOCK TABLES");
                        $c->rollback();
                        response(501, "Error", "Date wrong inserted");
                    }
                } else {
                    $stmt->close();
                    $c->query("UNLOCK TABLES");
                    $c->rollback();
                    response(502, "Error", "Can't get the record from the table");
                }
            } else {
                $stmt->close();
                response(503, "Error", "Word already exists");
            }
        } else {
            $stmt->close();
            response(504, "Error", "Can't get the record from the table");
        }
    } else {
        if (!variables_permission_yes_or_not(10)) {
            global $your_privileges;
            response(401, "Error", "You don't have enough privileges to add word. Your privileges: " . $your_privileges);
        } else if (!$condition) {
            response(400, "Error", "Parameters are not enough. Check all required fields.");
        } else {
            response(402, "Error", "Something was wrong in POST request");
        }
    }
    $c->close();
} else {
    response(505, "Error", "Can't connect to the database");
}

function response($response_code, $response_status, $response_description)
{
    $response['code'] = $response_code;
    $response['status'] = $response_status;
    $response['description'] = $response_description;

    http_response_code($response_code);

    $json_response = json_encode($response);
    echo $json_response;
}

function isAGoodDate($date)
{
    $date_tmp = explode("-", $date);
    if (count($date_tmp) == 3) {
        //if there are exactly 3 elements (year, month and day)
        if (is_numeric($date_tmp[0]) && $date_tmp[0] >= 1900 && $date_tmp[0] <= (date('Y') + 2)) {
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

function isSupportedLanguage($lang)
{
    return ($lang === "it" || $lang === "en" || $lang === "fr");
}

?>