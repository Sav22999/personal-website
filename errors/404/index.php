<?php
include_once(dirname(__DIR__) . '/error-template.php');
render_error_page(404, 'Page not found', 'The page you\'re looking for doesn\'t exist or has been moved.');
?>
