<?php
//the following credentials are used for: mysqli(localhost, username, password, database)
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/api/emoticolor/api-functions.php");

echo generateOtpCode();
?>