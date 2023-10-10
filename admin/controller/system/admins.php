<?php
use System\WebSite;
use System\ToolBox;

// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin")))
	WebSite::Redirect("login", true);
// =========================
?>