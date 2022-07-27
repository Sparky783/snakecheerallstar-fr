<?php
// ==== Access security ====
if(!ToolBox::SearchInArray($session->roles, array("admin")))
	WebSite::Redirect("login", true);
// =========================
?>