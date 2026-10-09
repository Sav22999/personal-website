<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/wordoftheday/include/variables.php");
    global $path_wordoftheday;
    ?>
    <title>Word of the Day &#8211; New word</title>
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
            $sql = "SELECT `word` FROM `wordoftheday_en` ORDER BY `added_date` DESC LIMIT 3";
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

        <div id="element-to-hide" style="display: none"><?php echo $description; ?></div>

        <h1>New word - Word of the Day: English</h1>
        <div class="main-text">
            <input type="text" placeholder="Word *" class="textbox width-100-perc"
                   id="word-text" oncontextmenu="return false;" autofocus/>
            <input type="date" placeholder="Date *" class="textbox width-100-perc"
                   id="date-text" oncontextmenu="return false;" value="<?php echo getLatestDate("en"); ?>"/>
            <select class="textbox width-100-perc lowercase"
                    id="type-text" oncontextmenu="return false;">
                <option value="">- Select -</option>
                <option value="noun">Noun</option>
                <option value="verb">Verb</option>
                <option value="adjective">Adjective</option>
                <option value="adverb">Adverb</option>
                <option value="preposition">Preposition</option>
                <option value="phrasal verb">Phrasal verb</option>
                <option value="conjunction">Conjunction</option>
                <option value="interjection">Interjection</option>
                <option value="prepositional phrase">Prepositional phrase</option>
            </select>
            <input type="text" placeholder="Phonetics *" class="textbox width-100-perc"
                   id="phonetics-text" oncontextmenu="return false;"/>
            <textarea placeholder="Definition *" class="textarea width-100-perc height-100"
                      id="definition-text" oncontextmenu="return false;"></textarea>
            <textarea placeholder="Etymology / Origin" class="textarea width-100-perc height-100"
                      id="etymology-text" oncontextmenu="return false;"></textarea>
            <input type="text" placeholder="Source" class="textbox width-100-perc"
                   id="source-text" list="source-text-suggestions" oncontextmenu="return false;"/>

            <datalist id="source-text-suggestions">
                <option value="Wiktionary">
            </datalist>
        </div>
        <div class="main-buttons text-right">
            <input type="submit" value="Add" class="button submit no-margin-bottom no-margin-right"
                   onclick="insert('en')"/>
        </div>

        <script>
            /*
            let word = $("#WOTD-rss-title").html();
            if (word != null && word != "") {
                $("#word-text").val(word.toLowerCase());
            }
            */
            $("#source-text").val("Wiktionary");
            //$("#element-to-hide").html("<?php echo $date_to_show; ?> |  <a target='_blank' href='https://en.wiktionary.org/wiki/" + word.replaceAll(" ", "_") + "#English'>See on Wiktionary</a>");
            loadNewWord(new Date("<?php echo getLatestDate("en"); ?>"));
        </script>

        <?php
    } else {
        echo("<div class='message'>You don't have enough permissions to see this page.</div>");
    }

    function get_xml_from_url($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $xmlstr = curl_exec($ch);
        curl_close($ch);

        return $xmlstr;
    }

    ?>
</main>

<?php show_admin_bar(); ?>
</body>
</html>
