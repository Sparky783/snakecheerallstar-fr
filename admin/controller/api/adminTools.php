<?php
use ApiCore\Api;
use System\ToolBox;
use Snake\Adherent;

if (ToolBox::searchInArray($session->admin_roles, ['admin'])) {
	// Supprime l'adhérent de la base de données.
	$app->post('/remove_adherent', function($args) {
		$adherent = Adherent::getById($args['id_adherent']);
		$result = $adherent->removeFromDatabase();

		API::sendJSON([
			'result' => $result,
		]);
	});

	$app->get('/validation_remove', function($args) {
		$adherents = Adherent::getListBySection((int)$args['id_section']);

		API::sendJSON([
			'adherents' => $adherents,
		]);
	});
}
?>