<?php
use ApiCore\Api;
use Snake\Inscription;
use System\ToolBox;
use System\Session;
use Snake\SnakeMailer;
use Snake\Tuteur;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
	$app->post('/adherent_info_add_tuteur', function($args) {
		$session = Session::getInstance();

		$tuteur = new Tuteur();
		$response = [];
			
		if (count($tuteur->setInformation($args)) === 0) {
			$tuteur->addAdherent(unserialize($session->selectedAdherent));

			if ($tuteur->saveToDatabase()) {
				$response = [
					'id' => $tuteur->GetId(),
					'message' => "Le tuteur a bien été ajouté."
				];
			} else {
				$response = [
					'message' => "Une erreur lors de l'ajout dans la base de données est survenue."
				];
			}
		} else {
			$response = [
				'message' => "L'un des champs n'est pas correctement rempli."
			];
		}

		API::sendJSON($response);
	});
}

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'tresorier', 'secretaire'])) {
	$app->post('/adherent_info_send_bill', function($args) {
		$session = Session::getInstance();
		$adherent = unserialize($session->selectedAdherent);
		
		if (ENV === 'DEV') {
			$tuteur = new Tuteur();
			$tuteur->SetFirstname(TITLE . ' - DEV');
			$tuteur->SetEmail(EMAIL_WABMASTER);
			$tuteur->SetPhone('00 00 00 00 00');
		} else {
			if ($args['id_tuteur'] === 'snake') {
				$tuteur = new Tuteur();
				$tuteur->SetFirstname(TITLE);
				$tuteur->SetEmail(EMAIL_CONTACT);
				$tuteur->SetPhone('00 00 00 00 00');
			} else {
				$tuteur = Tuteur::getById($args['id_tuteur']);
			}
		}

		$result = SnakeMailer::sendBill($adherent->getPayment(), $tuteur);
		$response = [];
		
		if ($result) {
			$response = [
				'message' => "La facture à bien été envoyé."
			];
		} else {
			$response = [
				'message' => "Une erreur est survenue, veuillez réessayer."
			];
		}

		API::sendJSON($response);
	});
}

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'secretaire'])) {
	$app->post('/adherent_info_send_recap', function($args) {
		$session = Session::getInstance();
		$adherent = unserialize($session->selectedAdherent);
		
		if (ENV === 'DEV') {
			$tuteur = new Tuteur();
			$tuteur->setFirstname(TITLE . ' - DEV');
			$tuteur->setEmail(EMAIL_WABMASTER);
			$tuteur->setPhone('00 00 00 00 00');
		} else {
			if ($args['id_tuteur'] === 'snake') {
				$tuteur = new Tuteur();
				$tuteur->setFirstname(TITLE);
				$tuteur->setEmail(EMAIL_CONTACT);
				$tuteur->setPhone('00 00 00 00 00');
			} else {
				$tuteur = Tuteur::getById($args['id_tuteur']);
			}
		}

		$inscription = Inscription::getByPaymentId($adherent->getPayment()->getId());
		$result = SnakeMailer::sendRecap($inscription, $tuteur);
		$response = [];
		
		if ($result) {
			$response = [
				'message' => "Le récapitulatif à bien été envoyé."
			];
		} else {
			$response = [
				'message' => "Une erreur est survenue, veuillez réessayer."
			];
		}

		API::sendJSON($response);
	});
}
?>