<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Nuovo articolo";
    $current_page = "new-article";
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
                <h1 class="h1-left">Nuovo articolo</h1>
                <hr class="hr-left margin-bottom30">
                <?php if (!permission_yes_or_not(9)) { ?>
                    <div class='alert'>Devi effettuare l'accesso per poter creare un nuovo articolo.</div>
                <?php } else {
                if (isset($_POST["title"]) != "" && isset($_POST["path"]) != "" && isset($_POST["tags"]) != "" && isset($_POST["image"]) != "" && isset($_POST["article"]) != "") {
                    global $path;
                    $article_path = $_POST["path"];
                    $url = $path . "/blog/";

                    $sql_log = "INSERT INTO logs(`id`, `user`, `date`, `ip`, `status`, `type`) VALUES(NULL, '{{*{{id}}*}}','" . htmlspecialchars(date("Y-m-d H:i:s")) . "','" . htmlspecialchars(get_user_ip()) . "','{{*{{status}}*}}', '{{*{{type}}*}}')";

                    $url = $url . date("Y") . "/";
                    if (!is_dir($url)) mkdir($url, 0755);
                    $url = $url . date("m") . "/";
                    if (!is_dir($url)) mkdir($url, 0755);

                    $url = $url . $article_path . "/";
                    if (!is_dir($url)) {
                        mkdir($url, 0755);
                        $url = "/blog/" . date("Y") . "/" . date("m") . "/" . $article_path . "/";
                        $date = date("Y-m-d H:i:s");
                        $article = $_POST["article"];
                        $article = str_replace("'", "&#39;", $article);
                        $article = str_replace("\\", "&#92;", $article);
                        $title = $_POST["title"];
                        $title = str_replace("'", "&#39;", $title);
                        $tags = explode(", ", $_POST["tags"]);

                        $c = new mysqli($localhost_db, $username_db, $password_db, $database_db);
                        $c->set_charset("utf8");

                        $sql_to_use = $sql_log;
                        $sql_to_use = str_replace("{{*{{type}}*}}", "new-article", $sql_to_use);
                        $sql_to_use = str_replace("{{*{{id}}*}}", $_SESSION["user_id"], $sql_to_use);

                        $sql = "INSERT INTO articles(id, status, published, last_updated, author, title, path, image, text) VALUES (NULL, 1, '" . $date . "', '" . $date . "', '" . $_SESSION["user_id"] . "', '" . $title . "', '" . $url . "', '" . $_POST["image"] . "', '" . $article . "')";
                        if ($c->query($sql)) {
                            echo "<div class='alert'>Articolo inserito correttamente.</div>";

                            $sql_to_use = str_replace("{{*{{status}}*}}", "0", $sql_to_use);
                            $c->query($sql_to_use);
                        } else {
                            echo "<div class='alert'>Non è stato possibile inserire l'articolo.</div>";

                            $sql_to_use = str_replace("{{*{{status}}*}}", "1", $sql_to_use);
                            $c->query($sql_to_use);
                        }
                        $tags_id = array();
                        $sql2 = "SELECT * FROM tags WHERE text = ";
                        $sql5 = "SELECT * FROM articles WHERE path='" . $url . "' AND title='" . $title . "'";
                        $id_article_to_insert = 0;
                        $r = $c->query($sql5);
                        if ($r->num_rows == 1) {
                            $row = $r->fetch_array();
                            $id_article_to_insert = $row["id"];

                            $link = $path . $url . "index.php";
                            $link_modello = $path . "/blog/model.php";
                            //open model-file and open article-file
                            $new_article = fopen($link, "w+");
                            $article_modello = fopen($link_modello, "r");
                            $file = fread($article_modello, filesize($link_modello));
                            fclose($article_modello);
                            //replace generic-id with specific-id of the article
                            $file_to_create = fwrite($new_article, str_replace("\"{{*{{id_articolo}}*}}\"", $id_article_to_insert, $file));
                            fclose($new_article);
                            echo "<br><center><a href='" . $url . "index.php" . "'>" . $url . "index.php" . "</a></center><br><hr><br>";
                        }
                        for ($i = 0; $i < count($tags); $i++) {
                            $id_tag_to_insert = 0;
                            $sql3 = $sql2 . "'" . $tags[$i] . "'";
                            $r = $c->query($sql3);
                            if ($r->num_rows == 0) {
                                //tag doesn't exist
                                $sql4 = "INSERT INTO tags(id, text) VALUES(NULL, '" . $tags[$i] . "')";
                                if ($c->query($sql4)) {
                                    //echo "Tag added: " . $tags[$i] . "!";
                                }
                            }
                            $r = $c->query($sql3);
                            if ($r->num_rows == 1) {
                                //tag exist
                                $row = $r->fetch_array();
                                $id_tag_to_insert = $row["id"];
                                array_push($tags_id, $id_tag_to_insert);
                                $sql4 = "INSERT INTO tagsArticles(id, article, tag) VALUES(NULL, " . $id_article_to_insert . "," . $id_tag_to_insert . ")";
                                if ($c->query($sql4)) {
                                    //echo "Tag & Article added!";
                                }
                            }
                        }
                        $c->close();
                    } else {
                        echo "<div class='alert'>Non è stato possibile creare questo articolo perché esiste già un articolo con lo stesso percorso.</div>";
                    }
                }
                ?>
                    <form action="./" method="post">
                        <input type="text" name="title" placeholder="Titolo" id="admin-title-article"
                               onchange="generate_path(this.value)" onkeyup="generate_path(this.value)" required/>
                        <input type="text" name="path" placeholder="Percorso articolo" id="admin-path-article"
                               required/>
                        <input type="text" name="tags" placeholder="Tag (separare con virgola e spazio)" required/>
                        <input type="text" name="image" value="/images/articles/" id="admin-image-url"
                               placeholder="Immagine in evidenza"
                               onkeyup="update_show_image_preview()"
                               required/><input type="button" id="admin-image-show-hide" onclick="show_hide_image()"/>
                        <div id="admin-image-preview-url"></div>
                        <textarea id="textarea-article" name="article" placeholder="Articolo" wrap="off"></textarea>
                        <input type="submit" value="Crea"/>
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
                        var article_text_draft =
                            "<!-- testo articolo -->\n\n\n" +
                            "<!-- slider -->\n" +
                            "[[*[[slider[[*[[\n{\"n_images\":\"0\", \"extension\": \"jpg\", \"path\": \"/images/articles/{{*{{nome_cartella}}*}}/slide/\", \"previews\": \"false\"}\n]]*]]slider]]*]]\n\n" +
                            "<!-- link utili -->\n" +
                            "<!--\n" +
                            "<br>\n<br>\n<br>\n" +
                            "<b>Link utili</b>\n" +
                            "<br>\n<small>\n<a href=\"\"></a>\n" +
                            "<br/>\n<a href=\"\"></a>\n</small>\n" +
                            "-->";
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
                        }).setValue(article_text_draft);
                    </script>
                <?php } ?>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->