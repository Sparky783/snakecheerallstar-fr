<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin")))
	WebSite::Redirect("login", true);
// =========================

include_once(ABSPATH . "model/Options.php");

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