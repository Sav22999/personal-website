<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Admin";
    $current_page = "admin";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<main class="clearfix">
    <aside>
        <?php show_aside(); ?>
    </aside>

    <section>
        <article>
            <div id="admin-sec">
                <?php
                global $localhost_db, $username_db, $password_db, $database_db;

                function logAction($userId, $status, $type)
                {
                    global $localhost_db, $username_db, $password_db, $database_db;

                    $sql_log = "INSERT INTO logs(`id`, `user`, `date`, `ip`, `status`, `type`) VALUES(NULL, ?, ?, ?, ?, ?)";

                    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                    $c->set_charset("utf8");

                    $stmt = $c->prepare($sql_log);
                    $date = date("Y-m-d H:i:s");
                    $ip = get_user_ip();

                    $stmt->bind_param("sssss", $userId, $date, $ip, $status, $type);
                    $stmt->execute();

                    $stmt->close();
                    $c->close();
                }

                if (isset($_POST["username"]) && isset($_POST["password"]) && $_SESSION["user_id"] == 0) {
                    // Login
                    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                    $c->set_charset("utf8");

                    $username = $_POST["username"];
                    $password = hash('sha256', $_POST["password"]);

                    $sql = "SELECT * FROM users WHERE (username=? OR email=?) AND password=?";

                    $stmt = $c->prepare($sql);
                    $stmt->bind_param("sss", $username, $username, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        $_SESSION["user_id"] = $row["id"];
                        $_SESSION["user_permission"] = $row["privileges"];
                        echo "<div class='alert'>Accesso eseguito correttamente.</div>";
                        logAction($row["id"], "0", "login");
                    } else {
                        echo "<div class='alert'>Accesso non riuscito.</div>";
                        logAction("0", "1", "login");
                    }

                    $stmt->close();
                    $c->close();
                }

                if ((isset($_POST["logout"]) || isset($_GET["logout"])) && $_SESSION["user_id"] != 0) {
                    // Logout
                    logAction($_SESSION["user_id"], "0", "logout");

                    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                    $c->set_charset("utf8");
                    $c->close();

                    session_unset();
                    session_destroy();
                    echo "<div class='alert'>Disconnessione riuscita.</div>";
                }

                if (isset($_GET["session_expired"]) && $_SESSION["user_id"] != 0) {
                    logAction($_SESSION["user_id"], "0", "session-expired");

                    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                    $c->set_charset("utf8");
                    $c->close();

                    session_unset();
                    session_destroy();
                    echo "<div class='alert'>Sessione scaduta.</div>";
                }
                ?>
                <h1 class="h1-center">Area riservata</h1>
                <hr class="hr-center margin-bottom30">
                <?php if ($_SESSION["user_id"] == 0) { ?>
                    <form action="./" method="post">
                        <input type="text" name="username" placeholder="Username or email" required/>
                        <input type="password" name="password" placeholder="Password" required/>
                        <input type="submit" value="Accedi"/>
                    </form>
                <?php } else {
                    $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                    $c->set_charset("utf8");
                    $sql = "SELECT * FROM users WHERE id=" . htmlspecialchars($_SESSION["user_id"]);
                    $r = $c->query($sql);
                    if ($r->num_rows == 1) {
                        $row = $r->fetch_assoc();
                        echo "<div id='user-data' class='clearfix'>";
                        ?>
                    <div id="user-data-img"
                         style="background-image: url('https://s.gravatar.com/avatar/<?php echo md5($row["email"]); ?>?s=500&r=g');"></div><?php
                        echo "Id: " . $row["id"];
                        echo "<br>Nome: " . $row["name"];
                        echo "<br>Cognome: " . $row["surname"];
                        echo "<br>Username: " . $row["username"];
                        echo "<br>Email: " . $row["email"];
                        echo "</div>";

                        global $work_in_progress;
                        if (permission_yes_or_not(10)) {
                            echo "<div id='user-data' class='clearfix'>";
                            ?>
                            Lavori in corso:
                            <div id='admin-yes-no'>
                                <button onclick="yes_no_working(1)" id='yes-working'
                                        class='admin-yes-no-button <?php if ($work_in_progress) {
                                            echo "admin-yes-not-selected";
                                        } ?>'>Sì
                                </button>
                                <button onclick="yes_no_working(0)" id='no-working'
                                        class='admin-yes-no-button <?php if (!$work_in_progress) {
                                            echo "admin-yes-not-selected";
                                        } ?>'>No
                                </button>
                            </div>
                            <?php
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='alert'>Non è stato possibile caricare i dati.</div>";
                    }
                    $c->close();
                    ?>
                    <form action="./" method="post">
                        <input type="submit" name="logout" value="Disconnetti"/>
                    </form>
                <?php } ?>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->