<?php
use ApiCore\Api;
use System\Session;

$app->Post("/change_saison", function($args) {
	if(preg_match('/^\d{4}-\d{4}$/i', $args['saison']))
	{
		$session = Session::getInstance();
		$session->selectedSaison = $args['saison'];

		API::SendJSON(true);
	}
	
	API::SendJSON(false);
});
?>