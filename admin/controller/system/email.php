<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "member")))
	WebSite::Redirect("login", true);
// =========================

include_once(ABSPATH . "model/snake/SnakeTools.php");
include_once(ABSPATH . "model/snake/Section.php");

$sectionsHtml = "<option value='all'>Tous le monde</option>";

$sections = Section::GetList($session->selectedSaison);
		
foreach($sections as $section)
	$sectionsHtml .= "<option value='" . $section->GetId() . "'>" . $section->GetName() . "</option>";
?>