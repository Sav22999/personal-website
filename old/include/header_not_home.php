<?php
global $title, $header_path, $root_separator;
?>
<div id="header_not_home" class="clearfix">
    <h1 id="header_title">
        <?php if (isset($title)) {
            echo $title;
        } ?>
    </h1>
    <?php if (isset($header_path)) { ?>
        <hr class="header_separator"/>
        <h4 id="header_path">
            <?php echo str_replace("{{*{{separator}}*}}", $root_separator, $header_path) . " " . $root_separator . " " . $title; ?>
        </h4>
    <?php } ?>
</div>