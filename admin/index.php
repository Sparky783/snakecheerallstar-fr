<?php
use System\Session;
use System\WebSite;
use Snake\SnakeTools;

$session = Session::getInstance();

if(!isset($session->selectedSaison)) {
	$session->selectedSaison = SnakeTools::GetCurrentSaison();
}

if(!isset($session->admin_isConnected)) {
	$session->admin_isConnected = false;
}

$website = new WebSite(ABSPATH. 'admin', true); // true = Enable admin mode

$website->SetPages(array(
	'login',
	'home',
	'profil',
	'adherents',
	'adherent-info',
	'adherent-add',
	'email',
	'presences',
	'presences_graph',
	'comptabilite',
	'reductions',
	'ag_elections',
	'admins',
	'options'
));
$website->DefaultPage("login");

$website->Run();