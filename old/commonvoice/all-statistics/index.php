<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>CV Project &#8211; Saverio Morelli</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/commonvoice/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>
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
<?php if (variables_permission_yes_or_not(10)) { ?>
<br>
<h1 class="no-padding no-margin h1-center text-primary-color margin-top-10">The latest 10'000 entries</h1>
<hr class="hr-center">
<div class="text-center">
    <button class="margin-5 font-family-basic" onclick="downloadAsJSON()">Download as JSON the latest 10'000 entries
    </button>
</div>
<div class="statistics-data-table">
    <table>
        <tr id="title">
            <th>Id</th>
            <th>Date</th>
            <th>Type</th>
            <th>Logged</th>
            <th>Language</th>
            <th>Version</th>
            <th>Source</th>
            <th>Username</th>
            <th>Offline</th>
            <th>Sentece_id</th>
            <th>Clip_id</th>
            <th>Details</th>
        </tr>
        <tr>
            <td colspan='12' id="td-loading">Loading …</td>
        </tr>
        <?php
        global $localhost_db, $username_db, $password_db, $database_commonvoice_api;
        $sql = "SELECT * FROM `usage` ORDER BY `date` DESC LIMIT 10000";
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_commonvoice_api);
        $c->set_charset("utf8");
        $json_to_download = "[";
        if ($r = $c->query($sql)) {
            $index = 0;
            if ($r->num_rows > 0) {
                while ($row = $r->fetch_assoc()) {
                    $index++;
                    echo "<script>document.getElementById('td-loading').style.display='none';</script>";
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["logged"] . "</td>";
                    echo "<td>" . $row["language"] . "</td>";
                    echo "<td>" . $row["version"] . "</td>";
                    echo "<td>" . $row["source"] . "</td>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["offline"] . "</td>";
                    if ($row["sentence_id"] != "") {
                        echo "<td class='text-center'><div class='background-secondary-color border-radius-default width-100-perc height30' title='" . $row["sentence_id"] . "'>sentence_id</div></td>";
                    } else {
                        echo "<td></td>";
                    }
                    if ($row["clip_id"] != "") {
                        echo "<td class='text-center'><div class='background-primary-color border-radius-default width-100-perc height30' title='" . $row["clip_id"] . "'>clip_id</div></td>";
                    } else {
                        echo "<td></td>";
                    }
                    if ($row["details"]) {
                        echo "<td class='text-center'><div class='background-red-color border-radius-default width-100-perc height30' title='" . $row["details"] . "'>details</div></td>";
                    } else {
                        echo "<td></td>";
                    }
                    //echo "<td class='text-left'>" . str_replace("\n", "<br>", $row["details"]) . "</td>";
                    echo "</tr>";

                    $json_to_download .= "{";
                    $json_to_download .= "\"id\":\"" . $row["id"] . "\", ";
                    $json_to_download .= "\"date\":\"" . $row["date"] . "\", ";
                    $json_to_download .= "\"type\":\"" . $row["type"] . "\", ";
                    $json_to_download .= "\"logged\":\"" . $row["logged"] . "\", ";
                    $json_to_download .= "\"language\":\"" . $row["language"] . "\", ";
                    $json_to_download .= "\"version\":\"" . $row["version"] . "\", ";
                    $json_to_download .= "\"source\":\"" . $row["source"] . "\", ";
                    $json_to_download .= "\"username\":\"" . $row["username"] . "\", ";
                    $json_to_download .= "\"offline\":\"" . $row["offline"] . "\", ";
                    $json_to_download .= "\"sentence_id\":\"" . $row["sentence_id"] . "\", ";
                    $json_to_download .= "\"clip_id\":\"" . $row["clip_id"] . "\", ";
                    $json_to_download .= "\"details\":\"" . str_replace("\n", " | ", str_replace("\"", "'", $row["details"])) . "\"";
                    $json_to_download .= "}";
                    if ($index < $r->num_rows) $json_to_download .= ",\n";
                }
            } else {
                echo "<tr><td colspan='12'>No data found.</td></tr>";
            }
        }
        $c->close();
        $json_to_download .= "]";
        }
        ?>
    </table>

    <textarea class="hidden" id="json-text-to-download"><?php echo $json_to_download; ?></textarea>

    <script>
        function downloadAsJSON() {
            let data = document.getElementById("json-text-to-download").value;
            let file = 'usage-' + Date.now() + '.json';

            let link = document.createElement('a');
            link.download = file;
            let blob = new Blob(['' + data + ''], {
                type: 'application/json'
            });
            link.href = URL.createObjectURL(blob);
            link.click();
            URL.revokeObjectURL(link.href);
        }
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