<?php
include_once(dirname(__DIR__) . '/error-template.php');
render_error_page(503, 'Service Unavailable', 'The server is temporarily unable to handle the request. Please try again later.');
?>
