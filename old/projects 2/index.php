<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Projects";
    $current_page = "projects";
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
        <div id="projects-sec1" class="clearfix">
            <h1 class="h1-center">My active projects</h1>
            <hr class="hr-center"/>
            <div class="projects-div clearfix margin-top30">
                <div class="project project-50 background-white-color text-black-color" id="project-4"
                     onclick="location.href='<?php echo get_redirect("websites-notes"); ?>'">
                    <img class="project-img" src="/old/images/projects/websites-notes.png" alt="Websites notes"/>
                    <h1 class="project-h1">Notefox: websites notes</h1>
                    <p class="text-color-dark">
                        This is a web browser add-on which permits you to take notes on every website in a smart and
                        simple way.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/notefox" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
                <div class="project project-50 background-white-color text-black-color" id="project-3"
                     onclick="location.href='<?php echo get_redirect("sav-pdf-viewer-pro"); ?>'">
                    <img class="project-img" src="/old/images/projects/sav-pdf-viewer-pro.png"
                         alt="Sav PDF Viewer Pro"/>
                    <h1 class="project-h1">Sav PDF Viewer</h1>
                    <p class="text-color-dark">
                        The simplest PDF viewer.
                        <br>
                        Safe (the app doesn't require any permissions), Lightweight (just 5.9MB), and modern.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/sav-pdf-viewer" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
            </div>
            <div class="projects-div clearfix">
                <div class="project project-30 background-white-color text-black-color" id="project-2"
                     onclick="location.href='<?php echo get_redirect("emoji"); ?>'">
                    <img class="project-img" src="/old/images/projects/emoji.png" alt="Emoji"/>
                    <h1 class="project-h1">Emoji</h1>
                    <p class="text-color-dark">
                        This is a web browser add-on which permits, just with a single click, to copy or insert an
                        emoji.
                        <br>
                        There is a search-box and the "Most used emojis" section (the first one).
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/emoji" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-6"
                     onclick="location.href='<?php echo get_redirect("savmrl.it"); ?>'">
                    <img class="project-img" src="/old/images/projects/savmrl.png" alt="savmrl.it"/>
                    <h1 class="project-h1">savmrl.it</h1>
                    <p class="text-color-dark">
                        It's an anonymous and free link shortener and redirecting service. No registration required!
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-6"
                     onclick="location.href='<?php echo get_redirect("accented-letters"); ?>'">
                    <img class="project-img" src="/old/images/projects/accented-letters.png" alt="Accented Letters"/>
                    <h1 class="project-h1">Accented Letters</h1>
                    <p class="text-color-dark">
                        With this web browser add-on you can copy accented letters with a single click.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/accented-letters" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
            </div>
            <div class="projects-div clearfix">
                <div class="project project-30 background-white-color text-black-color" id="project-5"
                     onclick="location.href='<?php echo get_redirect("limite"); ?>'">
                    <img class="project-img" src="/old/images/projects/limite.png" alt="Limite"/>
                    <h1 class="project-h1">Limite</h1>
                    <p class="text-color-dark">
                        This is a web browser add-on which permits you to check how much time you spend on each website
                        every day.
                        <br>
                        Don't lose precious time!
                        <br>
                        Optimise your productivity, your time and your life as well.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/limite" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-8"
                     onclick="location.href='<?php echo get_redirect("word-of-the-day"); ?>'">
                    <img class="project-img" src="/old/images/projects/word-of-the-day.png" alt="Word of the Day"/>
                    <h1 class="project-h1">Word of the Day</h1>
                    <p class="text-color-dark">
                        Every day the app offers a new word to learn, so you will have a vast vocabulary. You can read
                        the definition of the word, its origin / etymology, its pronunciation / phonetics (RP-IPA).
                        <br>
                        You can copy or share it as well!
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/word-of-the-day" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-9"
                     onclick="location.href='<?php echo get_redirect("html-per-tutti"); ?>'">
                    <img class="project-img" src="/old/images/projects/html-per-tutti.png" alt="HTML per tutti"/>
                    <h1 class="project-h1">HTML per tutti</h1>
                    <p class="text-color-dark">
                        It's a book/manual wrote by me to learn easily the HTML language. It's available on Amazon, both
                        paper and ebook format.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
            </div>
            <div class="projects-div clearfix">
                <div class="project project-30 background-white-color text-black-color" id="project-10"
                     onclick="location.href='<?php echo get_redirect("mozita-antispam"); ?>'">
                    <img class="project-img" src="/old/images/projects/mozita-antispam.png" alt="MozIta Antispam"/>
                    <h1 class="project-h1">MozIta Antispam</h1>
                    <p class="text-color-dark">
                        It's a bot realised for Mozilla Italia, because of the spam users.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-11"
                     onclick="location.href='<?php echo get_redirect("all-currencies"); ?>'">
                    <img class="project-img" src="/old/images/projects/all-currencies.png" alt="All currencies"/>
                    <h1 class="project-h1">All currencies</h1>
                    <p class="text-color-dark">
                        You can copy currencies with a single click. This is a Firefox add-on.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-12"
                     onclick="location.href='<?php echo get_redirect("mozita-l10n-addons"); ?>'">
                    <img class="project-img" src="/old/images/projects/mozita-l10n-addon.png" alt="MozIta L10n"/>
                    <h1 class="project-h1">MozIta L10n Addon</h1>
                    <p class="text-color-dark">
                        This add-on is especially for the Mozilla Italia community, in particular for the L10n team.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="width100 background-primary-color text-white-color border-radius-0">
    <div class="center-content">
        <div id="projects-sec2" class="clearfix">
            <h1 class="h1-center">Some contributions</h1>
            <hr class="hr-center"/>
            <div class="projects-div clearfix margin-top30">
                <div class="project project-30 background-white-color text-black-color" id="project-contribution-1"
                     onclick="location.href='<?php echo get_redirect("vademecum"); ?>'">
                    <h1 class="project-h1">Firefox Vademecum</h1>
                    <p class="text-color-dark">
                        Project of the Mozilla Italia community, which is a A4 sheet where there are some useful
                        information about Mozilla, its projects/initiatives and it's available as digital or printed.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-contribution-2"
                     onclick="location.href='<?php echo get_redirect("share-backported"); ?>'">
                    <img class="project-img" src="/old/images/projects/share-backported.png" alt="Share Backported"/>
                    <h1 class="project-h1">Share Backported</h1>
                    <p class="text-color-dark">
                        It's a Firefox add-on developed by Daniele Scasciafratte and it permits you to share easily on
                        the most famous social networks (customisable).
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-contribution-3"
                     onclick="location.href='<?php echo get_redirect("mozita-website"); ?>'">
                    <img class="project-img" src="/old/images/projects/mozita-website.png"
                         alt="Mozilla Italia Website"/>
                    <h1 class="project-h1">Sito Web Mozilla Italia</h1>
                    <p class="text-color-dark">
                        The new website of Mozilla Italia, realised also with other volunteers of the community. I've
                        realised the new logos as well.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="width100 margin-bottom-minus-8 background-transparent-color text-black-color border-radius-0">
    <div class="center-content">
        <div id="projects-sec3" class="clearfix">
            <h1 class="h1-center">My <b>discontinued</b> projects</h1>
            <hr class="hr-center"/>
            <div class="projects-div clearfix margin-top30">
                <div class="project project-30 background-white-color text-black-color" id="project-1"
                     onclick="location.href='<?php echo get_redirect("common-voice-android"); ?>'">
                    <img class="project-img" src="/old/images/projects/cv-project.png"
                         alt="Common Voice Android"/>
                    <h1 class="project-h1">CV Project</h1>
                    <p class="text-color-dark">
                        It was an unofficial app for Mozilla Common Voice, which permitted you to contribute to this
                        project
                        via your Android device.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                    <a href="/old/projects/cv-project" class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">See the timeline</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-discontinued-1"
                     onclick="location.href='<?php echo get_redirect("my-code-editor"); ?>'">
                    <img class="project-img" src="/old/images/projects/my-code-editor.png" alt="My Code Editor"/>
                    <h1 class="project-h1">My Code Editor</h1>
                    <p class="text-color-dark">
                        It was a code editor, free and light, with many features. It was available on Windows only.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-discontinued-2"
                     onclick="location.href='<?php echo get_redirect("nolimit-math"); ?>'">
                    <img class="project-img" src="/old/images/projects/nolimit-math.png" alt="NoLimit Math"/>
                    <h1 class="project-h1">NoLimit Math</h1>
                    <p class="text-color-dark">
                        It was a software to calculate (automatically) maths limits, finite and infinite (0/0 or ∞/∞)
                        and it generated the relative plot.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
            </div>
            <div class="projects-div clearfix">
                <div class="project project-30 background-white-color text-black-color" id="project-7"
                     onclick="location.href='<?php echo get_redirect("scrolly"); ?>'">
                    <img class="project-img" src="/old/images/projects/scrolly.png" alt="Scrolly"/>
                    <h1 class="project-h1">Scrolly</h1>
                    <p class="text-color-dark">
                        It was a Firefox add-on which remembered and restored the scroll position of each webpages
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                    <br>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-discontinued-3"
                     onclick="location.href='<?php echo get_redirect("mozita-myuserid"); ?>'">
                    <img class="project-img" src="/old/images/projects/mozita-myuserid.png" alt="MozIta MyUserID"/>
                    <h1 class="project-h1">MozIta MyUserId Bot</h1>
                    <p class="text-color-dark">
                        It was a bot realised to get userids from users already present in the community groups of
                        Mozilla Italia, used before the launch of MozIta Antispam bot.
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
                <div class="project project-30 background-white-color text-black-color" id="project-discontinued-4"
                     onclick="location.href='<?php echo get_redirect("mozitabot"); ?>'">
                    <img class="project-img" src="/old/images/projects/mozitabot.png" alt="MozItaBot"/>
                    <h1 class="project-h1">MozItaBot</h1>
                    <p class="text-color-dark">
                        Official bot of Mozilla Italia on Telegram (@MozItaBot).
                    </p>
                    <hr>
                    <a class="just-link">
                        <span class="text-underline text-lightblue-color-hover transition-0-5">Go to the project</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->