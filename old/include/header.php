<?php
global $title, $url_opengraph;
if (isset($title)) {
    echo "<title>" . $title . " – Saverio Morelli</title>";
} else {
    $title = "?";
    echo "<title>Saverio Morelli Official</title>";
}
if (!isset($url_opengraph) || $url_opengraph == "") {
    $url_opengraph = "https://www.saveriomorelli.com/images/opengraph/image.png";
}
?>

    <link rel="stylesheet" href="/old/style/site.css"/>
    <link rel="icon" href="/old/images/icon.png"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://unpkg.com/twemoji@13.1.0/dist/twemoji.min.js"></script>
    <script src="/old/script/site.js"></script>
    <meta http-equiv="content-type" content="text/html; charset=UTF-16">
    <meta name="viewport" content="width=device-width, initial-scale=0.8"/>

    <!--<meta name="google-adsense-account" content="ca-pub-4441008333572114">-->

    <meta property="og:locale" content="it_IT"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Saverio Morelli"/>
    <meta property="og:description"
          content="Saverio Morelli – Frontend developer & UX designer"/>
    <meta property="og:url" content="https://www.saveriomorelli.com/"/>
    <meta property="og:site_name" content="Saverio Morelli"/>
    <meta property="og:image" content="<?php echo $url_opengraph; ?>"/>
    <meta property="og:image:secure_url" content="<?php echo $url_opengraph; ?>"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:description"
          content="Saverio Morelli – Frontend developer & UX designer"/>
    <meta name="twitter:title" content="Saverio Morelli"/>
    <meta name="twitter:site" content="@Sav22999"/>
    <meta name="twitter:image" content="<?php echo $url_opengraph; ?>"/>
    <meta name="twitter:creator" content="@Sav22999"/>

    <div id="loading"></div> <!-- Show the "loading page" while it loads -->
    <div id="working">
        <div id="working_sentence">Manutenzione in corso.<br>Il sito web e il server sono in aggiornamento.<br>Il sito
            web tornerà presto on-line.
        </div>
    </div> <!-- Show the "working in progress" -->

<?php
if (work_in_progress()) {
    //show work in progress
    echo "<script>$('#working').css('display','block');</script>";
}

function show_pages_navigator($page_number, $all_articles, $more_details)
{
    global $arrow_left;
    global $arrow_right;
    global $n_articles_per_page;

    $n_pages = (int)($all_articles / $n_articles_per_page);
    if ($all_articles % $n_articles_per_page > 0) {
        $n_pages += 1;
    }

    echo "<div id=\"pages-navigator\">
            <ul>";
    if ($n_pages > 1) {
        if ($page_number >= 1 + 1) {
            ?>
        <a href="./?p=<?php echo $page_number - 1; ?>">
            <li><?php echo $arrow_left; ?></li></a><?php
        }
        if ($page_number > 1) {
            ?>
            <a href="./?p=1<?php echo $more_details; ?>">
                <li>
                    1
                </li>
            </a>
            <?php
        }
        if ($page_number > 1 + 2) {
            ?>
            <a href="./?p=<?php echo $page_number - 2; ?><?php echo $more_details; ?>">
                <li>
                    <?php echo $page_number - 2; ?>
                </li>
            </a>
            <?php
        }
        if ($page_number > 1 + 1) {
            ?>
            <a href="./?p=<?php echo $page_number - 1; ?><?php echo $more_details; ?>">
                <li>
                    <?php echo $page_number - 1; ?>
                </li>
            </a>
            <?php
        }
        ?>
        <li id="current"><?php echo $page_number; ?></li> <?php
        if ($page_number < ($n_pages - 1)) {
            ?>
            <a href="./?p=<?php echo $page_number + 1; ?><?php echo $more_details; ?>">
                <li>
                    <?php echo $page_number + 1; ?>
                </li>
            </a>
            <?php
        }
        if ($page_number < ($n_pages - 2)) {
            ?>
            <a href="./?p=<?php echo $page_number + 2; ?><?php echo $more_details; ?>">
                <li>
                    <?php echo $page_number + 2; ?>
                </li>
            </a>
            <?php
        }
        if ($page_number < $n_pages) {
            ?>
            <a href="./?p=<?php echo $n_pages; ?><?php echo $more_details; ?>">
                <li>
                    <?php echo $n_pages; ?>
                </li>
            </a>
            <?php
        }

        if ($page_number <= ($n_pages - 1)) {
            ?>
            <a href="./?p=<?php echo $page_number + 1; ?><?php echo $more_details; ?>">
                <li>
                    <?php echo $arrow_right; ?>
                </li>
            </a>
            <?php
        }
    }
    echo "</ul>
        </div>";
}

