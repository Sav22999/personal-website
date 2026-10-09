<?php
$dominio = $_SERVER['DOCUMENT_ROOT'] . "commonvoice";

$working_in_progress = false; // Impostare "true" quando si stanno facendo modifiche al sito -> non sarà visibile agli utenti

session_start();
?>
<link rel="stylesheet" href="/old/style/site.css"/>
<link rel="stylesheet" href="/old/commonvoice/css/colori.css"/>
<link rel="stylesheet" href="/old/commonvoice/css/site.css"/>
<link rel="icon" href="/old/images/projects/cv-project.png"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://unpkg.com/twemoji@13.1.0/dist/twemoji.min.js"></script>
<script src="/old/include/variables.php"></script>
<script src="/old/commonvoice/js/site.js"></script>
<meta http-equiv="content-type" content="text/html; charset=UTF-16">
<meta name="viewport" content="width=device-width, initial-scale=0.8"/>

<meta property="og:locale" content="it_IT"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="Donate your voice: CV Project"/>
<meta property="og:description"
      content="Donate your voice: CV Project – This app permits you to contribute to Common Voice from your smartphone. The app is NOT developed by Mozilla."/>
<meta property="og:url" content="https://www.saveriomorelli.com/commonvoice/"/>
<meta property="og:site_name" content="Saverio Morelli"/>
<meta property="og:image" content="https://www.saveriomorelli.com/commonvoice/img/opengraph/banner.png"/>
<meta property="og:image:secure_url" content="https://www.saveriomorelli.com/commonvoice/img/opengraph/banner.png"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:description"
      content="Donate your voice: CV Project – This app permits you to contribute to Common Voice from your smartphone. The app is NOT developed by Mozilla."/>
<meta name="twitter:title" content="Donate your voice: CV Project"/>
<meta name="twitter:site" content="@Sav22999"/>
<meta name="twitter:image" content="https://www.saveriomorelli.com/commonvoice/img/opengraph/banner.png"/>
<meta name="twitter:creator" content="@Sav22999"/>

<!-- Global site tag (gtag.js) - Google Analytics -->
<!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153389726-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-153389726-1');
</script>
<script data-ad-client="ca-pub-4441008333572114" async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->

<style>
    #frase_attesa_lunga_loading {
        display: none;
        position: relative;
        margin-top: 36%;
        color: white;
        background-color: transparent;
        font-size: 20px;
    }

    #working_in_progress {
        display: block;
        position: fixed;
        top: 0px;
        bottom: 0px;
        left: 0px;
        right: 0px;
        background-color: #222222;
        background-image: url("/old/images/working.png");
        background-position: center center;
        background-size: auto 15%;
        background-repeat: no-repeat;
        z-index: 999999;
    }

    #frase_attesa_lunga_working {
        display: block;
        position: relative;
        margin-top: 36%;
        color: white;
        background-color: transparent;
        font-size: 20px;
    }
</style>
<div id="loading">
    <center>
        <div id="frase_attesa_lunga_loading"></div>
    </center>
</div>
<div id="working_in_progress">
    <center>
        <div id="frase_attesa_lunga_working">Work in progress...<br>We're updating website and server.<br>Website will
            be online again as soon as.
        </div>
    </center>
</div>

<div id="black_background"></div>

<!--
<div id="install-from-mobile" class="background-black-color">
    <div id="install-from-mobile-text" class="font-family-basic">Install the app</div>
    <div id="install-from-mobile-img">
        <a href="https://f-droid.org/it/packages/org.commonvoice.saverio/"><img src="/old/images/icons/f-droid.png"/></a>
        <a href="https://play.google.com/store/apps/details?id=org.commonvoice.saverio"><img
                    src="/old/images/icons/play-store.png"/></a>
        <a href="https://appgallery.huawei.com/#/app/C101607593"><img src="/old/images/icons/appgallery.png"/></a>
    </div>
</div>
-->

<?php
if ($working_in_progress) {
    if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != 0) {
        echo "<style>#working_in_progress{display:none;}#frase_attesa_lunga_working{display:none;}</style>";
        ?>
        <style>
            #working_in_progress_btt {
                display: block;
                position: fixed;
                bottom: 0px;
                left: 0px;
                margin: 0px;
                background-color: gold;
                border: 1px solid black;
                color: black;
                font-family: inherit;
                padding: 5px;
                font-size: 20px;
                border-bottom-left-radius: 0px;
                font-weight: bold;
                z-index: 10;
            }
        </style>
        <input type="button" id="working_in_progress_btt" value="WORKING IN PROGRESS"/>
        <?php
    } else {
        echo "<style>#working_in_progress{display:block;}#frase_attesa_lunga_working{display:block;}</style>";
    }
} else {
    echo "<style>#working_in_progress{display:none;}#frase_attesa_lunga_working{display:none;}</style>";
}
?>

<script>
    $(window).load(function () {
        ridimensiona();
        loading();
    });
    var wait = setInterval(mostraFraseAttesa, 3000);
    var wait_n = 0;

    function mostraFraseAttesa() {
        if (wait_n == 0) {
            $("#frase_attesa_lunga_loading").fadeIn();
            clearInterval(wait);
            wait = setInterval(mostraFraseAttesa, 1000);
        }
        wait_n++;
        if (wait_n == 1 || wait_n == 4) document.getElementById("frase_attesa_lunga_loading").innerHTML = "Loading.";
        else if (wait_n == 2 || wait_n == 5) document.getElementById("frase_attesa_lunga_loading").innerHTML += ".";
        else if (wait_n == 3 || wait_n == 6) {
            document.getElementById("frase_attesa_lunga_loading").innerHTML += ".";
            if (wait_n == 6) {
                clearInterval(wait);
                wait = setInterval(mostraFraseAttesa, 3000);
            }
        } else if (wait_n >= 7) document.getElementById("frase_attesa_lunga_loading").innerHTML = "Sorry.<br>Loading longer than expected";
    }
</script>