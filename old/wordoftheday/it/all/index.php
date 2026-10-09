<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
    global $path_wordoftheday;
    ?>
    <title>Word of the Day [it] &#8211; All words</title>
    <link rel="stylesheet" href="/old/wordoftheday/style.css"/>
    <link rel="icon" href="/old/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/wordoftheday/script.js"></script>
</head>
<body>
<div id="fullscreen-inserting-word" class="fullscreen-message pop-up-display-message">
    Inserting the word...
</div>
<main class="padding-10 border-box">
    <?php
    global $authorised;
    if (variables_permission_yes_or_not(8)) {
        /*$full_year = date("Y");
        $full_month = date("F");
        $month = date("m");
        $full_day = date("d");

        $date_to_show = getLatestDate2("en");

        $word = get_xml_from_url("https://en.wiktionary.org/w/index.php?title=Wiktionary:Word_of_the_day/${date_to_show}");
        $word_obj = new SimpleXMLElement($word);

        $description = $word;*/

        ?>
        <div id="duplicated-words">
            <?php
            global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
            $c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api);
            $c->set_charset("utf8");
            echo "<b>Duplicates:</b><br>";
            $fieldToCheck = "date";
            $sql = "SELECT * FROM `wordoftheday_it` WHERE $fieldToCheck IN (SELECT $fieldToCheck FROM `wordoftheday_it` GROUP BY $fieldToCheck HAVING COUNT(*) > 1)";

            $result = $c->query($sql);

            if ($result->num_rows > 0) {
                $last_date = "";
                $color = "#eeeeee";
                while ($row = $result->fetch_assoc()) {
                    $br_or_not = "";
                    if ($last_date !== "" && $last_date !== $row["date"]) {
                        $br_or_not = "<br>";
                        if ($color === "#eeeeee") {
                            $color = "#dddddd";
                        } else if ($color === "#aaaaaa") {
                            $color = "#eeeeee";
                        } else if ($color === "#ffffff") {
                            $color = "#aaaaaa";
                        } else {
                            $color = "#ffffff";
                        }
                    }
                    $last_date = $row["date"];
                    echo "<div class='last-words word-duplicated-date-" . $row["date"] . "' id='word-duplicated-id-" . $row["id"] . "' style='background-color: $color'>• [" . $row["id"] . "] " . $row["date"] . " –  " . " <input type='submit' value='Fix' class='button submit no-margin padding-2-10' onclick='fixDate(" . $row["id"] . ", \"" . $row["date"] . "\", \"it\")'/>" . $row["word"] . "</div>";
                }
            } else {
                // No duplicates found
                echo "<span class='last-words'>• No duplicates found</span><br>";
            }
            ?>
        </div>
        <div id="last-inserted-words">
            <?php
            $sql = "SELECT `word`, `date` FROM `wordoftheday_it` ORDER BY `date` DESC LIMIT 100";
            if ($r = $c->query($sql)) {
                if ($r->num_rows > 0) {
                    echo "<b>Last 100 words:</b><br>";
                    $array = array();
                    while ($row = $r->fetch_array()) {
                        echo "<span class='last-words'>• " . $row["date"] . " – " . $row["word"] . "</span><br>";
                    }
                }
            }
            $c->close();
            ?>
        </div>
        <?php
    } else {
        echo("<div class='message'>You don't have enough permissions to see this page.</div>");
    }
    ?>
</main>

<?php show_admin_bar(); ?>
</body>
</html>
