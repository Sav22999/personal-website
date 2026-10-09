<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_commonvoice_api, $path;
header("Content-Type:application/json");

$response = null;
$response["last-update"] = date("Y-m-d H:i:s", filemtime($path . "/include/variables.php"));

foreach (get_supported_languages_with_full_native_name() as $language_code => $details) {
    $response_language["english"] = $details["english"];
    $response_language["native"] = $details["native"];
    $response_language["percentage"] = (int)$details["percentage"];
    $response_language["crowdin"] = (bool)$details["crowdin"]; //0->no, it's not supported in Crowdin | 1->yes

    $response["languages"][$language_code] = $response_language;
}
$json_response = json_encode($response);
echo $json_response;
?>