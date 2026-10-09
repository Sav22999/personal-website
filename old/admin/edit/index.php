<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Modifica articolo";
    $current_page = "edit-article";
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
            <div id="admin-new-article-sec">
                <h1 class="h1-left">Modifica articolo</h1>
                <hr class="hr-left">
                <?php if ($_SESSION["user_id"] == 0) { ?>
                    <div class='alert'>Devi effettuare l'accesso per poter modificare un nuovo articolo.</div>
                <?php } else if (isset($_GET["article"]) && $_GET["article"]) {
                $article_id = $_GET["article"];

                $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                $c->set_charset("utf8");
                $c2 = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                $c2->set_charset("utf8");

                $sql_log = "INSERT INTO logs(`id`, `user`, `date`, `ip`, `status`, `type`) VALUES(NULL, '{{*{{id}}*}}','" . htmlspecialchars(date("Y-m-d H:i:s")) . "','" . htmlspecialchars(get_user_ip()) . "','{{*{{status}}*}}', '{{*{{type}}*}}')";

                $sql = "SELECT articles.*, users.username as author_username FROM articles INNER JOIN users ON articles.author = users.id WHERE articles.id=" . htmlspecialchars($article_id) . " ORDER BY last_updated DESC";
                $sql2 = "SELECT tags.id as tag_id, tags.text as tag_text, tagsArticles.article as article FROM tags INNER JOIN tagsArticles ON tags.id = tagsArticles.tag WHERE tagsArticles.article=" . htmlspecialchars($article_id);
                $sql3 = "DELETE FROM tagsArticles WHERE tagsArticles.article = " . htmlspecialchars($article_id);
                $sql4 = "INSERT INTO tags(id, text) VALUES(NULL, '";
                $sql5 = "INSERT INTO tagsArticles(id, article, tag) VALUES(NULL, " . htmlspecialchars($article_id) . ", ";
                $sql7 = "SELECT * FROM tags WHERE text = '";
                $r = $c->query($sql);
                if ($r->num_rows == 1) {

                $row = $r->fetch_assoc();
                $tags = array();
                $r2 = $c->query($sql2);
                if ($r2->num_rows > 0) {
                    while ($row2 = $r2->fetch_assoc()) {
                        array_push($tags, $row2["tag_text"]);
                    }
                }
                $tags_text = "";
                if (count($tags) > 0) {
                    $tags_text .= $tags[0];
                    for ($i = 1; $i < count($tags); $i++) {
                        $tags_text .= ", " . $tags[$i];
                    }
                }


                if (isset($_POST["title"]) != "" && isset($_POST["path"]) != "" && isset($_POST["tags"]) != "" && isset($_POST["image"]) != "" && isset($_POST["article"]) != "" && isset($_POST["status"]) != "") {
                    global $path;
                    $article_path = $_POST["path"];
                    $url = $path . "/blog/";

                    //delete the old-file and the old-folder
                    if (is_dir($path . $row["path"])) {
                        unlink($path . $row["path"] . "index.php");
                        rmdir($path . $row["path"]);
                    }

                    $published = $row["published"];
                    $year_published = date('Y', strtotime($published));
                    $month_published = date('m', strtotime($published));

                    $url = $url . $year_published . "/";
                    if (!is_dir($url)) mkdir($url, 0755);
                    $url = $url . $month_published . "/";
                    if (!is_dir($url)) mkdir($url, 0755);

                    $tags_id = array();
                    $url = $url . $article_path . "/";
                    if (!is_dir($url) || $_POST["path"] == $article_path) {
                        if (!is_dir($url)) mkdir($url, 0755);
                        $url = "/blog/" . $year_published . "/" . $month_published . "/" . $article_path . "/";
                        $last_updated = date("Y-m-d H:i:s");
                        $article = $_POST["article"];
                        $article = str_replace("'", "&#39;", $article);
                        $article = str_replace("\\", "&#92;", $article);
                        $title = $_POST["title"];
                        $title = str_replace("'", "&#39;", $title);
                        $tags = explode(", ", $_POST["tags"]);

                        $link = $path . $url . "index.php";
                        $link_modello = $path . "/blog/model.php";
                        //open model-file and open article-file
                        $new_article = fopen($link, "w+");
                        $article_modello = fopen($link_modello, "r");
                        $file = fread($article_modello, filesize($link_modello));
                        fclose($article_modello);
                        //replace generic-id with specific-id of the article
                        $file_to_create = fwrite($new_article, str_replace("\"{{*{{id_articolo}}*}}\"", $article_id, $file));
                        fclose($new_article);

                        $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                        $c->set_charset("utf8");

                        $sql_to_use = $sql_log;
                        $sql_to_use = str_replace("{{*{{type}}*}}", "update-article", $sql_to_use);
                        $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);

                        $sql6 = "UPDATE articles SET last_updated='" . htmlspecialchars($last_updated) . "' , status=" . htmlspecialchars($_POST["status"]) . " , path='" . htmlspecialchars($url) . "' , title='" . $title . "' , text='" . $article . "', image='" . htmlspecialchars($_POST["image"]) . "' WHERE id=" . htmlspecialchars($article_id);
                        if ($c->query($sql6)) {
                            echo "<div class='alert'>Articolo aggiornamento correttamente.</div>";
                            echo "<a href='" . $url . "'><button>Vai all'articolo</button></a><a href='./?article=" . htmlspecialchars($article_id) . "'><button>Modifica nuovamente</button></a>";

                            $sql_to_use = str_replace("{{*{{status}}*}}", "0", $sql_to_use);
                            $c->query($sql_to_use);
                        } else {
                            echo "<div class='alert'>Errore durante l'aggiornamento dell'articolo.</div>";

                            $sql_to_use = str_replace("{{*{{status}}*}}", "1", $sql_to_use);
                            $c->query($sql_to_use);
                        }
                        if ($c->query($sql3)) {
                            //
                            //echo "Deleted all!";
                        }
                        for ($i = 0; $i < count($tags); $i++) {
                            $id_tag_to_insert = 0;
                            $r = $c->query($sql7 . htmlspecialchars($tags[$i]) . "'");
                            if ($r->num_rows == 0) {
                                //tag doesn't exist
                                if ($c->query($sql4 . htmlspecialchars($tags[$i]) . "')")) {
                                    //
                                    //echo "Tag added: " . $tags[$i] . "!";
                                }
                            }
                            $r = $c->query($sql7 . htmlspecialchars($tags[$i]) . "'");
                            if ($r->num_rows == 1) {
                                //tag exist
                                $row = $r->fetch_array();
                                $id_tag_to_insert = $row["id"];
                                array_push($tags_id, $id_tag_to_insert);
                                if ($c->query($sql5 . $id_tag_to_insert . ")")) {
                                    //
                                    //echo "Tag & Article added!";
                                }
                            }
                        }
                        $c->close();
                    } else {
                        echo "<div class='alert'>Errore nella creazione dell'articolo.</div>";
                    }
                } else {
                ?>
                    <div id="admin-update-article-status">
                        <button onclick="change_status(0)" id='status-0'
                                class='admin-update-article-status-button <?php if ($row["status"] == 0) {
                                    echo "admin-update-article-status-selected";
                                } ?>'>
                            Non definito
                        </button>
                        <button onclick="change_status(1)" id='status-1'
                                class='admin-update-article-status-button <?php if ($row["status"] == 1) {
                                    echo "admin-update-article-status-selected";
                                } ?>'>
                            Non pubblicato
                        </button>
                        <button onclick="change_status(2)" id='status-2'
                                class='admin-update-article-status-button <?php if ($row["status"] == 2) {
                                    echo "admin-update-article-status-selected";
                                } ?>'>
                            Pubblicato
                        </button>
                        <button onclick="change_status(3)" id='status-3'
                                class='admin-update-article-status-button <?php if ($row["status"] == 3) {
                                    echo "admin-update-article-status-selected";
                                } ?>'>
                            Eliminato
                        </button>
                    </div>
                    <form action="./?article=<?php echo $article_id; ?>" method="post">
                        <input type="radio" name="status" value="0" id="status0"
                               class="hidden" <?php if ($row["status"] == 0) {
                            echo "checked";
                        } ?>>
                        <input type="radio" name="status" value="1" id="status1"
                               class="hidden" <?php if ($row["status"] == 1) {
                            echo "checked";
                        } ?>>
                        <input type="radio" name="status" value="2" id="status2"
                               class="hidden" <?php if ($row["status"] == 2) {
                            echo "checked";
                        } ?>>
                        <input type="radio" name="status" value="3" id="status3"
                               class="hidden" <?php if ($row["status"] == 3) {
                            echo "checked";
                        } ?>>
                        <input type="text" name="title"
                               value="<?php echo str_replace("\"", "&quot;", str_replace("'", "&#39;", $row["title"])); ?>"
                               placeholder="Titolo"
                               id="admin-title-article"
                               onchange="generate_path(this.value)" onkeyup="generate_path(this.value)"
                               required/>
                        <input type="text" name="path" value="<?php echo explode("/", $row["path"])[4]; ?>"
                               placeholder="Percorso articolo" id="admin-path-article"
                               required/>
                        <input type="text" name="tags" value="<?php echo $tags_text; ?>"
                               placeholder="Tag (separare con virgola e spazio)" required/>
                        <input type="text" name="image" id="admin-image-url"
                               value="<?php echo $row["image"]; ?>"
                               placeholder="Immagine in evidenza"
                               onkeyup="update_show_image_preview()"
                               required/><input type="button" id="admin-image-show-hide"
                                                onclick="show_hide_image()"/>
                        <div id="admin-image-preview-url"></div>
                        <textarea id="textarea-article" name="article" placeholder="Articolo"
                                  wrap="off"><?php echo htmlentities($row["text"]); ?></textarea>
                        <input type="submit" value="Aggiorna"/>
                    </form>

                <link rel="stylesheet" href="/old/codemirror/lib/codemirror.css"/>
                <link rel="stylesheet" href="/old/codemirror/theme/lucario.css"/>
                <link rel="stylesheet" href="/old/codemirror/theme/darcula.css"/>
                    <script src="/old/codemirror/lib/codemirror.js"></script>
                    <script src="/old/codemirror/addon/edit/closetag.js"></script>
                    <script src="/old/codemirror/addon/fold/xml-fold.js"></script>
                    <script src="/old/codemirror/mode/xml/xml.js"></script>
                    <script src="/old/codemirror/mode/javascript/javascript.js"></script>
                    <script src="/old/codemirror/mode/css/css.js"></script>
                    <script src="/old/codemirror/mode/htmlmixed/htmlmixed.js"></script>
                    <script src="/old/codemirror/addon/selection/active-line.js"></script>
                    <style>
                        .CodeMirror {
                            border: 1px solid #3399ff;
                            border-radius: var(--border-radius);
                            transition: 0.5s;
                            margin: 10px;
                            margin-left: 5px;
                            margin-right: -5px;
                            font-size: 14px;
                            min-height: 500px;
                            text-align: left;
                        }
                    </style>

                    <script>
                        var editor = CodeMirror.fromTextArea(document.getElementById("textarea-article"), {
                            mode: 'text/html',
                            lineNumbers: true,
                            autoCloseTags: true,
                            indentUnit: 4,
                            theme: "darcula",
                            styleActiveLine: true,
                            matchBrackets: true,
                            autofocus: false,
                            smartIndent: true,
                            indentWithTabs: true,
                            autocomplete: true,
                            matchTags: {bothTags: true},
                            lineWrapping: true
                        });
                    </script>
                    <?php
                }
                } else {
                    echo "Errore 02.";
                }
                } else {
                    echo "Errore 03.";
                } ?>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->