<?php
use System\WebSite;

// ==== Access security ====
if(!$session->admin_isConnected)
	WebSite::Redirect("login", true);
// =========================

global $router;

$name = $session->admin_name;
?>