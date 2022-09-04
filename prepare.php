<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("ABSPATH", dirname(__FILE__) . "/"); // Absolute path of the API.

// Chargement de la configuration et de l'ensemble des éléments communs.
include_once("config.php");

// Force to using HTTPS
if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "on")
{
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit;
}
?>