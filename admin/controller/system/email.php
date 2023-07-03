<?php
use System\WebSite;
use System\ToolBox;
use Snake\Section;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "member")))
	WebSite::Redirect("login", true);
// =========================

$sectionsHtml = "<option value='all'>Tous le monde</option>";

$sections = Section::GetList($session->selectedSaison);
		
foreach($sections as $section)
	$sectionsHtml .= "<option value='" . $section->GetId() . "'>" . $section->GetName() . "</option>";
?>