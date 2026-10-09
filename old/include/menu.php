<?php
global $current_page;
?>
<div id="menu">
    <div id="cont-menu">
        <a href="<?php echo get_url("home"); ?>">
            <h1 id="title-menu">
                Saverio Morelli
            </h1>
        </a>
        <ul id="items-menu">
            <li id="show-mobile-menu" onclick="open_close_mobile_menu('')">
                <svg width="40"
                     height="40"
                     viewBox="0 0 40 40">
                    <path
                            d="M 3.3333336,6.666668 H 36.666666 a 3.3333335,3.3333335 0 0 0 0,-6.666667 H 3.3333336 a 3.3333335,3.3333335 0 0 0 0,6.666667 z M 36.666666,16.666667 H 3.3333336 a 3.3333331,3.3333331 0 0 0 0,6.666667 H 36.666666 a 3.3333331,3.3333331 0 0 0 0,-6.666667 z m 0,16.666666 H 3.3333336 a 3.3333337,3.3333337 0 0 0 0,6.666667 H 36.666666 a 3.3333337,3.3333337 0 0 0 0,-6.666667 z"
                            style="fill:#ffffff;fill-opacity:1;stroke-width:1.33333337"/>
                </svg>
            </li>
            <a href="<?php echo get_url("home"); ?>">
                <li id="home-menu" class="item-menu <?php if (isset($current_page) && $current_page == "home") {
                    echo "selected-page";
                } ?>">Home
                </li>
            </a>
            <a href="<?php echo get_url("about-me"); ?>">
                <li id="about-me-menu" class="item-menu <?php if (isset($current_page) && $current_page == "about-me") {
                    echo "selected-page";
                } ?>">About me
                </li>
            </a>
            <a href="<?php echo get_url("projects"); ?>">
                <li id="projects-menu" class="item-menu <?php if (isset($current_page) && $current_page == "projects") {
                    echo "selected-page";
                } ?>">Projects
                </li>
            </a>
            <a href="<?php echo get_url("contact-me"); ?>">
                <li id="contact-me-menu"
                    class="item-menu <?php if (isset($current_page) && $current_page == "contact-me") {
                        echo "selected-page";
                    } ?>">Contact me
                </li>
            </a>
        </ul>
    </div>
</div>