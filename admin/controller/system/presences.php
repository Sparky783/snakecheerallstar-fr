<?php
use System\WebSite;
use System\ToolBox;
use Snake\Section;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "coach")))
	WebSite::Redirect("login", true);
// =========================

$sections = Section::GetList($session->selectedSaison);

$sectionsHtml = "";
foreach($sections as $section)
	$sectionsHtml .= "<option value='" . $section->GetId() . "'>" . $section->GetName() . "</option>";
?>