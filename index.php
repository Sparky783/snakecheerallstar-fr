<?php
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.
include_once(ABSPATH . "model/system/WebSite.php");
include_once(ABSPATH . "model/Options.php");

// Loaded for session object
include_once(ABSPATH . "model/snake/Inscription.php");

$session = Session::getInstance();

// ==== Controleur général ========================================================
if(!isset($session->websiteOptions))
{
	$options = new Options();
	$options->LoadFromDatabase();
	$session->websiteOptions = serialize($options);
}	
// ================================================================================

$website = new WebSite();

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