<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin", "coach")))
	WebSite::Redirect("login", true);
// =========================

include_once(ABSPATH . "model/system/Database.php");

$sectionsHtml = "";
$database = new Database();

$sections = $database->query("SELECT * FROM sections");
		
if($sections != null)
{
	while($section = $sections->fetch())
		$sectionsHtml .= "<option value='" . $section['id_section'] . "'>" . $section['name'] . "</option>";
}
?>