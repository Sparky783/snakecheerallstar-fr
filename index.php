<?php
use System\Session;
use System\WebSite;
use System\Options;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.

$session = Session::getInstance();

$website = new WebSite(ABSPATH);

$website->SetPages(array(
	"accueil",
	"actualites",
	"club",
	"cours",
	"inscription",
	"galerie",
	"contact",
	"cgu",
	"election_ag" // Cloture géré dans le fichier controller/system/election_ag.php
));
$website->DefaultPage("accueil");

$website->Run();