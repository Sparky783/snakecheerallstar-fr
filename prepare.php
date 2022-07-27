<?php
define("ABSPATH", dirname(__FILE__) . "/"); // Absolute pth of the API.

// Chargement de la configuration et de l'ensemble des éléments communs.
include_once("config.php");

// Force to using HTTPS
if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "on")
{
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit;
}
?>