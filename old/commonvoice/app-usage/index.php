<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>App usage | CV Project &#8211; Saverio Morelli</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/commonvoice/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

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
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-25 padding-top-25 padding-bottom-25 text-left font-family-basic"
     id="message-box">
    <p class="center-content padding-default" id="message-box-text">
        Loading...
    </p>
</div>
<div class="font-family-basic text-black-color background-transparent-color center-content">
    <form action="./" method="get">
        <select name="language" onchange="this.form.submit()" id="search">
            <?php
            $language = "all";
            if (isset($_GET["language"])) {
                $language = $_GET["language"];
            }

            $languages = get_supported_languages_with_full_native_name();
            echo '<option value="all">All languages</option>';
            foreach ($languages as $code => $details) {
                $selected = "";
                if ($language == $code) $selected = " selected";
                echo '<option value="' . $code . '"' . $selected . '>' . $details["native"] . '</option>';
            }
            ?>
        </select>
    </form>
</div>

<div id="body" class="font-family-basic text-black-color">
    <?php
    $filter = "today";
    $year = 0;
    if ($_GET["filter"]) {
        if ($_GET["filter"] == "always") {
            $filter = "always";
        } else if ($_GET["filter"] == "yesterday") {
            $filter = "yesterday";
        } else if ($_GET["filter"] == "year" && isset($_GET["year"]) && is_numeric($_GET["year"])) {
            $filter = "year";
            $year = $_GET["year"];
        }
    }
    ?>


    <div id="filter-statistics-cvapp" class="filter-bar filter-bar-white app-usage-statistics-data">
        <button onclick="location.href='./?language=<?php echo $language; ?>&filter=today'" id='today-view'
                class='filter-bar-button filter-4 filter-bar-button-white filter-bar-button-<?php if ($filter == "today") {
                    echo "selected";
                } ?>'>
            Today
        </button>
        <button onclick="location.href='./?language=<?php echo $language; ?>&filter=yesterday'" id='always-view'
                class='filter-bar-button filter-4 filter-bar-button-white filter-bar-button-<?php if ($filter == "yesterday") {
                    echo "selected";
                } ?>'>
            Yesterday
        </button>
        <button onclick="location.href='./?language=<?php echo $language; ?>&filter=year&year=<?php echo date("Y"); ?>'"
                id='always-view'
                class='filter-bar-button filter-4 filter-bar-button-white filter-bar-button-<?php if ($filter == "year" && $year == date("Y")) {
                    echo "selected";
                } ?>'>
            This year
        </button>
        <button onclick="location.href='./?language=<?php echo $language; ?>&filter=always'" id='always-view'
                class='filter-bar-button filter-4 filter-bar-button-white filter-bar-button-<?php if ($filter == "always") {
                    echo "selected";
                } ?>'>
            Always
        </button>
    </div>

    <script>
        $(document).ready(function () {
            load_data();
        });

        function load_data() {
            $.ajax({
                url: "/api/common-voice-android/v2/app-usage/get/details/?language=<?php echo $language; ?>&filter=<?php echo $filter; ?>&year=<?php echo $year; ?>",
                type: 'get',
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response != null) {
                        <?php
                        if ($filter == "year") echo "$('.date-data').html('${year}');";
                        else echo "$('.date-data').html('${filter}');";
                        ?>

                        let listen = response["<?php echo $language; ?>"]["listen"];
                        let speak = response["<?php echo $language; ?>"]["speak"];

                        let listen_validated = listen["validated"]["all"];
                        let listen_accepted = listen["accepted"]["all"];
                        let listen_rejected = listen["rejected"]["all"];
                        let listen_reported = listen["reported"]["all"];
                        let speak_sent = speak["sent"]["all"];
                        let speak_reported = speak["reported"]["all"];

                        let total_listen = listen_validated;
                        let total_speak = speak_sent + speak_reported;

                        if (total_listen > 0) {
                            $("#clips-val-data").html((listen_validated).toLocaleString("en"));
                            $("#clips-acc-data").html((listen_accepted).toLocaleString("en") + " (" + Math.round((100 * listen_accepted) / total_listen) + "%)");
                            $("#clips-rej-data").html((listen_rejected).toLocaleString("en") + " (" + Math.round((100 * listen_rejected) / total_listen) + "%)");
                            $("#clips-rep-data").html((listen_reported).toLocaleString("en") + " (" + Math.round((100 * listen_reported) / total_listen) + "%)");
                        } else {
                            $("#clips-val-data").html((listen_validated).toLocaleString("en"));
                            $("#clips-acc-data").html((listen_accepted).toLocaleString("en") + " (0%)");
                            $("#clips-rej-data").html((listen_rejected).toLocaleString("en") + " (0%)");
                            $("#clips-rep-data").html((listen_reported).toLocaleString("en") + " (0%)");
                        }
                        if (total_speak > 0) {
                            $("#recor-sen-data").html((speak_sent).toLocaleString("en") + " (" + Math.round((100 * speak_sent) / total_speak) + "%)");
                            $("#sente-rep-data").html((speak_reported).toLocaleString("en") + " (" + Math.round((100 * speak_reported) / total_speak) + "%)");
                        } else {
                            $("#recor-sen-data").html((speak_sent).toLocaleString("en") + " (0%)");
                            $("#sente-rep-data").html((speak_reported).toLocaleString("en") + " (0%)");
                        }

                        $("#message-box").removeClass("background-red-color");
                        $("#message-box").addClass("background-primary-color");
                        $("#message-box").css("display", "none");
                    } else {
                        //null
                        $(".app-usage-statistics-data").css("display", "none");
                        $("#message-box").css("display", "none");
                        if ($("#search").val() != "") {
                            $("#message-box-text").html("The selected language doesn't exist or doesn't have data.");
                            $("#message-box").removeClass("background-primary-color");
                            $("#message-box").addClass("background-red-color");
                            $("#message-box").css("display", "block");
                        }
                    }
                },
                error: function () {
                    //error
                    $("#message-box-text").html("The selected language doesn't exist or doesn't have data.");
                    $("#message-box").css("display", "block");
                    $("#message-box").removeClass("background-primary-color");
                    $("#message-box").addClass("background-red-color");
                }
            });
        }
    </script>

    <div class="app-usage-statistics-data">
        <h1 class="no-padding no-margin h1-center">Clips</h1>
        <hr class="hr-center">
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>Date</th>
                    <th>Clips validated</th>
                    <th>Clips accepted</th>
                    <th>Clips rejected</th>
                    <th>Clips reported</th>
                </tr>
                <tr>
                    <td class="date-data">···</td>
                    <td id="clips-val-data">···</td>
                    <td id="clips-acc-data">···</td>
                    <td id="clips-rej-data">···</td>
                    <td id="clips-rep-data">···</td>
                </tr>
            </table>
        </div>

        <h1 class="no-padding no-margin h1-center">Recordings</h1>
        <hr class="hr-center">
        <div class="statistics-data-table">
            <table>
                <tr id="title">
                    <th>Date</th>
                    <th>Recordings sent</th>
                    <th>Sentences reported</th>
                </tr>
                <tr>
                    <td class="date-data">···</td>
                    <td id="recor-sen-data">···</td>
                    <td id="sente-rep-data">···</td>
                </tr>
            </table>
        </div>
    </div>
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