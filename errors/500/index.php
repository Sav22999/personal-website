<?php
include_once(dirname(__DIR__) . '/error-template.php');
render_error_page(500, 'Internal Server Error', 'Something went wrong on our end. Please try again later.');
?>
