<?php
include_once(ABSPATH . "model/system/WebSite.php");
require_once(ABSPATH . "model/system/Session.php");
include_once(ABSPATH . "model/snake/SnakeTools.php");

$session = Session::getInstance();

// ==== Controleur général ========================================================
if(!isset($session->selectedSaison))
	$session->selectedSaison = SnakeTools::GetCurrentSaison();
// ================================================================================

$website = new WebSite(true); // true = Enable admin mode

$website->SetPages(array(
	"login",
	"home",
	"profil",
	"users",
	"adherents",
	"adherent-info",
	"email",
	"presences",
	"presences_graph",
	"comptabilite",
	"reductions",
	"ag_elections",
	"options"
));
$website->DefaultPage("login");

$website->Run();