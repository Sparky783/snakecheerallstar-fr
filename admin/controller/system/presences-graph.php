<?php
use System\WebSite;
use System\ToolBox;
use System\Database;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "coach")))
	WebSite::Redirect("login", true);
// =========================

$sectionsHtml = "";
$database = new Database();

$sections = $database->query("SELECT * FROM sections");
		
if($sections != null)
{
	while($section = $sections->fetch())
		$sectionsHtml .= "<option value='" . $section['id_section'] . "'>" . $section['name'] . "</option>";
}
?>