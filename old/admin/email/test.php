<?php
$text = "Ciao, ti ringrazio per avermi contattato 🤣<br>Se ci tieni puoi cercare vulnerabilità, certo, e poi me le segnali. <br>/ <br>Cordialmente, <br>Sav!\"#£$%&/()=[]{}èé,.-;:_ç°*§ì\'0987654321\\|+€";
$message_text = htmlspecialchars(strval($text));
$message_text = str_replace("'", "&#39;", $message_text);
$message_text = str_replace("\"", "&#34;", $message_text);
$message_text = str_replace("\\", "&#92;", $message_text);
$message_text = str_replace("\n", "<br>", $message_text);
$message_text = str_replace("è", "&#232;", $message_text);
$message_text = str_replace("é", "&#233;", $message_text);
$message_text = str_replace("à", "&#224;", $message_text);
$message_text = str_replace("ì", "&#236;", $message_text);
$message_text = str_replace("ò", "&#242;", $message_text);
$message_text = str_replace("ù", "&#249;", $message_text);
$message_text = str_replace("È", "&#200;", $message_text);
$message_text = str_replace("É", "&#201;", $message_text);
$message_text = str_replace("À", "&#192;", $message_text);
$message_text = str_replace("Ì", "&#204;", $message_text);
$message_text = str_replace("Ò", "&#210;", $message_text);
$message_text = str_replace("Ù", "&#217;", $message_text);
$pattern = "/[^0-9a-zA-Z\\\$\\%\\€\\s,.;\\#\\<\\>\\-\\'\\\"\\<\\>\\&\\/\\(\\)\\[\\]\\{\\}\\\\]/";
$message_text = preg_replace($pattern, "", $message_text);

echo "<pre>" . $message_text . "</pre>";
?>