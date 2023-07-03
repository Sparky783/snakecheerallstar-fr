<?php
use System\WebSite;
use System\ToolBox;
use Snake\Section;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "member")))
	WebSite::Redirect("login", true);
// =========================

global $router;

$sections = Section::GetList($session->selectedSaison);

$sectionsHtml = "";
foreach($sections as $section)
	$sectionsHtml .= "<option value='" . $section->GetId() . "'>" . $section->GetName() . "</option>";

$addAdhButtonHtml = "";

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "secretaire")))
	$addAdhButtonHtml = "
		<div class='btn-group right'>
			<a id='buttonAddAdherent' class='btn btn-primary' href=" . $router->GetUrl('adherent-add') . ">
				<i class='fa fa-plus'></i>
				Ajouter un adhérent
			</a>
		</div>
	";
?>