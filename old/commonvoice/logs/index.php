<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>Logs | CV Project &#8211; Saverio Morelli</title>
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

<div id="body" class="font-family-basic text-black-color">
    <div class="app-usage-statistics-data">
        <h1 class="no-padding no-margin h1-center">Logs</h1>
        <hr class="hr-center">
        <div class="text-center text-white-color padding-10 margin-25">
            <p class="center-content background-gradient-primary padding-10 border-radius-default" id="message-box">
                Loading...
            </p>
        </div>
        <div class="statistics-data-table" id="statistics-data-table-2">
        </div>
        <script>
            $(document).ready(function () {
                load_data();
            });

            function load_data() {
                $.ajax({
                    url: "/api/common-voice-android/v2/logs/get/?limit=10",
                    type: 'get',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response != null) {
                            let htmlToShow = "";
                            htmlToShow = '<table><tr id="title"><th>Id</th><th>Date</th><th>Language</th><th>Version</th><th>Source</th><th>Logged</th><th>Error</th><th>Tag</th><th>Stack Trace</th><th>Additional logs</th></tr>';
                            for (let response_temp in response) {
                                htmlToShow += '<tr><td>' + response[response_temp]["general"]["id"] + '</td><td>' + response[response_temp]["general"]["logDate"] + '</td><td>' + response[response_temp]["general"]["language"] + '</td><td>' + response[response_temp]["general"]["version"] + '</td><td>' + response[response_temp]["general"]["source"] + '</td><td>' + Boolean(response[response_temp]["general"]["logged"]) + '</td><td>' + response[response_temp]["log"]["errorLevel"] + '</td><td>' + response[response_temp]["log"]["tag"] + '</td><td>' + response[response_temp]["log"]["stackTrace"] + '</td><td>' + response[response_temp]["log"]["additionalLogs"] + '</td>';
                            }
                            htmlToShow += '</table></div>';

                            $(".statistics-data-table").html(htmlToShow);
                            $("#message-box").css("display", "none");
                        } else {
                            //null
                            $(".statistics-data-table").html('<div>An error occurred.</div>');
                            $("#message-box").css("display", "block");
                        }
                    },
                    error: function () {
                        //error
                        $(".statistics-data-table").html('<div>An error occurred.</div>');
                        $("#message-box").css("display", "block");
                    }
                });
            }
        </script>
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