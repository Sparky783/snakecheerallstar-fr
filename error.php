<?php
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.

// #######################################
// #### LANCEMENT DE LA PAGE D'ERREUR ####
// #######################################
if(!MAINTENANCE_MODE)
	include_once(ABSPATH . "view/error.php");
else
	include_once(ABSPATH . "view/maintenance.php");