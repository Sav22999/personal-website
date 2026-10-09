<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "About me";
    $current_page = "about-me";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<div class="margin-top380"></div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="center-content">
        <div id="about-me-sec1" class="clearfix">
            <div class="img-float-right" style="background-image: url('/old/images/photos/about-me-1.jpg');"></div>
            <h1 class="h1-left">Chi sono</h1>
            <hr class="hr-left"/>
            <p>
                Mi chiamo Saverio Morelli, e sono un ragazzo proveniente dalla Basilicata, nello specifico da Matera
                (provincia), la <i>città dei sassi</i>. Sono nato il 22 settembre 1999 a Tricarico (Mt).
                <br>
                Studio <i>Informatica</i> presso l'Università degli Studi di Trento e vivo, pertanto, a Trento.
                <br>
                Il mio solito username è <b>@Sav22999</b> dove <i>Sav</i> sta per Saverio, <i>22</i>, <i>9</i> e
                <i>99</i> stanno per la mia data di nascita, rispettivamente giorno, mese e anno.
                <br>
                Amo il nuoto, l'Inghilterra, la storia, leggere libri, programmare, vedere serie tv e film. Sono un
                Mozilla Rep, sono un volontario
                attivo di Mozilla Italia e sono un istruttore di nuoto (FIN).
                <br>
                Ho scritto un libro sull'HTML, <a href="https://amzn.to/2SaSRPu" class="text-black-color">disponibile su
                    Amazon</a>.
            </p>
        </div>
    </div>
</div>
<div class="width100 margin-bottom-minus-8 background-primary-color text-white-color border-radius-0">
    <div class="center-content">
        <div id="about-me-sec3" class="clearfix">
            <h1 class="h1-center">IT skills</h1>
            <hr class="hr-center"/>
            <div id="about-me-sec3-imgs">
                <img src="/old/images/it-skills/html.png" class="it-skills-icons" alt="HTML"/>
                <img src="/old/images/it-skills/css.png" class="it-skills-icons" alt="CSS"/>
                <img src="/old/images/it-skills/php.png" class="it-skills-icons" alt="PHP"/>
                <img src="/old/images/it-skills/javascript.png" class="it-skills-icons" alt="JavaScript"/>
                <img src="/old/images/it-skills/java.png" class="it-skills-icons" alt="Java"/>
                <img src="/old/images/it-skills/cpp.png" class="it-skills-icons" alt="C++"/>
                <img src="/old/images/it-skills/csharp.png" class="it-skills-icons" alt="C#"/>
                <img src="/old/images/it-skills/python.png" class="it-skills-icons" alt="Python"/>
                <img src="/old/images/it-skills/kotlin.png" class="it-skills-icons" alt="Kotlin"/>
                <img src="/old/images/it-skills/jquery.png" class="it-skills-icons" alt="jQuery"/>
                <hr class="margin-top30 margin-bottom30">
                <img src="/old/images/it-skills/androidstudio.png" class="it-skills-icons" alt="Android Studio"/>
                <img src="/old/images/it-skills/firefox.png" class="it-skills-icons" alt="Mozilla Firefox"/>
                <img src="/old/images/it-skills/thunderbird.png" class="it-skills-icons" alt="Mozilla Thunderbird"/>
                <img src="/old/images/it-skills/kdevelop.png" class="it-skills-icons" alt="KDevelop"/>
                <img src="/old/images/it-skills/intellijidea.png" class="it-skills-icons"
                     alt="JetBrains IntelliJ IDEA"/>
                <img src="/old/images/it-skills/phpstorm.png" class="it-skills-icons" alt="JetBrains PhpStorm"/>
                <img src="/old/images/it-skills/pycharm.png" class="it-skills-icons" alt="JetBrains PyCharm"/>
                <img src="/old/images/it-skills/vscode.png" class="it-skills-icons" alt="Microsoft Visual Studio Code"/>
                <img src="/old/images/it-skills/visualstudio.png" class="it-skills-icons"
                     alt="Microsoft Visual Studio"/>
            </div>
            <p></p>
        </div>
    </div>
</div>
<div class="width100 margin-bottom-minus-8 background-transparent-color text-black-color border-radius-0">
    <div class="center-content">
        <div id="about-me-sec3" class="clearfix">
            <div class="img-float-left" style="background-image: url('/old/images/photos/about-me-2.jpg');"></div>
            <h1 class="h1-left">Volontariato</h1>
            <hr class="hr-right"/>
            <p>
                Sono un volontario della comunità italiana di Mozilla, ovvero Mozilla Italia. Mi occupo, in essa,
                principalmente di traduzione (En-It), e programmazione di vari progetti. Inoltre, da Marzo 2020 sono
                anche un <a href="https://people.mozilla.org/p/Sav22999/" class="text-black-color">Mozilla Rep</a>.
                <br>
                Ho sviluppato, insieme al supporto di tutta la comunità MozIta, i bot ufficiali su Telegram: <a
                        href="http://t.me/mozitabot" class="text-black-color">@MozItaBot</a> e <a
                        href="http://t.me/mozita_antispam_bot" class="text-black-color">@mozita_antispam_bot</a>.
                Sempre su Telegram, sono admin dei gruppi comunitari MozIta.
                <br>
                Ho migliorato il <a href="https://github.com/MozillaItalia/firefox-vademecum" class="text-black-color">Vademecum</a>
                della
                comunità, realizzando la versione HTML-CSS e ideando (e realizzando) la versione "tecnica" (VT) e la
                versione "Common Voice" (CV).
                <br>
                Ho migliorato la grafica della comunità Mozilla Italia, realizzando il nuovo design (loghi, icone,
                immagini profilo, ecc.), e ho anche stilato le linee guida di stile.
                <br> Ho realizzato, con altri volontari, il nuovo sito web di <a href="https://www.mozillaitalia.org"
                                                                                 class="text-black-color">Mozilla
                    Italia</a>.
                <br>
                Sono molto attivo anche nel progetto Mozilla Common Voice, del quale ho anche realizzato un'<a
                        href="https://github.com/Sav22999/common-voice-android" class="text-black-color">app Android</a>
                (non ufficiale).
                <br>
                Sono anche un volontario (e socio) di <a href="https://www.informaticisenzafrontiere.org/"
                                                         class="text-black-color">Informatici Senza Frontiere</a> dal
                2020.
            </p>
        </div>
    </div>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->