<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <?php
    $title = "Segnalazione";
    $current_page = "";
    $header_path = "<a href='" . get_url("html-per-tutti") . "' class='header_path'>HTML per tutti</a>"; // if in the path there are more father-root, use {{*{{separator}}*}} to separate them: Root1 {{*{{separator}}*}} Root2
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
                <p class="text-center no-padding no-margin">
                    <a href="https://amzn.to/2SaSRPu">
                        <button>Acquista cartaceo</button>
                    </a>
                    <a href="https://amzn.to/2ScXTux">
                        <button>Acquista ebook</button>
                    </a>
                </p>
                <hr>
                <p>
                    Puoi scrivere una email all'indirizzo <a href="mailto:saverio.morelli@protonmail.com"><b>saverio.morelli@protonmail.com</b></a>.
                    Riceverai una risposta appena possibile.
                    <br>
                    In alternativa puoi contattarmi su Telegram come <b>@Sav22999</b>.
                </p>
            </div>
        </article>
    </section>
</main>

<?php show_footer(); ?>
</body>
</html>
<!-- Website realised by Saverio Morelli - www.saveriomorelli.com -->