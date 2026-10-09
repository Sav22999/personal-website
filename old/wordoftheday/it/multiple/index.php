<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
    global $path_wordoftheday;
    ?>
    <title>Word of the Day [it] &#8211; New word</title>
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
        <div id="last-inserted-words">
            <?php
            global $localhost_db, $username_db, $password_db, $database_wordoftheday_api;
            $c = new mysqli($localhost_db, $username_db, $password_db, $database_wordoftheday_api);
            $c->set_charset("utf8");
            $sql = "SELECT `word` FROM `wordoftheday_it` ORDER BY `added_date` DESC LIMIT 3";
            if ($r = $c->query($sql)) {
                if ($r->num_rows > 0) {
                    echo "<b>Last inserted words:</b><br>";
                    $array = array();
                    while ($row = $r->fetch_array()) {
                        array_push($array, $row["word"]);
                    }
                    $array = array_reverse($array);
                    foreach ($array as $item) {
                        if ($item != "") {
                            echo "• <span class='last-words'>" . $item . "</span><br>";
                        }
                    }
                }
            }
            $c->close();
            ?>
        </div>
        <?php
        ?>

        <h1>New word [Multiple] - Word of the Day: Italiano</h1>
        <div class="main-text">
            <textarea placeholder="JSON es. [{word: ...}, ...]" class="textarea width-100-perc height-300"
                      id="json-text"></textarea>
        </div>
        <div class="main-buttons text-right">
            <input type="submit" value="Add" class="button submit no-margin-bottom no-margin-right"
                   onclick="insertMultiple('it')"/>
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
