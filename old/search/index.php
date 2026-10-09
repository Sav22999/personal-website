<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Risultati di ricerca";
    $current_page = "search";
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
        <?php
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
        $c->set_charset("utf8");
        $c2 = new mysqli($localhost_db, $username_db, $password_db, $database_db);
        $c2->set_charset("utf8");

        $details = "";
        if (isset($_GET["k"]) && $_GET["k"] != "") {
            $k = htmlspecialchars(htmlentities($_GET["k"], ENT_QUOTES));
            $details = " AND (title LIKE '%" . $k . "%' OR text LIKE '%" . $k . "%' OR '%" . $k . "%' LIKE title OR '%" . $k . "%' LIKE text)";

            $c->query("INSERT INTO searches(`id`, `type`, `k`, `ip`, `date`) VALUES(NULL, 'general', '" . $k . "', '" . get_user_ip() . "', '" . date("Y-m-d H:i:s") . "')");
        }
        $sql3 = "SELECT COUNT(*) as n FROM articles WHERE status=2" . $details;
        if (isset($_GET["author"])) {
            $sql3 = "SELECT COUNT(*) as n FROM articles INNER JOIN users ON articles.author = users.id WHERE articles.status=2 AND users.username = '" . htmlentities($_GET["author"], ENT_QUOTES) . "' ORDER BY last_updated DESC";

            $c->query("INSERT INTO searches(`id`, `type`, `k`, `ip`, `date`) VALUES(NULL, 'author', '" . htmlentities($_GET["author"], ENT_QUOTES) . "', '" . get_user_ip() . "', '" . date("Y-m-d H:i:s") . "')");
        }
        $r2 = $c2->query($sql3);
        $all_articles = ($r2->fetch_assoc())["n"];
        $page_number = 1;
        if (isset($_GET["p"])) {
            $page_number = $_GET["p"];
        }
        $sql = "";
        if (isset($_GET["author"])) {
            $sql = "SELECT articles.*, users.username as author_username FROM articles INNER JOIN  users ON articles.author = users.id WHERE status=2 AND users.username = '" . $_GET["author"] . "' ORDER BY last_updated DESC LIMIT " . $n_articles_per_page . " OFFSET " . ($n_articles_per_page * ($page_number - 1));
        } else {
            $sql = "SELECT articles.*, users.username as author_username FROM articles INNER JOIN users ON articles.author = users.id WHERE status=2" . $details . " ORDER BY last_updated DESC LIMIT " . $n_articles_per_page . " OFFSET " . ($n_articles_per_page * ($page_number - 1));
        }
        $sql2 = "SELECT tags.id as tag_id, tags.text as tag_text, tagsArticles.article as article FROM tags INNER JOIN tagsArticles ON tags.id = tagsArticles.tag WHERE tagsArticles.article =";
        $r = $c->query($sql);
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_assoc()) {
                $title = $row["title"];
                $author = $row["author_username"];
                $description = explode(" ", $row["text"]);
                $short_description = "";
                for ($i = 0; $i < 50; $i++) {
                    $short_description .= $description[$i] . " ";
                }
                $short_description .= " ...";
                $image = $row["image"];
                $status = $row["status"];
                //$published = $row["published"];
                $last_updated = date('d/m/Y', strtotime($row["last_updated"]));
                $path = $row["path"];
                $tags = array();
                $r2 = $c2->query($sql2 . $row["id"]);
                if ($r2->num_rows > 0) {
                    while ($row2 = $r2->fetch_assoc()) {
                        array_push($tags, $row2["tag_text"]);
                    }
                }
                ?>
                <article>
                    <a href="<?php echo $path; ?>">
                        <div class="cover-img" style="background-image: url('<?php echo $image; ?>');"></div>
                    </a>
                    <h3 id="date"><?php echo $last_updated; ?></h3>
                    <h3 id="author"><a href="/old/search/?author=<?php echo $author; ?>"><?php echo $author; ?></a></h3>
                    <h3 id="tag">
                        <ul>
                            <?php for ($i = 0; $i < count($tags); $i++) { ?>
                                <li><a href="/old/search/?k=<?php echo $tags[$i]; ?>"><?php echo $tags[$i]; ?></a></li>
                            <?php } ?>
                        </ul>
                    </h3>
                    <a href="<?php echo $path; ?>">
                        <h1 class="h1-article">
                            <?php echo $title; ?>
                        </h1>
                    </a>
                    <div class="article-text">
                        <?php echo $short_description; ?>
                    </div>
                    <a href="<?php echo $path; ?>">
                        <button>Continua a leggere...</button>
                    </a>
                </article>
                <?php
            }
            $more_details = "&";
            if (isset($_GET["author"])) $more_details .= "author=" . htmlspecialchars(htmlentities($_GET["author"], ENT_QUOTES));
            else if (isset($_GET["k"])) $more_details .= "k=" . htmlspecialchars(htmlentities($_GET["k"], ENT_QUOTES));
            show_pages_navigator($page_number, $all_articles, $more_details);
        } else {
            echo "Non sono presenti risultati.";
        }
        $c2->close();
        $c->close();
        ?>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->