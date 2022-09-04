<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "coach")))
	WebSite::Redirect("login", true);
// =========================

include_once(ABSPATH . "model/snake/Section.php");

$sections = Section::GetList($session->selectedSaison);

$sectionsHtml = "";
foreach($sections as $section)
	$sectionsHtml .= "<option value='" . $section->GetId() . "'>" . $section->GetName() . "</option>";
?>