<?php
use System\WebSite;
use System\ToolBox;
use System\Options;

// ==== Access security ====
if (!ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster'])) {
	WebSite::redirect('login', true);
}
// =========================

unset($session->websiteOptions);

if (!isset($session->websiteOptions)) {
	$options = new Options();
	$options->loadFromDatabase();
	$session->websiteOptions = serialize($options);
} else {
	$options = unserialize($session->websiteOptions);
}

// Liste des options à afficher
$cbOpenInscriptionValue = '';

if($options->IS_OPEN_INSCRIPTION) {
	$cbOpenInscriptionValue = 'checked';
}

$tbMinDateIscriptionValue = $options->INSCRIPTION_MIN_DATE->format('Y-m-d');
$tbMaxDateInscriptionValue = $options->INSCRIPTION_MAX_DATE->format('Y-m-d');
?>