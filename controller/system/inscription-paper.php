<?php
// ================================
// ==== Controleur Inscription ====
// ================================
global $gmm;

// ==============================================
// ==== Gestion des accés pour l'inscription ====
// ==============================================

$allowAccess = false;

// Accès à l'inscription pour le dev.
if (ENV === 'DEV' || $gmm->getValue('pass') === 'bwsgl2024') {
	$allowAccess = true;
}

// Accès normal au inscriptions suivant les paramètres du site.
$options = unserialize($session->websiteOptions);
$today = new DateTime();

if ($options->IS_OPEN_INSCRIPTION && $today >= $options->INSCRIPTION_MIN_DATE &&  $today < $options->INSCRIPTION_MAX_DATE) {
	$allowAccess = true;
}
?>