function work_in_progress()
{
    global $current_page, $localhost_db, $username_db, $password_db, $database_db, $work_in_progress;
    $conn_work = new mysqli($localhost_db, $username_db, $password_db, $database_db);
    $conn_work->set_charset("utf8");
    $sql_work = "SELECT status FROM maintenances ORDER BY date DESC LIMIT 1";
    $r_work = $conn_work->query($sql_work);
    if ($r_work->num_rows == 1) {
        $row = $r_work->fetch_array();
        if ($row["status"] == 0) {
            $work_in_progress = false;
        } else {
            $work_in_progress = true;
        }
    }
    $conn_work->close();
    return ($work_in_progress && $current_page != "admin" && $_SESSION["user_id"] == 0);
}

function permission_yes_or_not($permission_required)
{
    return $_SESSION["user_permission"] >= $permission_required;
}

function show_no_permission_message()
{
    return "Non hai i permessi per poter visualizzare questa pagina.";
}

function after($string, $inthat)
{
    if (!is_bool(strpos($inthat, $string)))
        return substr($inthat, strpos($inthat, $string) + strlen($string));
}

function after_last($string, $inthat)
{
    if (!is_bool(strrevpos($inthat, $string)))
        return substr($inthat, strrevpos($inthat, $string) + strlen($string));
}

function before($string, $inthat)
{
    return substr($inthat, 0, strpos($inthat, $string));
}

function before_last($string, $inthat)
{
    return substr($inthat, 0, strrevpos($inthat, $string));
}

function between($string, $that, $inthat)
{
    return before($that, after($string, $inthat));
}

function between_last($string, $that, $inthat)
{
    return after_last($string, before_last($that, $inthat));
}

function strrevpos($instr, $needle)
{
    $rev_pos = strpos(strrev($instr), strrev($needle));
    if ($rev_pos === false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
}

function str_replace_first($search, $replace, $subject)
{
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

$number_slider = 0 + 0;
function show_slider($slider_json)
{
    global $arrow_left_slider, $arrow_right_slider, $number_slider;
    $number_slider += 0;
    $json = json_decode($slider_json);
    $n_images = strval($json->{"n_images"});
    $extension = $json->{"extension"};
    $path_slider = $json->{"path"};
    $previews = $json->{"previews"};
    if ($n_images > 0) {
        $return = '';
        $return .= '<div id="slider" class="slider-loading-' . $number_slider . '">';
        $return .= '<img class="image-slider slider-' . $number_slider . '" />';
        $return .= '<div id="back-arrow" class="back-arrow-' . $number_slider . '" onclick="back_slider(' . $number_slider . ')">' . $arrow_left_slider . '</div>';
        $return .= '<div id="forward-arrow" class="forward-arrow-' . $number_slider . '" onclick="forward_slider(' . $number_slider . ')">' . $arrow_right_slider . '</div>';
        $return .= '</div>';
        $return .= '<div id="preview-slider"></div>';
        $return .= '';
        $return .= '';
        $return .= '<script>';
        $return .= 'initialise_slider(' . $number_slider . ',' . $n_images . ',"' . $path_slider . '","' . $extension . '");';
        $return .= '</script>';
        ?>
        <!--<input type="button" id="tasto_chiudi" onclick="chiudi()" />-->
        <!--<div id='image_slider_open_zoom_layer' onclick='show_image_zoom_slider()'></div>-->
        <?php
        if ($previews == "true") {
            //show preview
        }
        ?>
        <?php
        $number_slider++;
        return $return;
    }
    return "";
}

?>