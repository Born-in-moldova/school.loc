<?php
require_once 'config.php';
require_once APP_PATH . 'db_connect.php';
require_once APP_PATH . 'app/helpers/autoload_h.php';
require_once APP_PATH . 'app/helpers/main_h.php';
// require_once APP_PATH . 'helpers/clean_h.php';

//routing
if (isset($_GET['c'])) {
    if (file_exists('app/controllers/' . $_GET['c'] . '_c.php')) {
        $controller = $_GET['c'];
    } else {
        header("HTTP/1.0 404 Not Found");
        die();
    }
} else {
    $controller = 'school';
}

$action = 'index';

if (isset($_GET['a'])) 
    $action = $_GET['a'];

require_once APP_PATH . 'app/controllers/' . $controller . '_c.php';
/*$controller_class = $controller . "_C";
$obj_c = new $controller_class;
$obj_c->$action();*/