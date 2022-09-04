<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "member")))
	WebSite::Redirect("login", true);
// =========================

include_once(ABSPATH . "model/snake/Section.php");
include_once(ABSPATH . "model/snake/Adherent.php");

global $router;

$sections = Section::GetList($session->selectedSaison);

$sectionsHtml = "";
foreach($sections as $section)
	$sectionsHtml .= "<option value='" . $section->GetId() . "'>" . $section->GetName() . "</option>";
?>