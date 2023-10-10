<?php
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.

use System\WebSite;

$website = new WebSite(ABSPATH);

$website->SetPages(array(
	"accueil",
	"actualites",
	"club",
	"cours",
	"inscription-paper",
	"inscription",
	"galerie",
	"contact",
	"cgu",
	"election_ag" // Cloture géré dans le fichier controller/system/election_ag.php
));

$website->DefaultPage("accueil");

$website->Run();