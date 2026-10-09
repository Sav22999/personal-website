<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>CV Project &#8211; Saverio Morelli</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/commonvoice/header.php"); ?>
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
<div id="body" class="font-family-basic">
    <div class="width100 background-transparent-color text-black-color border-radius-0">
        <div class="center-content center">
            <div class="clearfix">
                <div class="projects-div clearfix">
                    <div class="section-release project-100 background-transparent-color text-black-color">
                        <h1 class="h1-center">Offline mode</h1>
                        <hr class="hr-center"/>
                        <p class="center-content">
                            <small>
                                Last update:
                                <?php
                                $path = $_SERVER['DOCUMENT_ROOT'] . "/old";
                                echo date("Y-m-d", filemtime($path . "/commonvoice/offline-mode/index.php"));
                                ?>
                            </small>
                            <br>
                            <br>
                            The offline mode is the feature which permits you to contribute to Common Voice also when
                            you don't have an Internet connection.
                            <br>
                            When offline mode is turned on (default setting) you can validate up to 50 clips and record
                            up to 50 sentences. You can skip or report them as well.
                            <br>
                            Since the 2.3.6.6 release you can customise this number in <code
                                    class="code background-darkgray">Settings</code>
                            &#129034; <code class="code background-darkgray">Offline mode</code> (50-500).
                            <br>
                            <br>
                            <b>How does it work?</b>
                            <br>
                            The app download 50 clips and 50 sentences every time on your device and it sends to the
                            Mozilla server just when you have an Internet connection again.
                            <br>
                            <br>
                            <b>How can you enable or disable the Offline mode?</b>
                            <br>
                            Since the 2.2 you are able to disable this feature, just go to Settings, then Offline mode
                            and you can turn on (green) or off (red) the toggle.
                            <br>
                            <br>
                            <b>How can you know if you are using the Offline mode or the "normal mode"?</b>
                            <br>
                            In Listen and Speak you will see at the top-left corner the "no-wifi" icon. It's the Offline
                            mode icon, which indicates you are using it. If you tap that icon you will be able to see
                            also the clips or recordings residual.
                        </p>
                    </div>
                </div>
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