<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
//header("Access-Control-Allow-Headers: x-requested-with, origin, content-type");

include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.
include_once(ABSPATH . "model/api-core/Api.php"); // Load the API core. Completely independant.
include_once(ABSPATH . "model/system/Router.php"); // Load the API core. Completely independant.


global $router;
$router = new Router();

if(!MAINTENANCE_MODE) {
	$app = new Api();
	
	// Open the controler folder and include all files.
	$dir = ABSPATH . "/controller/api";
	
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