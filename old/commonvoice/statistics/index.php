<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>CV Project &#8211; Saverio Morelli</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/commonvoice/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <script src="/old/chartjs/Chart.js"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153189423-2">
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'UA-153189423-2');
    </script>
</head>
<body>
<div id="top-bar">
    <a href="/old/commonvoice/">
        <div id="main-title">
            CV Project
        </div>
    </a>
</div>
<div class="margin-top100"></div>
<!--
<div id="proudly-basilicata" class="background-primary-color text-white-color font-family-basic">
    <script>
        document.write(
            twemoji.parse("Developed with 🤍 in Basilicata, Italy")
        );
    </script>
</div>
-->
<div class="background-darkgrey-color margin-left-minus-8 margin-right-minus-8 padding-bottom-50 padding-top-50 text-center">
    <a href="https://f-droid.org/it/packages/org.commonvoice.saverio/"><img src="/old/images/icons/f-droid.png"
                                                                            class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://play.google.com/store/apps/details?id=org.commonvoice.saverio"><img
                src="/old/images/icons/play-store.png"
                class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://github.com/Sav22999/common-voice-android"><img src="/old/images/socials/github.png"
                                                                    class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://crowdin.com/project/common-voice-android"><img src="/old/images/icons/crowdin.png"
                                                                    class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://t.me/sav_projects/6"><img src="/old/images/socials/telegram.png"
                                               class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://liberapay.com/Sav22999/"><img src="/old/images/icons/liberapay.png"
                                                   class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://www.paypal.me/saveriomorelli"><img src="/old/images/icons/paypal.png"
                                                        class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://ko-fi.com/saveriomorelli"><img src="/old/images/icons/ko-fi.png"
                                                    class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
