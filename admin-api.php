<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
//header("Access-Control-Allow-Headers: x-requested-with, origin, content-type");

include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.

include_once(ABSPATH . "admin/api.php");