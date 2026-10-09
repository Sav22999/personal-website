<?php
include_once(dirname(__DIR__) . '/error-template.php');
render_error_page(403, 'Forbidden', 'You don\'t have permission to access this resource.');
?>
