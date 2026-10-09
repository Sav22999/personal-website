<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>CV Project &#8211; Saverio Morelli</title>
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
<div class="font-family-basic text-black-color background-transparent-color center-content">
    <form action="./" method="get">
        <input type="search" name="userid" placeholder="Type your in-app CV Project username" class="font-family-basic"
               id="search"
               value="<?php if (isset($_GET["userid"])) {
                   echo $_GET["userid"];
               } ?>"/>
    </form>
</div>
<div id="body" class="font-family-basic text-black-color">
    <?php
    $userid = "";
    if (isset($_GET["userid"])) {
        $userid = $_GET["userid"];
    }
    $today = true;
    $otherbutton = false;
    $filter = "today";
    $filter_to_use = "start_date=" . $filter;
    $number_of_buttons = 2;
    $other_button_name = "";
    $other_button_link = "";
    if (isset($_GET["filter"]) && $_GET["filter"] == "always") {
        $today = false;
        $otherbutton = false;
        $filter = "always";
        $filter_to_use = "start_date=" . $filter;
    } else if (isset($_GET["filter"]) && ($_GET["filter"] == "cvcontest" || $_GET["filter"] == "mozita-contest-apr-2021")) {
        $today = false;
        $otherbutton = true;
        if ($_GET["filter"] == "mozita-contest-apr-2021") {
            $other_button_name = "<span class=\"font-family-twemoji\">🇮🇹</span> Contest: CV Project";
            $other_button_link = $_GET["filter"];
            $filter = "1➞30 Apr 2021";
            $filter_to_use = "start_date=2021-04-01&end_date=2021-04-30";
        } else if ($_GET["filter"] == "cvcontest") {
            $other_button_name = "<span class=\"font-family-twemoji\">🇮🇹</span> Contest: CV Android";
            $other_button_link = $_GET["filter"];
            $filter = "14➞28 Feb 2021";
            $filter_to_use = "start_date=2021-02-14&end_date=2021-02-28";
        }
        $number_of_buttons = 3;
    } else if (isset($_GET["filter"]) && $_GET["filter"] == "date" && isset($_GET["start_date"]) && isAGoodDate($_GET["start_date"])) {
        $today = false;
        $otherbutton = true;
        $other_button_name = $_GET["start_date"] . " ➞ " . date("Y-m-d");
        $other_button_link = "filter=date&start_date=" . $_GET["start_date"];
        $filter = $_GET["start_date"] . " ➞ " . date("Y-m-d");
        $filter_to_use = "start_date=" . $_GET["start_date"];
        if (isset($_GET["end_date"]) && isAGoodDate($_GET["end_date"])) {
            $other_button_name = $_GET["start_date"] . " ➞ " . $_GET["end_date"];
            $other_button_link = "filter=date&start_date=" . $_GET["start_date"] . "&end_date=" . $_GET["end_date"];
            $filter = $_GET["start_date"] . " ➞ " . $_GET["end_date"];
            $filter_to_use = "start_date=" . $_GET["start_date"] . "&end_date=" . $_GET["end_date"];
        }
        $number_of_buttons = 3;
    }

    function isAGoodDate($date)
    {
        $date_tmp = explode("-", $date);
        if (count($date_tmp) == 3) {
            //if there are exactly 3 elements (year, month and day)
            if (is_numeric($date_tmp[0]) && $date_tmp[0] >= 1900 && $date_tmp[0] <= date('Y')) {
                //year
                if (is_numeric($date_tmp[1]) && $date_tmp[1] >= 1 && $date_tmp[1] <= 12) {
                    //month
                    if (is_numeric($date_tmp[2]) && $date_tmp[2] >= 1 && $date_tmp[2] <= 31) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    ?>

    <div id="filter-statistics-cvapp" class="filter-bar filter-bar-white app-usage-statistics-data">
        <button onclick="location.href='./?userid=<?php echo $userid; ?>&filter=today'" id='today-view'
                class='filter-bar-button filter-<?php echo $number_of_buttons; ?> filter-bar-button-white filter-bar-button-<?php if ($today && !$otherbutton) {
                    echo "selected";
                } ?>'>
            Today
        </button>
        <button onclick="location.href='./?userid=<?php echo $userid; ?>&filter=always'" id='always-view'
                class='filter-bar-button filter-<?php echo $number_of_buttons; ?> filter-bar-button-white filter-bar-button-<?php if (!$today && !$otherbutton) {
                    echo "selected";
                } ?>'>
            Always
        </button>
        <?php if ($otherbutton) { ?>
            <button onclick="location.href='./?userid=<?php echo $userid; ?>&filter=<?php echo $other_button_link; ?>'"
                    id='cvcontest-view'
                    class='filter-bar-button filter-<?php echo $number_of_buttons; ?> filter-bar-button-white filter-bar-button-<?php if (!$today && $otherbutton) {
                        echo "selected";
                    } ?>'>
                <?php echo $other_button_name; ?>
            </button>
        <?php } ?>
    </div>

    <script>
        $(document).ready(function () {
            load_data();
        });

        function load_data() {
            $.ajax({
                url: "/api/common-voice-android/v2/app-usage/get/user/?id=<?php echo $userid; ?>&<?php echo $filter_to_use; ?>",
                type: 'get',
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response != null) {
                        $(".date-data").html("<?php echo $filter; ?>");

                        let listen = response["user-stats"]["listen"];
                        let speak = response["user-stats"]["speak"];

                        let total_listen = listen["validated"];
                        let total_speak = speak["sent"] + speak["reported"];

                        if (total_listen > 0) {
                            $("#clips-val-data").html(listen["validated"]);
                            $("#clips-acc-data").html((listen["accepted"]).toLocaleString("en") + " (" + Math.round((100 * listen["accepted"]) / total_listen) + "%)");
                            $("#clips-rej-data").html((listen["rejected"]).toLocaleString("en") + " (" + Math.round((100 * listen["rejected"]) / total_listen) + "%)");
                            $("#clips-rep-data").html((listen["reported"]).toLocaleString("en") + " (" + Math.round((100 * listen["reported"]) / total_listen) + "%)");
                        } else {
                            $("#clips-val-data").html((listen["validated"]).toLocaleString("en"));
                            $("#clips-acc-data").html((listen["accepted"]).toLocaleString("en") + " (0%)");
                            $("#clips-rej-data").html((listen["rejected"]).toLocaleString("en") + " (0%)");
                            $("#clips-rep-data").html((listen["reported"]).toLocaleString("en") + " (0%)");
                        }
                        if (total_speak > 0) {
                            $("#recor-sen-data").html((speak["sent"]).toLocaleString("en") + " (" + Math.round((100 * speak["sent"]) / total_speak) + "%)");
                            $("#sente-rep-data").html((speak["reported"]).toLocaleString("en") + " (" + Math.round((100 * speak["reported"]) / total_speak) + "%)");
                        } else {
                            $("#recor-sen-data").html((speak["sent"]).toLocaleString("en") + " (0%)");
                            $("#sente-rep-data").html((speak["reported"]).toLocaleString("en") + " (0%)");
                        }

                        $("#message-box").css("display", "none");
                        $("#message-box").removeClass("background-red-color");
                        $("#message-box").addClass("background-primary-color");
                    } else {
                        //null
                        $(".app-usage-statistics-data").css("display", "none");
                        $("#message-box").css("display", "none");
                        if ($("#search").val() != "") {
                            $("#message-box").html("The selected user doesn't exist.<br>Remember you need to specify the CV Project username (it starts with \"User\" and it ends with \"::CVAppSav\").<br>You can find it in Settings > Advanced > Show string...");
                            $("#message-box").css("display", "block");
                            $("#message-box").addClass("background-red-color");
                            $("#message-box").removeClass("background-primary-color");
                        }
                    }
                },
                error: function () {
                    //error
                    $("#message-box").html("The selected user doesn't exist.<br>Remember you need to specify the CV Project username (it starts with \"User\" and it ends with \"::CVAppSav\").<br>You can find it in Settings > Advanced > Show string...");
                    $("#message-box").css("display", "block");
                    $("#message-box").addClass("background-red-color");
                    $("#message-box").removeClass("background-primary-color");
                }
            });
        }
    </script>

    <div class="text-center text-white-color padding-10 margin-25">
        <p class="center-content background-primary-color padding-10 border-radius-default" id="message-box">
            Loading...
        </p>
    </div>

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
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 font-family-basic">
    <div class="center-content padding-default">
        You can find your username in <span
                class="border-radius-default background-secondary-color text-white-color font-family-source">Settings</span>
        >
        <span class="border-radius-default background-secondary-color text-white-color font-family-source">Advanced</span>
        > <span class="border-radius-default background-secondary-color text-white-color font-family-source">Show the string which identifies me inside the app</span>
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