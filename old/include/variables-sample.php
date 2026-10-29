<?php
/*
 * Sample file: copy it to variables.php (same folder) and fill in the database details.
 * variables.php is excluded from git (.gitignore): never commit it.
 */
$path = $_SERVER['DOCUMENT_ROOT'] . "/old";

$menu = $path . "/include/menu.php";
$header = $path . "/include/header.php";
$footer = $path . "/include/footer.php";
$header_not_home = $path . "/include/header_not_home.php";
$aside = $path . "/include/aside.php";
$header_articles = $path . "/include/header_articles.php";

$localhost_db = "DB_HOST";
$username_db = "DB_USER";
$password_db = "DB_PASSWORD";
$database_db = "DB_NAME";

$database_wordoftheday_api = "DB_NAME_WORDOFTHEDAY_API";
$database_quiz_nuoto_api = "DB_NAME_QUIZ_NUOTO_API";
$database_commonvoice_api = "DB_NAME_COMMONVOICE_API";
$database_easyrecipes_api = "DB_NAME_EASYRECIPES_API";

$database_emojiaddon_api = "DB_NAME_EMOJIADDON_API";
$database_savpdfviewer_api = "DB_NAME_SAVPDFVIEWER_API";

$database_savmrl_api = "DB_NAME_SAVMRL_API";

$root_separator = "/"; //this is to separate "root(s)" and "current page" (e.g. Home / Current Page)
$n_articles_per_page = 5; //5 default

$width_arrow = 6.987; //do not change
$height_arrow = 11.975; //do not change
$viewbox_arrow = 16; //do not change

