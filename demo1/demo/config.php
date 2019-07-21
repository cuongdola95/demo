<?php
session_start();

/**
 * php version 
 * 
 */
if(version_compare(phpversion(), '5.6.0', '<') == true){ 
    die ('PHP5.6 Only'); 
}

/**
 * autoloading
 **/
function __autoload($name) {
    $path = implode('/',explode('_', $name)) . '.php';
    $path = $_SERVER['DOCUMENT_ROOT'] . "/" . $path;
    if(file_exists($path)) {
        require_once $path;
    }
}

ini_set('display_errors', 0);
set_time_limit(0);
mb_internal_encoding("UTF-8");
header('Content-Type: text/html;charset=UTF-8');

/**
 * define base url
 * 
 **/
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "";
$base_root = $_SERVER['DOCUMENT_ROOT'] . "";
$root_logs = "/home/khiemtv/www/sim/chay/logs";
$key_enc = "6sq#nN7eC]5>n>5n";

/**
 * config db 
 * 
 **/
$config['default'] = array(
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'db'   => '',
    );

// reload data
if(isset($_SESSION['admin_logged'])) {
    $models_users = new Models_Users();
    $adminuser = $models_users->getObject($_SESSION['admin_logged']->getId());
}

$date_format = "d/m/Y - H:i:s";
$date_timezone = "Asia/Ho_Chi_Minh";
date_default_timezone_set($date_timezone);
