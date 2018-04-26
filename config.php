<?php  
//echo '<pre>' . print_r($_SERVER, true) . '</pre>';
define('DEBUG', false);
define('DB_HOST', 'localhost');
define('DB_NAME', 'school');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('APP_PATH', $_SERVER['DOCUMENT_ROOT'] . '/' );

//SITE_URL : http://school.loc
//APP_PATH : Z:/home/school.loc/www/

?>