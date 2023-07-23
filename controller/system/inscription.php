<?php
// ================================
// ==== Controleur Inscription ====
// ================================

use System\Session;
use Snake\Inscription;

global $gmm;

$session = Session::getInstance();

if (!isset($session->inscription)) {
	$session->inscription = new Inscription();
}

// ==============================================
// ==== Gestion des accés pour l'inscription ====
// ==============================================
$allowAccess = false;

// Accès aux inscriptions pour les développeurs
if(ENV == "Dev")
	$allowAccess = true;

// Accès aux inscription pour test.
if(!isset($session->inscriptionAllowAccess)) {
	$session->inscriptionAllowAccess = false;
}

$test = false;
if($test && $gmm->getValue("pass") == "kamoulox" && !$session->inscriptionAllowAccess) {
	$session->inscriptionAllowAccess = true;
}

if($session->inscriptionAllowAccess) {
	$allowAccess = true;
}

// Accès normal au inscriptions suivant les paramètres du site.
$options = unserialize($session->websiteOptions);
$today = new DateTime();

if ($options->IS_OPEN_INSCRIPTION && $today >= $options->INSCRIPTION_MIN_DATE &&  $today < $options->INSCRIPTION_MAX_DATE) {
	$allowAccess = true;
}
// ==============================================

$script = "";
$html = "";

$step1 = "";
$step2 = "";
$step3 = "";
$step4 = "";
$step5 = "";

if($allowAccess)
{
	// Annulation de l'inscription et remise à zéro.
	if($gmm->GetValue("cancel") == "true")
		$session->inscription = new Inscription();

	// Chargement de la bonne page
	switch($session->inscription->GetState())
	{
		case Inscription::$STEPS['Adherents']:
			{
				$step1 = " active";
				include "inscription/step_1.php";
			}
			break;

		case Inscription::$STEPS['Tuteurs']:
			{
				$step2 = " active";
				include "inscription/step_2.php";
			}
			break;

		case Inscription::$STEPS['Authorization']:
			{
				$step3 = " active";
				include "inscription/step_3.php";
			}
			break;

		case Inscription::$STEPS['Payment']:
			{
				$step4 = " active";
				include "inscription/step_4.php";
			}
			break;

		case Inscription::$STEPS['Validation']:
			{
				$step5 = " active";
				include "inscription/step_5.php";
			}
			break;
	}

	// Ruban de status
	$htmlStep = "
		<div id='steps'>
			<span class='step-line'></span>
			<div class='step'>
				<span class='" . $step1 . "'>1<br />Adhérents</span>
			</div>
			<div class='step'>
				<span class='" . $step2 . "'>2<br />Tuteurs</span>
			</div>
			<div class='step'>
				<span class='" . $step3 . "'>3<br />Autorisations</span>
			</div>
			<div class='step'>
				<span class='" . $step4 . "'>4<br />Paiement</span>
			</div>
			<div class='step'>
				<span class='" . $step5 . "'>5<br />Confirmation</span>
			</div>
		</div>
	";
}
else
{
	$htmlStep = "";
	
	$html = "
		<div id='messageInscription'>
			<div class='alert alert-warning'>
				<i class='fas fa-exclamation-triangle'></i>
				Les inscriptions sont fermé pour le moment.
			</div>
		</div>
	";
}
?>