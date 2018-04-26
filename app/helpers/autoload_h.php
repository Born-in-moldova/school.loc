<?
function __autoload($name){
    require_once APP_PATH.'app/models/'.$name.'_m.class.php';
}

/* spl_autoload_register(function ($class_name) {
    require_once APP_PATH.'classes/'.$class_name.'.class.php';
}); */