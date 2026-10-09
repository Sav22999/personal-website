<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Notefox";
    $url_opengraph = "https://www.saveriomorelli.com/projects/notefox/opengraph.png";
    $current_page = "details-project";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a> {{*{{separator}}*}} <a href='" . get_url("projects") . "' class='header_path'>Projects</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();

    redirectTo("https://www.notefox.eu/opened-times/", 0);
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>

    <script>
        function link(url) {
            return location.href = url;
        }
    </script>
</head>
<body>
<div class="margin-top380"></div>
<div class="width100 background-transparent-color text-black-color border-radius-0">
    <div class="center-content clearfix">
        <h1>You opened the addon popup so many times, so I hope you like Notefox.</h1>
        <p>
            Because you are a usual user, maybe you have some suggestions or feedback: <b>I want to hear you</b>. Write
            me via Telegram,
            via email or opening an issue on GitHub. You can also write a review on the Firefox Addons website.
        <div class="text-center center-content">
            <button onclick="link('https://www.saveriomorelli.com/contact-me/')">Email</button>
            <button onclick="link('https://t.me/sav_projects/7')">Telegram</button>
            <button onclick="link('https://github.com/Sav22999/websites-notes')">GitHub</button>
            <button onclick="link('https://addons.mozilla.org/it/firefox/addon/websites-notes/')">Review the addon!
            </button>
        </div>
        </p>
        <h3>Support the addon!</h3>
        <p>
            Notefox is a <b>totally free</b> and <b>open-source add-on</b>. This means I don't earn anything directly
            from the addon, but only from donations. You can support my work making me a donation.
        <div class="text-center center-content">
            <button onclick="link('https://liberapay.com/Sav22999/')">Donate via LiberaPay</button>
            <button onclick="link('https://www.paypal.me/saveriomorelli')">Donate via PayPal</button>
        </div>
        </p>
    </div>
</div>
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center">
    <a href="https://github.com/Sav22999/websites-notes"><img src="/old/images/socials/github.png"
                                                              class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://addons.mozilla.org/it/firefox/addon/websites-notes/"><img src="/old/images/icons/firefox-bw.svg"
                                                                               class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://crowdin.com/project/notefox"><img src="/old/images/icons/crowdin.png"
                                                       class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://t.me/sav_projects/7"><img src="/old/images/socials/telegram.png"
                                               class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://liberapay.com/Sav22999/"><img src="/old/images/icons/liberapay-bw.svg"
                                                   class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://www.paypal.me/saveriomorelli"><img src="/old/images/icons/paypal-bw.svg"
                                                        class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
</div>
<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->