$new_viewbox_arrow = 16;
$arrow_left = "<svg
                            width=\"" . ($width_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            height=\"" . ($height_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            viewBox=\"0 0 " . $viewbox_arrow . " " . $viewbox_arrow . "\">
                        <path
                                id=\"arrow-left\" class=\"arrow\"
                                d=\"m 5.5270972,7.9999913 9.8301268,-9.830481 A 2.2898037,2.2898861 0 0 0 12.119441,-5.0683886 L 0.67042348,6.3810418 a 2.2898037,2.2898861 0 0 0 0,3.2378989 L 12.119441,21.068371 a 2.2898037,2.2898861 0 0 0 3.237783,-3.237899 z\"
                                style=\"fill:#000000;fill-opacity:1;stroke-width:2.28984499\"/>
                    </svg>";
$arrow_right = "<svg
                            width=\"" . ($width_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            height=\"" . ($height_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            viewBox=\"0 0 " . $viewbox_arrow . " " . $viewbox_arrow . "\">
                        <path
                                d=\"M 10.47291,7.9999913 0.64278358,-1.8304897 A 2.2898037,2.2898861 0 0 1 3.8805666,-5.0683886 L 15.329584,6.3810418 a 2.2898037,2.2898861 0 0 1 0,3.2378989 L 3.8805666,21.068371 A 2.2898037,2.2898861 0 0 1 0.64278358,17.830472 Z\"
                                id=\"arrow-right\" class=\"arrow\"
                                style=\"fill:#000000;fill-opacity:1;stroke-width:2.28984499\"/>
                    </svg>";

$new_viewbox_arrow = 32;
$arrow_left_slider = "<svg
                            width=\"" . ($width_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            height=\"" . ($height_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            viewBox=\"0 0 " . $viewbox_arrow . " " . $viewbox_arrow . "\">
                        <path
                                id=\"arrow-left\" class=\"arrow\"
                                d=\"m 5.5270972,7.9999913 9.8301268,-9.830481 A 2.2898037,2.2898861 0 0 0 12.119441,-5.0683886 L 0.67042348,6.3810418 a 2.2898037,2.2898861 0 0 0 0,3.2378989 L 12.119441,21.068371 a 2.2898037,2.2898861 0 0 0 3.237783,-3.237899 z\"
                                style=\"fill:#000000;fill-opacity:1;stroke-width:2.28984499\"/>
                    </svg>";
$arrow_right_slider = "<svg
                            width=\"" . ($width_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            height=\"" . ($height_arrow * $new_viewbox_arrow / $viewbox_arrow) . "\"
                            viewBox=\"0 0 " . $viewbox_arrow . " " . $viewbox_arrow . "\">
                        <path
                                d=\"M 10.47291,7.9999913 0.64278358,-1.8304897 A 2.2898037,2.2898861 0 0 1 3.8805666,-5.0683886 L 15.329584,6.3810418 a 2.2898037,2.2898861 0 0 1 0,3.2378989 L 3.8805666,21.068371 A 2.2898037,2.2898861 0 0 1 0.64278358,17.830472 Z\"
                                id=\"arrow-right\" class=\"arrow\"
                                style=\"fill:#000000;fill-opacity:1;stroke-width:2.28984499\"/>
                    </svg>";

$work_in_progress = false; //do not change this

session_start();
$time = time();
$timeout_duration = 900;//in seconds (15 minutes = 15*60)
if (isset($_SESSION['LAST_ACTIVITY']) &&
    ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration && isset($_SESSION["user_id"]) && $_SESSION["user_id"] != 0) {
    session_start();
    session_regenerate_id();
    echo "<script>alert('Sessione scaduta')</script>";
    header("Location: /admin/?session_expired");
}
$_SESSION['LAST_ACTIVITY'] = $time;

if (!isset($_SESSION["user_id"])) {
    $_SESSION["user_id"] = 0;
}
if (!isset($_SESSION["user_permission"])) {
    $_SESSION["user_permission"] = 0;
}

function show_header()
{
    global $header;
    return include_once($header);
}

function show_menu()
{
    global $menu;
    return include_once($menu);
}

function show_header_not_home()
{
    global $header_not_home;
    return include_once($header_not_home);
}

function show_aside()
{
    global $aside;
    return include_once($aside);
}

function show_footer()
{
    global $footer;
    return include_once($footer);
}

function show_header_articles()
{
    global $header_articles;
    return include_once($header_articles);
}

function get_url($destination)
{
    if ($destination != "") {
        switch ($destination) {
            case "home":
                return "/old/";
                break;
            case "about-me":
                return "/old/about-me/";
                break;
            case "contact-me":
                return "/old/contact-me/";
                break;
            case "projects":
                return "/old/projects/";
                break;
            case "blog":
                return "/old/blog/";
                break;
            case "admin":
                return "/old/admin/";
                break;
            case "html-per-tutti":
                return "/old/htmlpertutti/";
                break;
            case "donate":
                return "/old/donate/";
                break;
        }
    }
    return "";
}

function get_redirect($destination)
{
    if ($destination != "") {
        switch ($destination) {
            case "common-voice-android":
                return "https://github.com/cv-project-app/common-voice-app";
                break;
            case "mozitabot":
                return "https://github.com/MozillaItalia/mozitahub_bot";
                break;
            case "accented-letters":
                return "https://github.com/Sav22999/accented-letters-addons";
                break;
            case "my-code-editor":
                return "https://github.com/Sav22999/mycodeeditor";
                break;
            case "nolimit-math":
                return "https://sav22999.github.io/project_nolimit_math/";
                break;
            case "mozita-myuserid":
                return "https://github.com/Sav22999/mozitamyuserid_bot";
                break;
            case "mozita-antispam":
                return "https://github.com/MozillaItalia/mozitaantispam_bot";
                break;
            case "mozita-l10n-addons":
                return "https://github.com/Sav22999/mozita-l10n-addons";
                break;
            case "vademecum":
                return "https://github.com/MozillaItalia/firefox-vademecum";
                break;
            case "share-backported":
                return "https://github.com/Mte90/Share-Backported";
                break;
            case "all-currencies":
                return "https://github.com/Sav22999/all-currencies";
                break;
            case "html-per-tutti":
                return "https://www.saveriomorelli.com/htmlpertutti/";
                break;
            case "emoji":
                return "https://www.emojiaddon.com/";
                break;
            case "limite":
                return "https://github.com/Sav22999/limite";
                break;
            case "sav-pdf-viewer-pro":
                return "https://www.savpdfviewer.com";
                break;
            case "mozita-website":
                return "https://www.mozillaitalia.org";
                break;
            case "word-of-the-day":
                return "https://github.com/Sav22999/word-of-the-day";
                break;
            case "websites-notes":
                return "https://www.notefox.eu";
                break;
            case "scrolly":
                return "https://github.com/Sav22999/scrolly";
                break;
            case "savmrl.it":
                return "https://savmrl.it";
                break;

            default:
                return $destination;
        }
    }
    return $destination;
}

function variables_permission_yes_or_not($permission_required)
{
    return $_SESSION["user_permission"] >= $permission_required;
}

function get_user_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function show_admin_bar()
{
    if ($_SESSION["user_id"] != 0) {
        global $current_page;
        global $article_id;

        echo '
<link rel="stylesheet" href="/old/style/admin.css"/>
<div id="header-admin-panel">
    <ul>
        <a href="/old/admin/">
            <li class="admin-panel-images" style="background-image: url(\'/images/icons/home.png\')"
                title="Home admin"></li>
        </a>';

        if (variables_permission_yes_or_not(10)) {
            echo '
        <a href="/old/commonvoice/statistics/">
            <li id="admin-statistics-cvapp" style="background-image: url(\'/images/icons/statistics-cvapp.png\');"
                title="Vedi statistiche Common Voice App" class="text-center">(1)</li>
        </a>';
            echo '
        <a href="/old/commonvoice/all-statistics/">
            <li id="admin-all-statistics-cvapp" style="background-image: url(\'/images/icons/all-statistics-cvapp.png\');"
                title="Vedi all-statistics Common Voice App" class="text-center">(2)</li>
        </a>';
        }

        if (variables_permission_yes_or_not(8)) {
            echo '
        <a href="/old/wordoftheday/en/">
            <li id="admin-wordoftheday-en" style="background-image: url(\'/images/icons/wotd-en.png\');"
                title="Inserisci nuova parola su Word of the Day: English"></li>
        </a>';
        }

        if (variables_permission_yes_or_not(10)) {
            echo '
        <a href="/old/wordoftheday/it/">
            <li id="admin-wordoftheday-it" style="background-image: url(\'/images/icons/wotd-it.png\');"
                title="Inserisci nuova parola su Word of the Day: Italian"></li>
        </a>';
        }


        if (variables_permission_yes_or_not(10)) {
            echo '
        <a href="/old/wordoftheday/fr/">
            <li id="admin-wordoftheday-fr" style="background-image: url(\'/images/icons/wotd-fr.png\');"
                title="Inserisci nuova parola su Word of the Day: French"></li>
        </a>';
        }

        if (variables_permission_yes_or_not(10)) {
            echo '
        <a href="/old/commonvoice/in-app/message/">
            <li id="admin-message-cvandroid" style="background-image: url(\'/images/projects/cv-project.png\');"
                title="Aggiungi un messaggio in-app"></li>
        </a>
        ';
        }

        if (variables_permission_yes_or_not(10)) {
            echo '
        <a href="/old/savpdfviewer/redirect/">
            <li id="admin-savpdfviewer-redirect" style="background-image: url(\'/images/projects/sav-pdf-viewer-pro.png\');"
                title="Genera redirect link"></li>
        </a>
        ';
        }

        if (variables_permission_yes_or_not(10)) {
            echo '
        <a href="/old/savmrl/">
            <li id="admin-savmrl-redirect" style="background-image: url(\'/images/projects/savmrl.png\');"
                title="Verifica ultimi 50 link"></li>
        </a>
        ';
        }

        echo '
        <!--<a href=""><li id="admin-file-manager" style="background-image: url(\'/images/icons/file-manager.png\');" title="File manager"></li></a>-->
        <!--<a href=""><li id="admin-statistics" style="background-image: url(\'/images/icons/statistics.png\');" title="Statistiche"></li></a>-->
        <a href="/old/admin/?logout">
            <li id="admin-logout" style="background-image: url(\'/images/icons/exit.png\');" title="Disconnetti"></li>
        </a>
    </ul>
</div>
';
    }
}

function check_language($language)
{
    $supported_languages = get_supported_languages();
    return in_array($language, $supported_languages, true);
}

function get_supported_languages()
{
    return array_keys(get_supported_languages_with_full_native_name());
}

function get_supported_languages_with_full_native_name()
{
    return array(
        "ab" => array("native" => "Аԥсуа", "percentage" => "0", "english" => "Abkhazian", "crowdin" => false),
        "ar" => array("native" => "العربية", "percentage" => "43", "english" => "Arabic", "crowdin" => true),
        "as" => array("native" => "অসমীয়া", "percentage" => "1", "english" => "Assamese", "crowdin" => true),
        "ast" => array("native" => "Asturianu", "percentage" => "0", "english" => "Asturian", "crowdin" => true),
        "az" => array("native" => "تۆرکجه", "percentage" => "100", "english" => "Azerbaijani", "crowdin" => true),
        "ba" => array("native" => "башҡорт теле", "percentage" => "90", "english" => "Bashkir", "crowdin" => true),
        "bas" => array("native" => "ɓasaá", "percentage" => "0", "english" => "Basaa", "crowdin" => false),
        "be" => array("native" => "беларуская мова", "percentage" => "25", "english" => "Belarusian", "crowdin" => true),
        "bn" => array("native" => "বাংলা", "percentage" => "43", "english" => "Bengali", "crowdin" => true),
        "bg" => array("native" => "български език", "percentage" => "100", "english" => "Bulgarian", "crowdin" => true),
        "br" => array("native" => "Brezhoneg", "percentage" => "0", "english" => "Breton", "crowdin" => true),
        "ca" => array("native" => "català", "percentage" => "100", "english" => "Catalan", "crowdin" => true),
        "ckb" => array("native" => "کوردی ناوەندی", "percentage" => "0", "english" => "Central Kurdish", "crowdin" => true),
        "cnh" => array("native" => "Laiholh (Hakha)", "percentage" => "0", "english" => "Hakha Chin", "crowdin" => false),
        "cs" => array("native" => "Čeština", "percentage" => "57", "english" => "Czech", "crowdin" => true),
        "cv" => array("native" => "Чӑвашла", "percentage" => "0", "english" => "Chuvash", "crowdin" => false),
        "cy" => array("native" => "Cymraeg", "percentage" => "54", "english" => "Welsh", "crowdin" => true),
        "da" => array("native" => "dansk", "percentage" => "0", "english" => "Danish", "crowdin" => true),
        "de" => array("native" => "Deutsch", "percentage" => "100", "english" => "German", "crowdin" => true),
        "dv" => array("native" => "ދިވެހި", "percentage" => "0", "english" => "Dhivehi", "crowdin" => true),
        "el" => array("native" => "Ελληνικά", "percentage" => "1", "english" => "Greek", "crowdin" => true),
        "en" => array("native" => "English", "percentage" => "100", "english" => "English", "crowdin" => true),
        "eo" => array("native" => "Esperanto", "percentage" => "19", "english" => "Esperanto", "crowdin" => true),
        "es" => array("native" => "Español", "percentage" => "100", "english" => "Spanish", "crowdin" => true),
        "et" => array("native" => "eesti", "percentage" => "8", "english" => "Estonian", "crowdin" => true),
        "eu" => array("native" => "Euskara", "percentage" => "37", "english" => "Basque", "crowdin" => true),
        "fa" => array("native" => "فارسی", "percentage" => "100", "english" => "Persian", "crowdin" => true),
        "fi" => array("native" => "suomi", "percentage" => "75", "english" => "Finnish", "crowdin" => true),
        "fr" => array("native" => "Français", "percentage" => "100", "english" => "French", "crowdin" => true),
        "fy-NL" => array("native" => "Frysk", "percentage" => "100", "english" => "Frisian", "crowdin" => true),
        "ga-IE" => array("native" => "Gaeilge", "percentage" => "32", "english" => "Irish", "crowdin" => true),
        "gl" => array("native" => "Galego", "percentage" => "59", "english" => "Galician", "crowdin" => true),
        "gn" => array("native" => "Avañe'ẽ", "percentage" => "0", "english" => "Guarani", "crowdin" => true),
        "ha" => array("native" => "هَوُسَ", "percentage" => "0", "english" => "Hausa", "crowdin" => true),
        "he" => array("native" => "עברית", "percentage" => "0", "english" => "Hebrew", "crowdin" => true),
        "hi" => array("native" => "हिन्दी", "percentage" => "82", "english" => "Hindi", "crowdin" => true),
        "hsb" => array("native" => "Hornjoserbšćina", "percentage" => "0", "english" => "Upper Sorbian", "crowdin" => false),
        "hu" => array("native" => "Magyar", "percentage" => "100", "english" => "Hungarian", "crowdin" => true),
        "hy-AM" => array("native" => "Հայերեն", "percentage" => "0", "english" => "Armenian", "crowdin" => true),
        "ia" => array("native" => "Interlingua", "percentage" => "100", "english" => "Interlingua ", "crowdin" => true),
        "id" => array("native" => "Bahasa Indonesia", "percentage" => "0", "english" => "Indonesian", "crowdin" => true),
        "ig" => array("native" => "Ásụ̀sụ́ Ìgbò", "percentage" => "0", "english" => "Igbo", "crowdin" => true),
        "it" => array("native" => "Italiano", "percentage" => "100", "english" => "Italian", "crowdin" => true),
        "ja" => array("native" => "日本語", "percentage" => "100", "english" => "Japanese", "crowdin" => true),
        "ka" => array("native" => "ქართული", "percentage" => "100", "english" => "Georgian", "crowdin" => true),
        "kab" => array("native" => "Taqbaylit", "percentage" => "85", "english" => "Kabyle", "crowdin" => true),
        "kk" => array("native" => "қазақ тілі", "percentage" => "0", "english" => "Kazakh", "crowdin" => true),
        "kmr" => array("native" => "Kurdî (Kurmancî)", "percentage" => "0", "english" => "Kurmanji Kurdish", "crowdin" => false),
        "ky" => array("native" => "Кыргызча", "percentage" => "0", "english" => "Kirghiz", "crowdin" => false),
        "lg" => array("native" => "Luganda", "percentage" => "0", "english" => "Luganda", "crowdin" => true),
        "lt" => array("native" => "Lietuvių", "percentage" => "56", "english" => "Lithuanian", "crowdin" => true),
        "lv" => array("native" => "Latviešu", "percentage" => "100", "english" => "Latvian", "crowdin" => true),
        "mdf" => array("native" => "Мокшень кяль", "percentage" => "0", "english" => "Moksha", "crowdin" => false),
        "mhr" => array("native" => "Марий", "percentage" => "0", "english" => "Mari", "crowdin" => false),
        "mrj" => array("native" => "Кырык мары", "percentage" => "0", "english" => "Hill Mari", "crowdin" => false),
        "mk" => array("native" => "македонски јазик", "percentage" => "0", "english" => "Macedonian", "crowdin" => true),
        "ml" => array("native" => "മലയാളം", "percentage" => "0", "english" => "Malayalam", "crowdin" => true),
        "mr" => array("native" => "मराठी", "percentage" => "0", "english" => "Marathi", "crowdin" => true),
        "mn" => array("native" => "Монгол хэл", "percentage" => "0", "english" => "Mongolian", "crowdin" => true),
        "mt" => array("native" => "Malti", "percentage" => "0", "english" => "Maltese", "crowdin" => true),
        "myv" => array("native" => "Эрзякс", "percentage" => "0", "english" => "Erzya", "crowdin" => false),
        "nan-tw" => array("native" => "臺語", "percentage" => "0", "english" => "Taiwanese Hokkien", "crowdin" => false),
        "ne-NP" => array("native" => "नेपाली", "percentage" => "0", "english" => "Nepali", "crowdin" => true),
        "nl" => array("native" => "Nederlands", "percentage" => "100", "english" => "Dutch", "crowdin" => true),
        "nn-NO" => array("native" => "Norsk (nynorsk)", "percentage" => "0", "english" => "Nynorsk", "crowdin" => true),
        "or" => array("native" => "ଓଡ଼ିଆ", "percentage" => "7", "english" => "Odia", "crowdin" => true),
        "os" => array("native" => "ирон ӕвзаг", "percentage" => "0", "english" => "Ossetian", "crowdin" => false),
        "pa-IN" => array("native" => "ਪੰਜਾਬੀ", "percentage" => "0", "english" => "Punjabi", "crowdin" => true),
        "pl" => array("native" => "polski", "percentage" => "100", "english" => "Polish", "crowdin" => true),
        "pt" => array("native" => "Português", "percentage" => "100", "english" => "Portuguese", "crowdin" => true),
        "quy" => array("native" => "Quechua Chanka", "percentage" => "0", "english" => "Ayacucho Quechua", "crowdin" => false),
        "rm-sursilv" => array("native" => "romontsch sursilvan", "percentage" => "0", "english" => "Sursilvan", "crowdin" => false),
        "rm-vallader" => array("native" => "Rumantsch Vallader", "percentage" => "0", "english" => "Vallader", "crowdin" => false),
        "ro" => array("native" => "Română", "percentage" => "45", "english" => "Romanian", "crowdin" => true),
        "ru" => array("native" => "Русский", "percentage" => "87", "english" => "Russian", "crowdin" => true),
        "rw" => array("native" => "Ikinyarwanda", "percentage" => "0", "english" => "Kinyarwanda", "crowdin" => false),
        "sah" => array("native" => "Саха тыла", "percentage" => "0", "english" => "Yakut", "crowdin" => false),
        "sat" => array("native" => "ᱥᱟᱱᱛᱟᱲᱤ (ᱚᱞ ᱪᱤᱠᱤ)", "percentage" => "0", "english" => "Santali", "crowdin" => true),
        "sc" => array("native" => "Sardu", "percentage" => "0", "english" => "Sardinian language", "crowdin" => true),
        "sk" => array("native" => "slovenčina", "percentage" => "59", "english" => "Slovak", "crowdin" => true),
        "sl" => array("native" => "slovenščina", "percentage" => "0", "english" => "Slovenian", "crowdin" => true),
        "sr" => array("native" => "српски језик", "percentage" => "0", "english" => "Serbian", "crowdin" => true),
        "sv-SE" => array("native" => "Svenska", "percentage" => "98", "english" => "Swedish", "crowdin" => true),
        "sw" => array("native" => "Swahili", "percentage" => "0", "english" => "Kiswahili", "crowdin" => true),
        "ta" => array("native" => "தமிழ்", "percentage" => "27", "english" => "Tamil", "crowdin" => true),
        "th" => array("native" => "ไทย", "percentage" => "0", "english" => "Thai", "crowdin" => false),
        "tig" => array("native" => "ትግረ", "percentage" => "0", "english" => "Tigre", "crowdin" => true),
        "tw" => array("native" => "Twi", "percentage" => "0", "english" => "Twi", "crowdin" => false),
        "skr" => array("native" => "سرائیکی", "percentage" => "0", "english" => "Saraiki", "crowdin" => false),
        "tok" => array("native" => "Toki Pona", "percentage" => "0", "english" => "Toki Pona", "crowdin" => false),
        "ti" => array("native" => "ትግርኛ", "percentage" => "0", "english" => "Tigrinya", "crowdin" => true),
        "tr" => array("native" => "Türkçe", "percentage" => "87", "english" => "Turkish", "crowdin" => true),
        "tt" => array("native" => "Татарча", "percentage" => "92", "english" => "Tatar", "crowdin" => false),
        "ug" => array("native" => "ئۇيغۇرچە‎", "percentage" => "0", "english" => "Uyghur", "crowdin" => true),
        "uk" => array("native" => "Українська", "percentage" => "83", "english" => "Ukrainian", "crowdin" => true),
        "ur" => array("native" => "اردو", "percentage" => "0", "english" => "Urdu", "crowdin" => true),
        "uz" => array("native" => "O'zbekcha", "percentage" => "100", "english" => "Uzbek", "crowdin" => true),
        "vi" => array("native" => "Việt", "percentage" => "0", "english" => "Vietnamese", "crowdin" => true),
        "vot" => array("native" => "vad̕d̕a", "percentage" => "0", "english" => "Votic", "crowdin" => false),
        "yue" => array("native" => "粵語", "percentage" => "0", "english" => "Yue Chinese", "crowdin" => false),
        "zh-CN" => array("native" => "汉语（中国大陆）", "percentage" => "72", "english" => "Simplified Chinese", "crowdin" => true),
        "zh-HK" => array("native" => "中文（香港）", "percentage" => "0", "english" => "Chinese (Hong Kong)", "crowdin" => true),
        "zh-TW" => array("native" => "華語（台灣）", "percentage" => "0", "english" => "Chinese (Taiwan)", "crowdin" => true));
}

function getGoodString($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirectTo($url, $seconds)
{
    header("Refresh:$seconds;url=$url");
}

?>