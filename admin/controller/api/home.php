<?php
use ApiCore\Api;
use System\Session;

$app->post('/change_saison', function($args) {
	if (preg_match('/^\d{4}-\d{4}$/i', $args['saison'])) {
		$session = Session::getInstance();
		$session->selectedSaison = $args['saison'];

		API::sendJSON(true);
	}
	
	API::sendJSON(false);
});
?>