<?php
// ================================
// ==== Controleur Inscription ====
// ================================

use System\Session;
use Snake\Inscription;
use Snake\EInscriptionStep;

global $gmm;

$session = Session::getInstance();
$session->inscription = new Inscription();
$session->inscriptionState = EInscriptionStep::Information;

// ==============================================
// ==== Gestion des accés pour l'inscription ====
// ==============================================

if (!isset($session->inscriptionAllowAccess)) {
	$session->inscriptionAllowAccess = false;
}

// Accès à l'inscription pour le dev.
if (ENV === 'DEV' || $gmm->getValue('pass') === 'bwsgl2024') {
	$session->inscriptionAllowAccess = true;
}

// Accès normal au inscriptions suivant les paramètres du site.
$options = unserialize($session->websiteOptions);
$today = new DateTime();

if ($options->IS_OPEN_INSCRIPTION && $today >= $options->INSCRIPTION_MIN_DATE &&  $today < $options->INSCRIPTION_MAX_DATE) {
	$session->inscriptionAllowAccess = true;
}

$allowAccess = $session->inscriptionAllowAccess;
?>