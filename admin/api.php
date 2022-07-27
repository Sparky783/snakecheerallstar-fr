<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
			
include_once(ABSPATH . "model/system/ToolBox.php");
include_once(ABSPATH . "model/system/Session.php");
include_once(ABSPATH . "model/system/Router.php"); // Load the API core. Completely independant.
include_once(ABSPATH . "model/api-core/Api.php"); // Load the API core. Completely independant.

global $router;
$router = new Router(true); // Enable admin mode

$session = Session::getInstance();
			
if($session->isConnected)
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