<?php
use System\WebSite;
use System\ToolBox;
use Snake\Section;

// ==== Access security ====
if (!ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
	WebSite::redirect('login', true);
}
// =========================

global $router;
global $gmm;

$sections = Section::getList($session->selectedSaison);
$idSelectedSection = (int)$gmm->getValue('section');

$sectionsHtml = '';
foreach ($sections as $section) {
	$isSelected = $section->getId() === $idSelectedSection ? ' selected' : '';
	$sectionsHtml .= "<option value='{$section->getId()}' {$isSelected}>{$section->getName()}</option>";
}

$addAdhButtonHtml = '';

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'secretaire'])) {
	$link = $router->getUrl('adherent-add');
	$addAdhButtonHtml = <<<HTML
		<div class='btn-group right'>
			<a id='buttonAddAdherent' class='btn btn-primary' href={$link}>
				<i class='fa fa-plus'></i>
				Ajouter un adhérent
			</a>
		</div>
		HTML;
}
?>