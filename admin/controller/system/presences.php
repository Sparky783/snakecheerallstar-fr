<?php
use System\WebSite;
use System\ToolBox;
use Snake\Section;
use Snake\SnakeTools;

// ==== Access security ====
if (!ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'coach'])) {
	WebSite::redirect('login', true);
}
// =========================

$sections = Section::getList($session->selectedSaison);
$sectionsHtml = '';

foreach ($sections as $section) {
	$sectionsHtml .= "<option value='{$section->getId()}'>{$section->getName()}</option>";
}

$canBeDisplayed = $session->selectedSaison === SnakeTools::getCurrentSaison();
?>