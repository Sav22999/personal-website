<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/savmrl/include/variables.php");
    global $path_savmrl;
    ?>
    <title>savmrl.it &#8211; Last links inserted</title>
    <link rel="stylesheet" href="/old/savmrl/style.css"/>
    <link rel="icon" href="/old/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/savmrl/script.js"></script>

    <?php
    if (variables_permission_yes_or_not(10)) {
        ?>
        <script>
            function change_status(id) {
                const statusElement = document.getElementById("status-" + id);
                const currentStatus = statusElement.classList;

                const rowElement = document.getElementById("row-" + id);
                const currentRowClass = rowElement.classList;

                $.ajax({
                    url: "./include/change-status.php",
                    type: "POST",
                    data: JSON.stringify({id: id}),
                    success: function (response) {
                        //console.log("Status updated successfully:", response);
                        if (currentStatus.contains("not-reported")) {
                            statusElement.classList.remove("not-reported");
                            statusElement.classList.add("reported");
                        } else if (currentStatus.contains("reported")) {
                            statusElement.classList.remove("reported");
                            statusElement.classList.add("not-reported");
                        }
                        if (currentRowClass.contains("not-reported")) {
                            rowElement.classList.remove("not-reported");
                            rowElement.classList.add("reported");
                        } else if (currentRowClass.contains("reported")) {
                            rowElement.classList.remove("reported");
                            rowElement.classList.add("not-reported");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error updating status:", error);
                        alert("An error occurred while updating the status.");
                    }
                });
            }
        </script>
    <?php
    }else {
    ?>
        <script>
            function change_status(id) {
                alert("You don't have enough permissions to change the status of this link.");
            }
        </script>
        <?php
    }
    ?>
</head>
<body>
<main class="padding-10 border-box">
    <?php
    if (variables_permission_yes_or_not(10)) {
        ?>
        <table>
            <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Link</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            global $localhost_db, $username_db, $password_db, $database_savmrl_api;
            $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl_api);
            $c->set_charset("utf8");
            $sql = "SELECT * FROM `redirect_savmrl` WHERE `access_code` IS NULL ORDER BY `inserted_timestamp` DESC LIMIT 50";
            if ($r = $c->query($sql)) {
                if ($r->num_rows > 0) {
                    echo "<div class='padding-30'>Last 50 links inserted: <small class='tip'>Double click on the status to toggle</small></div>";
                    $array = array();
                    while ($row = $r->fetch_array()) {
                        $truncated_link = $row["redirect_link"];
                        if (strlen($truncated_link) > 50) {
                            $truncated_link = substr($truncated_link, 0, 50) . "…";
                        }
                        $reported_class = "";
                        if ($row["reported"] === "1") {
                            $reported_class = "reported";
                        } else {
                            $reported_class = "not-reported";
                        }
                        echo "<tr id='row-" . $row["id"] . "' class='" . $reported_class . "'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td title='Inserted: " . $row["inserted_timestamp"] . "'>" . $row["name"] . "</td>";
                        echo "<td class='td-link'><a href='" . $row["redirect_link"] . "' target='_blank' title='" . $row["redirect_link"] . "'>" . $truncated_link . "</a></td>";
                        echo "<td><div ondblclick='change_status(\"" . $row["id"] . "\")' id='status-" . $row["id"] . "' class='status " . $reported_class . "'></div></td>";
                        echo "</tr>";
                    }
                }
            }
            $c->close();
            ?>
            </tbody>
        </table>
        <?php
        ?>
        <?php
    } else {
        echo("<div class='message'>You don't have enough permissions to see this page.</div>");
    }
    ?>
</main>

<?php show_admin_bar(); ?>
</body>
</html>
