<?php
include_once(dirname(__DIR__) . '/error-template.php');
render_error_page(401, 'Unauthorized', 'You need to authenticate to access this resource.');
?>
