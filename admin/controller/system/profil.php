<?php
// ==== Access security ====
if(!$session->isConnected)
	WebSite::Redirect("login", true);
// =========================

global $router;

$name = $session->name;
?>