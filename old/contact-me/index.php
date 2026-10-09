<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Contact me";
    $current_page = "contact-me";
    $header_path = "<a href='" . get_url("home") . "' class='header_path'>Home</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
    show_header();
    ?>

    <header>
        <?php show_menu(); ?>
        <?php show_header_not_home(); ?>
    </header>
</head>
<body>
<main class="clearfix">
    <aside>
        <?php show_aside(); ?>
    </aside>
    <section>
        <article>
            <div id="contact-me-sec" class="margin-bottom30">
                <p>
                    You can write an email to <b>contact-me@saveriomorelli.com</b>.
                    <br>
                    Alternatively you can contact me on Telegram, join on <b>@sav_projects</b>.
                    <br>
                    <br>
                    You will receive an answer as soon as possible.
                </p>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->