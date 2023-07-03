<?php
use System\WebSite;
use System\ToolBox;
use System\Options;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster")))
	WebSite::Redirect("login", true);
// =========================

if(!isset($session->websiteOptions))
{
	$options = new Options();
	$options->LoadFromDatabase();
	$session->websiteOptions = serialize($options);
}
else
{
	$options = unserialize($session->websiteOptions);
}

// Liste des options à afficher
$cbOpenInscriptionValue = "";
if($options->IS_OPEN_INSCRIPTION)
	$cbOpenInscriptionValue = "checked";

$tbMinDateIscriptionValue = $options->INSCRIPTION_MIN_DATE->format("Y-m-d");

$tbMaxDateInscriptionValue = $options->INSCRIPTION_MAX_DATE->format("Y-m-d");
?>