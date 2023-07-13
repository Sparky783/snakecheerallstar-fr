<?php
// Start execution by displaying errors.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Force to using HTTPS.
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on')
{
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

// Define environment.
//if($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1' || $_SERVER['SERVER_ADDR'] === '172.25.0.4')
if($_SERVER['SERVER_ADDR'] !== '109.234.165.163')
    define('ENV', 'DEV');
else
    define('ENV', 'PROD');

// Define root path
define('ABSPATH', dirname(__FILE__) . '/');

// Load website configuration file
include_once('config.php');

// Chargement du loader
include_once(ABSPATH . 'model/System/SplClassLoader.php');

$classLoader = new System\SplClassLoader(ABSPATH . 'model');
$classLoader->register();

$classLoader = new System\SplClassLoader(ABSPATH . 'model/PHPMailer/src');
$classLoader->register();
?>