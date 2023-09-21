<?php
use ApiCore\Api;
use System\ToolBox;
use System\Session;
use Snake\SnakeMailer;
use Snake\Tuteur;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'secretaire'])) {
	$app->post('/email', function($args) {
		$session = Session::getInstance();

		// Récupération des données
		$subject = strip_tags($args['subject']);
		$message = strip_tags($args['message']);

		if ($subject !== '' && $message !== '') {
			if ($args['id_section'] === 'all') {
				$tuteurs = Tuteur::getList($session->selectedSaison);
			} else {
				$tuteurs = Tuteur::getListBySection($args['id_section']);
			}

			if (SnakeMailer::sendMessage($subject, $message, $tuteurs)) {
				$reponse = [
					'error' => false,
					'message' => "Votre message à bien été envoyé."
				];
			} else {
				$reponse = [
					'error' => true,
					'message' => "Désolé, une erreur est survenue."
				];
			}
		} else {
			$reponse = [
				'error' => true,
				'message' => "L'un des champs n'est pas correctement rempli."
			];
		}

		API::sendJSON($reponse);
	});
}
?>