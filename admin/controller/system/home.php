<?php
// ==== Access security ====
if(!$session->isConnected)
	WebSite::Redirect("login", true);
// =========================

global $router;

// Nom de l'utilisateur connecté
$name = $session->name;

// Liste des saisons disponibles
$saisons = "";

for($y = intval(date("Y")); $y >= 2019; $y --)
{
	$saison = SnakeTools::GetSaison($y . "-08-01");

	$selected = "";
	if($saison == $session->selectedSaison)
		$selected = "selected";

	$saisons .= "<option value='" . $saison . "' " . $selected . ">" . $saison . "</option>";
}

// Lien disponible pour l'utilisateur connecté
$links = "";

if(ToolBox::SearchInArray($session->roles, array("admin", "member")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("adherents") . "'><i class='fas fa-users'></i> Infos adhérents</a>";

if(ToolBox::SearchInArray($session->roles, array("admin", "coach")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("presences") . "'><i class='fas fa-dumbbell'></i> Suivi des présences</a>";

if(ToolBox::SearchInArray($session->roles, array("admin", "member")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("email") . "'><i class='fas fa-users'></i> Envoyer un E-mail</a>";

if(ToolBox::SearchInArray($session->roles, array("admin", "tresorier")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("comptabilite") . "'><i class='fas fa-donate'></i> Récap inscriptions</a>";
/*
if(ToolBox::SearchInArray($session->roles, array("admin", "tresorier")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("reductions") . "'><i class='fas fa-donate'></i> Réductions</a>";
*/
if(ToolBox::SearchInArray($session->roles, array("admin")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("ag_elections") . "'><i class='fas fa-cog'></i> Election de l'AG</a>";

if(ToolBox::SearchInArray($session->roles, array("admin")))
	$links .= "<span class='separator'></span>";

if(ToolBox::SearchInArray($session->roles, array("admin")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("options") . "'><i class='fas fa-cog'></i> Options du site</a>";

if(ToolBox::SearchInArray($session->roles, array("admin")))
	$links .= "<a class='btn btn-secondary btn-lg btn-block' href='" . $router->GetUrl("users") . "'><i class='fas fa-cog'></i> Utilisateurs</a>";
?>