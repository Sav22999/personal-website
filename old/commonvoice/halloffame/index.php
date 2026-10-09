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
    <a href="/old/commonvoice">
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


<div id="body" class="font-family-basic text-black-color">
    <div class="width100 background-transparent-color text-black-color border-radius-0 padding-10">
        <script>
            load_data();

            function load_data() {
                $.ajax({
                    url: "/api/common-voice-android/v2/hall-of-fame/get/",
                    type: 'get',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response != null) {

                            if (response["<?php echo date('Y-W'); ?>"] != undefined) {
                                let topContributed = response["<?php echo date('Y-W'); ?>"]["top-contributed"];
                                let topTrending = response["<?php echo date('Y-W'); ?>"]["top-trending"];

                                if (topContributed["language_name"] != undefined && topContributed["language_code"] != undefined) {
                                    $("#lang-top-contributed").html(topContributed["language_name"] + " (" + topContributed["language_code"] + ")");
                                } else {
                                    $("#lang-top-contributed").html("?");
                                }
                                if (topTrending["language_name"] != undefined && topTrending["language_code"] != undefined) {
                                    $("#lang-top-trending").html(topTrending["language_name"] + " (" + topTrending["language_code"] + ")");
                                } else {
                                    $("#lang-top-trending").html("?");
                                }

                                if (topContributed["link"] != undefined && topContributed["link"] != "" && topContributed["link"] != null) {
                                    $("#graph-top-contributed").attr("src", topContributed["link"]);
                                } else {
                                    $("#graph-top-contributed").attr("src", "");
                                }
                                if (topTrending["link"] != undefined && topTrending["link"] != "" && topTrending["link"] != null) {
                                    $("#graph-top-trending").attr("src", topTrending["link"]);
                                } else {
                                    $("#graph-top-trending").attr("src", "");
                                }
                            } else {
                                $("#lang-top-contributed").html("?");
                                $("#lang-top-trending").html("?");
                                $("#graph-top-contributed").attr("src", "");
                                $("#graph-top-trending").attr("src", "");
                            }
                        } else {
                            $("#lang-top-contributed").html("?");
                            $("#lang-top-trending").html("?");
                            $("#graph-top-contributed").attr("src", "");
                            $("#graph-top-trending").attr("src", "");
                        }
                    },
                    error: function () {
                        //error
                        $("#lang-top-contributed").html("?");
                        $("#lang-top-trending").html("?");
                    }
                });
            }
        </script>

        <div class="rank center-content background-transparent-color text-black-color">
            <h1 class="no-padding no-margin h1-center">Languages of the week</h1>
            <br>
            <div class="clearfix">
                <div class="projects-div clearfix">
                    <div class="project project-50 background-white-color text-black-color cursor-default">
                        <h1 class="project-h1">Top contributed<sup><small>*</small></sup></h1>
                        <h2 class="project-h2 font-size-30 margin-top30 margin-bottom30" id="lang-top-contributed">
                            •••</h2>
                        <img id="graph-top-contributed" width="90%" height="auto"/>
                    </div>
                    <div class="project project-50 background-white-color text-black-color text-center cursor-default">
                        <h1 class="project-h1">Top trending<sup><small>#</small></sup></h1>
                        <h2 class="project-h2 font-size-30 margin-top30 margin-bottom30" id="lang-top-trending">•••</h2>
                        <img id="graph-top-trending" width="90%" height="auto"/>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="center-content text-left padding-10">
            <div class="margin-10">
                <sup><small>*</small></sup> The language with the most added recording time
                <br>
                <sup><small>#</small></sup> The language with the highest percentage of new contributions
                <br>
                (the timeframe is always Friday night UTC plus 7 days)
            </div>
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