</div>
<?php if (date("Y-m-d") >= "2021-05-21" && date("Y-m-d") <= "2021-08-01") { ?>
    <div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
        <div class="center-content padding-default">
            <script>
                document.write(
                    twemoji.parse("Do you want to receive some stickers and support me to improve the app? 😍")
                );
            </script>
            <br>
            <script>
                document.write(
                    twemoji.parse("Make a donation now and you'll get stickers! 😉")
                );
            </script>
            <div class="margin-25"></div>
            <div class="text-center">
                <a href="https://www.saveriomorelli.com/commonvoice/get-stickers/" class="just-link">
                    <button class="margin-5 font-family-basic">Learn more</button>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<div class="background-secondary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center">
    <div class="center-content text-white-color font-family-basic text-left">
        These data are approximated, because users can disable the sending of Anonymous Statistics in
        app Settings.
        <br>
        App statistics are implemented the 20th-23rd April, so before those dates aren't available.
        <br>
        <small>
            Please note that we only use statistics to see how many people use the app each day. We do not collect any
            personal data. Please consider turning statistics on.
        </small>
    </div>
</div>
<div id="body" class="font-family-basic text-black-color">
    <h1 class="no-padding no-margin h1-center">Chart</h1>
    <hr class="hr-center margin-bottom30">

    <div id="filter-statistics-cvapp" class="filter-bar filter-bar-white">
        <button onclick="location.href='./?limit=5'" id='limit-10'
                class='filter-bar-button filter-6 filter-bar-button-white <?php if (isset($_GET["limit"]) && $_GET["limit"] == "5" && is_numeric($_GET["limit"])) {
                    echo "filter-bar-button-selected";
                } ?>'>
            5
        </button>
        <button onclick="location.href='./?limit=10'" id='limit-10'
                class='filter-bar-button filter-6 filter-bar-button-white <?php if ((isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] <= 10 && $_GET["limit"] != 5) || !isset($_GET["limit"])) {
                    echo "filter-bar-button-selected";
                } ?>'>
            10
        </button>
        <button onclick="location.href='./?limit=15'" id='status-0'
                class='filter-bar-button filter-6 filter-bar-button-white <?php if (isset($_GET["limit"]) && $_GET["limit"] == "15" && is_numeric($_GET["limit"])) {
                    echo "filter-bar-button-selected";
                } ?>'>
            15
        </button>
        <button onclick="location.href='./?limit=30'" id='status-1'
                class='filter-bar-button filter-6 filter-bar-button-white <?php if (isset($_GET["limit"]) && is_numeric($_GET["limit"]) && ($_GET["limit"] == "30")) {
                    echo "filter-bar-button-selected";
                } ?>'>
            30
        </button>
        <button onclick="location.href='./?limit=60'" id='status-2'
                class='filter-bar-button filter-6 filter-bar-button-white <?php if (isset($_GET["limit"]) && is_numeric($_GET["limit"]) && ($_GET["limit"] == "60")) {
                    echo "filter-bar-button-selected";
                } ?>'>
            60
        </button>
        <button onclick="location.href='./?limit=90'" id='status-3'
                class='filter-bar-button filter-6 filter-bar-button-white <?php if (isset($_GET["limit"]) && is_numeric($_GET["limit"]) && ($_GET["limit"] == "90" || $_GET["limit"] > 90)) {
                    echo "filter-bar-button-selected";
                } ?>'>
            90
        </button>
    </div>

    <!-- This is my chart/graph -->
    <canvas id="myChart" width="800" height="350" style="position: relative;"></canvas>

    <?php
    $publicCondition = "`public` = 'true'";
    if (variables_permission_yes_or_not(10)) {
        $publicCondition = "(`public` = 'true' OR `public` = 'false')";
    }

    $labels = array();//labels
    $data = array();//date
    $languages = array();//all languages
    $dates = array();

    $limit_data = 10;
    if (isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] >= 5) {
        if ($_GET["limit"] > 90) $limit_data = 90;
        else $limit_data = $_GET["limit"];
    }
    $empty_array_limit_data_string = [];
    for ($i = 0; $i < $limit_data; $i++) {
        if ($i == 0) $empty_array_limit_data_string = "0";
        else $empty_array_limit_data_string .= ", 0";
    }

    global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api);
    $c->set_charset("utf8");
    $sql = "SELECT DISTINCT `language` FROM `statistics` ORDER BY `language` ASC";
    $r = $c->query($sql);
    if ($r->num_rows > 0) {
        while ($row = $r->fetch_assoc()) {
            array_push($languages, $row["language"]);
        }
    }

    /*
    //I get this directly in "All languages"
    $sql = "SELECT DISTINCT CAST(`date` AS DATE) AS `date` FROM `statistics` WHERE ".$publicCondition." ORDER BY CAST(`date` AS DATE) DESC LIMIT ".$limit_data;
    $r = $c->query($sql);
    if ($r->num_rows > 0) {
        while ($row = $r->fetch_assoc()) {
            array_push($dates, $row["date"]);
        }
    }
    */

    //All languages
    $sql = "SELECT COUNT(*) AS n_users, CAST(`date` AS DATE) AS `date` FROM `statistics` WHERE " . $publicCondition . " GROUP BY CAST(`date` AS DATE) ORDER BY CAST(`date` AS DATE) DESC LIMIT " . $limit_data;
    $r = $c->query($sql);
    $n = 0;
    $n_records = $r->num_rows;
    if ($n_records > 0) {
        $data[0] = "";
        $labels[0] = "";
        while ($row = $r->fetch_assoc()) {
            if ($n > 0) {
                $data[0] = $row["n_users"] . ", " . $data[0];
                $labels[0] = "'" . $row["date"] . "'" . ", " . $labels[0];
            } else {
                $data[0] = $row["n_users"];
                $labels[0] = "'" . $row["date"] . "'";
            }
            array_push($dates, $row["date"]); //Add date to $dates
            $n++;
        }
    }
    $dates = array_reverse($dates);
    //echo $data[0] . " - " . $labels[0];

    for ($x = 0; $x < sizeof($languages); $x++) {
        $data[$x + 1] = "";
        $labels[$x + 1] = "";
        for ($j = 0; $j < sizeof($dates); $j++) {
            $labels[$x + 1] .= "'" . $dates[$j] . "'";
            $sql = "SELECT COUNT(*) AS n_users FROM `statistics` WHERE " . $publicCondition . " AND CAST(`date` AS DATE) = '" . htmlspecialchars($dates[$j]) . "' AND `language` = '" . htmlspecialchars($languages[$x]) . "'";
            $r = $c->query($sql);
            if ($r->num_rows > 0) {
                $row = $r->fetch_assoc();
                $data[$x + 1] .= $row["n_users"];
            } else {
                $data[$x + 1] .= "0";
            }
            if (($j + 1) < sizeof($dates)) {
                $data[$x + 1] .= ", ";
                $labels[$x + 1] .= ", ";
            }
        }
    }
    ?>

    <h1 class="no-padding no-margin h1-center">Details</h1>
    <hr class="hr-center">
    <div class="statistics-data-table">
        <table>
            <tr id="title">
                <th class="empty-cell"></th>
                <?php
                $labels_split = explode("', '", $labels[1]);
                $labels_split[0] = substr($labels_split[0], 1);
                $labels_split[sizeof($labels_split) - 1] = substr($labels_split[sizeof($labels_split) - 1], 0, -1);
                echo '<th>Average</th>';
                for ($x = 0; $x < sizeof($labels_split); $x++) {
                    echo '<th>' . $labels_split[$x] . '</th>';
                }
                ?>
            </tr>
            <?php
            $labels_split = explode("', '", $labels[1]);
            $labels_split[0] = substr($labels_split[0], 1);
            $labels_split[sizeof($labels_split) - 1] = substr($labels_split[sizeof($labels_split) - 1], 0, -1);

            $c2 = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api);
            $c2->set_charset("utf8");
            for ($x = 0;
                 $x < sizeof($languages) + 1;
                 $x++) {
                $data_split = explode(", ", $data[$x]);
                if ($data[$x] != "" && $data[$x] != $empty_array_limit_data_string) {
                    echo '<tr>';
                    if ($x == 0) {
                        echo '<th class="th-language">All</th>';
                    } else {
                        echo '<th class="th-language">' . $languages[$x - 1] . '</th>';
                    }
                    $max_value = max($data_split);
                    $min_value = min(array_filter($data_split));
                    $sum = 0;
                    $n_elements = 0;
                    for ($y = 0; $y < sizeof($labels_split); $y++) {
                        $sql_tmp = "";
                        $details_n_users = "";
                        $n_users_to_show = $data_split[$y];
                        if (variables_permission_yes_or_not(10)) {
                            if ($x == 0) $sql_tmp = "SELECT * FROM `statistics` WHERE public='false' AND CAST(`date` AS DATE) = '" . $labels_split[$y] . "'";
                            else $sql_tmp = "SELECT * FROM `statistics` WHERE public='false' AND CAST(`date` AS DATE) = '" . $labels_split[$y] . "' AND language='" . $languages[$x - 1] . "'";

                            if ($r2 = $c2->query($sql_tmp)) {
                                if ($r2->num_rows > 0) {
                                    $n_users_hide = $r2->num_rows;
                                    $n_users_to_show = $data_split[$y] - $n_users_hide;
                                    $details_n_users = " (+" . $n_users_hide . ")";
                                }
                            }
                        }
                        $sum += $n_users_to_show;
                        $n_elements++;
                    }

                    $avg = $sum / $n_elements;
                    echo '<td>' . round($avg) . '</td>';

                    for ($y = 0; $y < sizeof($labels_split); $y++) {
                        $sql_tmp = "";
                        $details_n_users = "";
                        $n_users_to_show = $data_split[$y];
                        if (variables_permission_yes_or_not(10)) {
                            if ($x == 0) $sql_tmp = "SELECT * FROM `statistics` WHERE public='false' AND CAST(`date` AS DATE) = '" . $labels_split[$y] . "'";
                            else $sql_tmp = "SELECT * FROM `statistics` WHERE public='false' AND CAST(`date` AS DATE) = '" . $labels_split[$y] . "' AND language='" . $languages[$x - 1] . "'";

                            if ($r2 = $c2->query($sql_tmp)) {
                                if ($r2->num_rows > 0) {
                                    $n_users_hide = $r2->num_rows;
                                    $n_users_to_show = $data_split[$y] - $n_users_hide;
                                    $details_n_users = " (+" . $n_users_hide . ")";
                                }
                            }
                        }
                        $color_red_green = "";
                        if ($max_value > 0 && $data_split[$y] == $max_value)
                            $color_red_green = 'class="text-green-color text-bold"';
                        else if ($min_value > 0 && $data_split[$y] == $min_value)
                            $color_red_green = 'class="text-red-color text-bold"';
                        echo '<td ' . $color_red_green . '>' . $n_users_to_show . $details_n_users . '</td>';
                        $sum += $n_users_to_show;
                        $n_elements++;
                    }
                    echo '</tr>';
                }
            }
            $c2->close();
            ?>
        </table>
    </div>

    <?php if (variables_permission_yes_or_not(10)) { ?>
        <h1 class="no-padding no-margin h1-center">Details (admin)</h1>
        <hr class="hr-center">
        <div id="filter-statistics-admin-cvapp" class="filter-bar filter-bar-white">
            <button onclick="location.href='./?filter=today'" id='filter-today'
                    class='filter-bar-button filter-3 filter-bar-button-white <?php if ((isset($_GET["filter"]) && $_GET["filter"] == "today") || !isset($_GET["filter"])) {
                        echo "filter-bar-button-selected";
                    } ?>'>
                Today
            </button>
            <button onclick="location.href='./?filter=yesterday'" id='filter-yesterday'
                    class='filter-bar-button filter-3 filter-bar-button-white <?php if (isset($_GET["filter"]) && $_GET["filter"] == "yesterday") {
                        echo "filter-bar-button-selected";
                    } ?>'>
                Yesterday
            </button>
            <button onclick="location.href='./?filter=ever'" id='filter-ever'
                    class='filter-bar-button filter-3 filter-bar-button-white <?php if (isset($_GET["filter"]) && $_GET["filter"] == "ever") {
                        echo "filter-bar-button-selected";
                    } ?>'>
                Ever
            </button>
        </div>
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>Id</th>
                    <th>Date</th>
                    <th>Logged</th>
                    <th>New user</th>
                    <th>Username</th>
                    <th>Language</th>
                    <th>Version</th>
                    <th>Statistics</th>
                    <th>Store</th>
                </tr>
                <?php
                $n_users_by_stores = array("gps" => 0, "fdgh" => 0, "hag" => 0, "aas" => 0, "other" => 0);
                $n_users_by_new = array("new" => 0, "old" => 0);
                $n_users_by_logged = array("logged" => 0, "anonymous" => 0);
                $n_users_by_version = array();

                $stores = array("gps" => "Google Play", "fdgh" => "F-Droid / GitHub", "hag" => "Huawei AppGallery", "aas" => "Amazon AppStore", "other" => "Other");

                $date_to_use = date("Y-m-d");
                $where_clause = "WHERE CAST(`date` AS DATE) = '" . $date_to_use . "'";
                $limit = "";
                if (isset($_GET["filter"])) {
                    if ($_GET["filter"] == "yesterday") {
                        $date_to_use = date("Y-m-d", strtotime("-1 days"));
                        $where_clause = "WHERE CAST(`date` AS DATE) = '" . $date_to_use . "'";
                    } else if ($_GET["filter"] == "ever") {
                        $where_clause = "";
                        //$limit = " LIMIT 200";
                    }
                }

                $sql = "SELECT * FROM `statistics` " . $where_clause . " ORDER BY `date` DESC" . $limit;
                $c2 = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api);
                $c2->set_charset("utf8");
                if ($r = $c->query($sql)) {
                    if ($r->num_rows > 0) {
                        while ($row = $r->fetch_assoc()) {
                            $sql_tmp = "SELECT * FROM `statistics` WHERE `username`='" . $row["username"] . "'";
                            $new_user = "yes";
                            $class_details_new_user = "class='text-green-color text-bold'";
                            if ($r2 = $c2->query($sql_tmp)) {
                                if ($r2->num_rows > 1) {
                                    $new_user = "no";
                                    $class_details_new_user = "class='text-red-color text-bold'";
                                }
                            }
                            if ($new_user == "yes") {
                                $n_users_by_new["new"]++;
                            } else {
                                $n_users_by_new["old"]++;
                            }
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["date"] . "</td>";
                            $logged_yes_no = "no";
                            $class_details_logged = "class='text-red-color text-bold'";
                            if ($row["logged"] == 1) {
                                $logged_yes_no = "yes";
                                $class_details_logged = "class='text-green-color text-bold'";
                                $n_users_by_logged["logged"]++;
                            } else {
                                $n_users_by_logged["anonymous"]++;
                            }
                            echo "<td " . $class_details_logged . ">" . $logged_yes_no . "</td>";
                            echo "<td " . $class_details_new_user . ">" . $new_user . "</td>";
                            echo "<td>" . $row["username"] . "</td>";
                            echo "<td>" . $row["language"] . "</td>";
                            if (isset($n_users_by_version[$row["version"]])) $n_users_by_version[$row["version"]]++;
                            else $n_users_by_version[$row["version"]] = 1;
                            echo "<td>" . $row["version"] . "</td>";
                            $public_activated_disabled = "activated";
                            $class_details_public = "class='text-green-color text-bold'";
                            if ($row["public"] == "false") {
                                $public_activated_disabled = "disabled";
                                $class_details_public = "class='text-red-color text-bold'";
                            }
                            echo "<td " . $class_details_public . ">" . $public_activated_disabled . "</td>";
                            $store_source = "F-Droid / GitHub";
                            if ($row["source"] == "GPS") {
                                $store_source = "Google Play";
                                $n_users_by_stores["gps"]++;
                            } else if ($row["source"] == "HAG") {
                                $store_source = "Huawei AppGalley";
                                $n_users_by_stores["hag"]++;
                            } else if ($row["source"] == "AAS") {
                                $store_source = "Amazon AppStore";
                                $n_users_by_stores["aas"]++;
                            } else if ($row["source"] == "FDGH" || $row["source"] == "FD-GH") {
                                $store_source = "F-Droid / GitHub";
                                $n_users_by_stores["fdgh"]++;
                            } else if ($row["source"] != "FDGH" && $row["source"] != "FD-GH") {
                                $store_source = "Other";
                                $n_users_by_stores["other"]++;
                            }
                            echo "<td>" . $store_source . "</td>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Today there is no data to show.</td></tr>";
                    }
                }
                $c2->close();
                arsort($n_users_by_stores);
                arsort($n_users_by_version);
                ?>
            </table>
        </div>
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>Store</th>
                    <th>Users</th>
                </tr>
                <?php
                $tot_users_stores = 0;
                foreach ($n_users_by_stores as $key => $val) {
                    echo '<tr>';
                    echo '<td>' . $stores[$key] . '</td>';
                    echo '<td>' . $val . '</td>';
                    echo '</tr>';
                    $tot_users_stores += $val;
                }
                ?>
                <tr>
                    <td>All stores</td>
                    <td><?php echo $tot_users_stores; ?></td>
                </tr>
            </table>
        </div>
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>New users</th>
                    <th>Old users</th>
                    <th>Total users</th>
                </tr>
                <tr>
                    <td><?php echo $n_users_by_new["new"]; ?></td>
                    <td><?php echo $n_users_by_new["old"]; ?></td>
                    <td><?php echo($n_users_by_new["new"] + $n_users_by_new["old"]); ?></td>
                </tr>
            </table>
        </div>
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>Logged users</th>
                    <th>Anonymous users</th>
                    <th>Total users</th>
                </tr>
                <tr>
                    <td><?php echo $n_users_by_logged["logged"]; ?></td>
                    <td><?php echo $n_users_by_logged["anonymous"]; ?></td>
                    <td><?php echo($n_users_by_logged["logged"] + $n_users_by_logged["anonymous"]); ?></td>
                </tr>
            </table>
        </div>
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>Version code</th>
                    <th>Users</th>
                </tr>
                <?php
                foreach ($n_users_by_version as $key => $val) {
                    echo '<tr>';
                    echo '<td>' . $key . '</td>';
                    echo '<td>' . $val . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    <?php } ?>

    <script>
        var chart = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(chart, {
            type: 'line',
            data: {
                labels: [<?php echo $labels[0]; ?>],
                datasets: [{
                    data: [<?php echo $data[0]; ?>],
                    label: "All languages",
                    borderColor: "rgba(51, 153, 255, 1)",
                    backgroundColor: "rgba(51, 153, 255, 0.2)",
                    fill: "start"
                }
                    <?php
                    for ($x = 0; $x < sizeof($languages); $x++) {
                        $rndC1 = rand(0, 255);
                        $rndC2 = rand(0, 255);
                        $rndC3 = rand(0, 255);
                        if ($data[$x + 1] != "" && $data[$x + 1] != $empty_array_limit_data_string) {
                            echo ',
                            {
                                data: [' . $data[$x + 1] . '],
                                label: "' . $languages[$x] . '",
                                borderColor: "rgba(' . $rndC1 . ', ' . $rndC2 . ', ' . $rndC3 . ', 1)",
                                fill: "false"
                            }
                            ';
                        }
                    }
                    ?>
                ]
            },
            options: {
                responsive: true,
                elements: {
                    line: {
                        tension: 0
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0,
                            stepSize: 1
                        }
                    }]
                }
            },
        });
    </script>
</div>
<div id="developed-by" class="background-black-color text-white-color font-family-basic">
    This app is developed by <a href="/old/" class="text-lightblue-color-hover">Saverio Morelli</a>
</div>
</body>
</html>
<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->