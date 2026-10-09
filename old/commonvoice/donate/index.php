<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>CV Project &#8211; Saverio Morelli</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/commonvoice/header2.php"); ?>
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
    <div class="rank center-content background-transparent-color text-black-color">
        <h1 class="no-padding no-margin h1-center">Buy me a coffee on:</h1>
        <hr class="hr-center"/>
        <div class="clearfix margin-top30">
            <div class="projects-div clearfix">
                <div class="project project-no-min-height project-50 background-white-color text-black-color padding-20"
                     onclick="location.href='https://liberapay.com/Sav22999/'">
                    <img src="/old/images/icons/liberapay.png" class="width30 height30">
                    <h1 class="project-h1 font-size-30">LiberaPay</h1>
                </div>
                <div class="project project-no-min-height project-50 background-white-color text-black-color padding-20"
                     onclick="location.href='https://www.paypal.me/saveriomorelli'">
                    <img src="/old/images/icons/paypal.png" class="width30 height30">
                    <h1 class="project-h1 font-size-30">PayPal</h1>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->