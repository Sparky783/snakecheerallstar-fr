<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->admin_roles, array("admin")))
	WebSite::Redirect("login", true);
// =========================
?>