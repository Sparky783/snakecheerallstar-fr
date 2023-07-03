<?php
use ApiCore\Api;
use System\Router;
use System\Session;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $router;
$router = new Router(true); // Enable admin mode
$session = Session::getInstance();
			
if($session->admin_isConnected)
{
	$app = new Api();

	// Open the controler folder and include all files.
	$dir = ABSPATH . "admin/controller/api";

	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(substr($file, -4) === ".php")
					include_once $dir . '/' . $file;
			}
			closedir($dh);
		}
	}

	$app->Run();
}
?>