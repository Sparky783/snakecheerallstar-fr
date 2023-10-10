<?php
// Start execution by displaying errors.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define environment.
if ($_SERVER['SERVER_ADDR'] !== '109.234.165.163') {
    define('ENV', 'DEV');
} else {
    define('ENV', 'PROD');
}

// Load website configuration file
include_once('config.php');

// Force to using HTTPS.
if (ENV === 'PROD' && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

header('Access-Control-Allow-Origin: *');

// Define root path
define('ABSPATH', dirname(__FILE__) . '/');

// Chargement du loader
include_once(ABSPATH . 'model/System/SplClassLoader.php');

$classLoader = new System\SplClassLoader(ABSPATH . 'model');
$classLoader->register();
?>