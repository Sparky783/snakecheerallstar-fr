<?php
use ApiCore\Api;
use System\ToolBox;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Inscription;
use Snake\EPaymentType;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster'])) {
	$app->post('/adherents_add', function($args) {
		$inscription = new Inscription();
		
		//==== Adhérents ====
		if (!isset($args['adherents'])) {
			API::sendJSON([
				'result' => false,
				'message' => 'Veuillez ajouter au moins un adhérent.'
			]);

			return;
		}

		$listAdherents = [];

		// Vérification des adhérents
		foreach ($args['adherents'] as $adherent) {
			$adh = new Adherent();
			
			if (count($adh->setInformation($adherent)) > 0) {
				API::sendJSON([
					'result' => false,
					'message' => "L'un des champs n'est pas correctement rempli."
				]);

				return;
			}

			// Si l'adhérent appartien à une section
			if ($adh->getSection() === null) {
				API::sendJSON([
					'result' => false,
					'message' => "Attention, {$adh->getFirstname()} est trop jeune pour s'inscrire."
				]);

				return;
			}

			// Si la section n'est pas pleine.
			if ($adh->getSection()->getNbMembers() >= $adh->getSection()->getNbMaxMembers()) {
				API::sendJSON([
					'result' => false,
					'message' => "Attention, la section {$adh->getSection()->getName()} pour {$adh->getFirstname()} est pleine."
				]);

				return;
			}

			$listAdherents[] = $adh;
		}

		// Enregistrement des adhérents dans l'objet inscription
		foreach ($listAdherents as $adherent) {
			$inscription->addAdherent($adherent);
		}

		//==== Tuteurs ====
		if (!isset($args['tuteurs'])) {
			API::sendJSON([
				'result' => false,
				'message' => 'Veuillez ajouter au moins un tuteur.'
			]);

			return;
		}

		$listTuteurs = [];

		foreach ($args['tuteurs'] as $tuteur) {
			$tut = new Tuteur();
			
			if (count($tut->setInformation($tuteur)) > 0) {
				API::sendJSON([
					'result' => false,
					'message' => "L'un des champs n'est pas correctement rempli."
				]);

				return;
			}

			$listTuteurs[] = $tut;
		}

		// Enregistrement des tuteurs dans l'objet inscription
		foreach ($listTuteurs as $tuteur) {
			$inscription->addTuteur($tuteur);
		}

		//==== Proccess ====
		$inscription->computeFinalPrice();

		//==== Payment ====
		$payment = $args['payment'];

		switch ($payment['mode']) {
			case 'espece':
				$inscription->getPayment()->setMethod(EPaymentType::Espece);
				break;

			case 'cheque':
				if (!isset($payment['deadlines'])) {
					API::sendJSON([
						'result' => false,
						'message' => "Veuillez choisir un nombre d'échéance entre 1 et 4."
					]);
					return;
				}
				
				$nbDeadlines = (int)$payment['deadlines'];

				if($nbDeadlines < 1 || $nbDeadlines > 4) {
					API::sendJSON([
						'result' => false,
						'message' => "Veuillez choisir un nombre d'échéance entre 1 et 4."
					]);
					return;
				}
				
				$inscription->getPayment()->setMethod(EPaymentType::Cheque);
				$inscription->getPayment()->setNbDeadlines($nbDeadlines);
				break;

			case 'virement':
				$inscription->getPayment()->setMethod(EPaymentType::Virement);
				break;

			default:
				API::sendJSON([
					'result' => false,
					'message' => 'Veuillez choisir un moyen de paiement.'
				]);
				return;
		}
		
		// Sauvegarde en base de donnée de l'inscription
		$result = $inscription->saveToDatabase();

		API::sendJSON([
			'result' => $result,
			'message' => "L'adhérent à bien été inscrit"
		]);
	});
}
?>