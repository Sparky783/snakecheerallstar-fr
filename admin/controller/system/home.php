<?php
use System\WebSite;
use System\ToolBox;
use Snake\SnakeTools;

// ==== Access security ====
if (!$session->admin_isConnected) {
	WebSite::redirect('login', true);
}
// =========================

global $router;

// Nom de l'utilisateur connecté
$name = $session->admin_name;

// Liste des saisons disponibles
$saisons = '';

for ($y = (int)date('Y'); $y >= 2019; $y --) {
	$saison = SnakeTools::getSaison($y . date('-m-d'));

	// Ignore la dernière saison 2018-2019 car il n'y a pas d'info dasn la BDD.
	if ($saison === '2018-2019') {
		continue;
	}

	$selected = '';

	if ($saison === $session->selectedSaison) {
		$selected = 'selected';
	}

	$saisons .= "<option value='{$saison}' {$selected}>{$saison}</option>";
}

// Lien disponible pour l'utilisateur connecté
$links = '';

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('adherents') . "'><i class='fas fa-users'></i> Infos adhérents</a>";
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'coach'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('presences') . "'><i class='fas fa-dumbbell'></i> Suivi des présences</a>";
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('email') . "'><i class='fas fa-users'></i> Envoyer un E-mail</a>";
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'tresorier'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('comptabilite') . "'><i class='fas fa-donate'></i> Récap inscriptions</a>";
}
/*
if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'tresorier'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->GetUrl('reductions') . "'><i class='fas fa-donate'></i> Réductions</a>";
}
*/
if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('ag_elections') . "'><i class='fas fa-cog'></i> Election de l'AG</a>";
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster'])) {
	$links .= "<span class='separator'></span>";
}

if(ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('options') . "'><i class='fas fa-cog'></i> Options du site</a>";
}

if(ToolBox::searchInArray($session->admin_roles, ['admin'])) {
	$links .= "<a class='btn btn-secondary' href='" . $router->getUrl('admins') . "'><i class='fas fa-cog'></i> Administrateurs</a>";
}
?>