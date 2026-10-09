<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Tutti gli articoli";
    $current_page = "all-articles";
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
        <?php if ($_SESSION["user_id"] == 0) { ?>
            <h1 class="h1-left">Tutti gli articoli</h1>
            <hr class="hr-left margin-bottom30">
            <div class='alert'>Devi effettuare l'accesso per poter visualizzare tutti gli articoli.</div>
        <?php } else {
            $details = "";
            if ((isset($_GET["filter"]) && $_GET["filter"] != "0" && $_GET["filter"] != "1" && $_GET["filter"] != "2" && $_GET["filter"] != "3") || !isset($_GET["filter"])) {
                $details = "status='0' OR status='1' OR status='2' OR status='3'";
            } else if (isset($_GET["filter"]) && $_GET["filter"] == "0") {
                $details = "status='0'";
            } else if (isset($_GET["filter"]) && $_GET["filter"] == "1") {
                $details = "status='1'";
            } else if (isset($_GET["filter"]) && $_GET["filter"] == "2") {
                $details = "status='2'";
            } else if (isset($_GET["filter"]) && $_GET["filter"] == "3") {
                $details = "status='3'";
            }

            $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
            $c->set_charset("utf8");
            $c2 = new mysqli($localhost_db, $username_db, $password_db, $database_db);
            $c2->set_charset("utf8");

            $sql3 = "SELECT COUNT(*) as n FROM articles WHERE " . htmlspecialchars($details);
            $r2 = $c2->query($sql3);
            $all_articles = ($r2->fetch_assoc())["n"];
            $page_number = 1;
            if (isset($_GET["p"])) {
                $page_number = $_GET["p"];
            }

            $sql = "SELECT articles.*, users.username as author_username FROM articles INNER JOIN users ON articles.author = users.id WHERE " . $details . " ORDER BY last_updated DESC LIMIT " . htmlspecialchars($n_articles_per_page) . " OFFSET " . ($n_articles_per_page * ($page_number - 1));
            $sql2 = "SELECT tags.id as tag_id, tags.text as tag_text, tagsArticles.article as article FROM tags INNER JOIN tagsArticles ON tags.id = tagsArticles.tag WHERE tagsArticles.article =";
            $r = $c->query($sql);
            ?>
            <div id="admin-all-articles">
                <button onclick="location.href='./?filter='" id='status-all'
                        class='admin-all-articles-button <?php if ((isset($_GET["filter"]) && $_GET["filter"] != "0" && $_GET["filter"] != "1" && $_GET["filter"] != "2" && $_GET["filter"] != "3") || !isset($_GET["filter"])) {
                            echo "admin-all-articles-selected";
                        } ?>'>
                    Tutti
                </button>
                <button onclick="location.href='./?filter=0'" id='status-0'
                        class='admin-all-articles-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "0") {
                            echo "admin-all-articles-selected";
                        } ?>'>
                    Non definiti
                </button>
                <button onclick="location.href='./?filter=1'" id='status-1'
                        class='admin-all-articles-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "1") {
                            echo "admin-all-articles-selected";
                        } ?>'>
                    Non pubblicati
                </button>
                <button onclick="location.href='./?filter=2'" id='status-2'
                        class='admin-all-articles-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "2") {
                            echo "admin-all-articles-selected";
                        } ?>'>
                    Pubblicati
                </button>
                <button onclick="location.href='./?filter=3'" id='status-3'
                        class='admin-all-articles-button <?php if (isset($_GET["filter"]) && $_GET["filter"] == "3") {
                            echo "admin-all-articles-selected";
                        } ?>'>
                    Eliminati
                </button>
            </div>
            <?php
            if ($r->num_rows > 0) {
                while ($row = $r->fetch_assoc()) {
                    $title = $row["title"];
                    $author = $row["author_username"];
                    $description_text = $row["text"];

                    //remove all comments from code
                    while (($comment = between_last('<!--', '-->', $description_text)) != "") {
                        $description_text = str_replace('<!--' . $comment . '-->', "", $description_text);
                    }
                    //remove all sliders from code
                    while (($slider = between_last('[[*[[slider[[*[[', ']]*]]slider]]*]]', $description_text)) != "") {
                        $description_text = str_replace('[[*[[slider[[*[[' . $slider . "]]*]]slider]]*]]", "", $description_text);
                    }
                    $description = explode(" ", $description_text);
                    $short_description = "";
                    for ($i = 0; $i < 50 && $i < count($description); $i++) {
                        $short_description .= $description[$i] . " ";
                    }
                    $short_description .= " …";
                    $image = $row["image"];
                    $status = $row["status"];
                    $published = date('d/m/Y', strtotime($row["published"]));
                    $title_details = "";
                    if ($status == 3) {
                        $title_details = "<div class='article-title-details title-deleted-article'>Eliminato</div>";
                    } else if (date('d', strtotime($row["last_updated"])) == date('d') && date('d', strtotime($row["published"])) != date('d', strtotime($row["last_updated"]))) {
                        $title_details = "<div class='article-title-details title-updated-article'>Aggiornato</div>";
                    } else if (date('d', strtotime($row["published"])) == date('d')) {
                        $title_details = "<div class='article-title-details title-new-article'>Nuovo</div>";
                    }
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

                    <article class="status-<?php echo $row["status"]; ?>">
                        <a href="<?php echo $path; ?>">
                            <div class="cover-img" style="background-image: url('<?php echo $image; ?>');"></div>
                        </a>
                        <h3 id="date"><?php echo $last_updated; ?></h3>
                        <h3 id="author"><a href="/old/search/?author=<?php echo $author; ?>"><?php echo $author; ?></a>
                        </h3>
                        <h3 id="tag">
                            <ul>
                                <?php for ($i = 0; $i < count($tags); $i++) { ?>
                                    <li><a href="/old/search/?k=<?php echo $tags[$i]; ?>"><?php echo $tags[$i]; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </h3>
                        <a href="<?php echo $path; ?>">
                            <h1 class="h1-article">
                                <?php echo $title_details; ?>
                                <?php echo $title; ?>
                            </h1>
                        </a>
                        <div class="article-text article-text-list">
                            <?php echo $short_description; ?>
                        </div>
                        <a href="<?php echo $path; ?>">
                            <button>Continua a leggere...</button>
                        </a>
                    </article>
                    <?php
                }
                $more_details = "";
                if (isset($_GET["filter"])) $more_details = "&filter=" . $_GET["filter"];
                show_pages_navigator($page_number, $all_articles, $more_details);
            }
            $c2->close();
            $c->close();
        }
        ?>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->