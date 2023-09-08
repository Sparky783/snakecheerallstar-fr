<?php
use ApiCore\Api;
use System\Session;
use Snake\Inscription;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\EInscriptionStep;
use Snake\EPaymentType;
use Snake\EUniformOption;
use Snake\SnakeTools;

// ===============================
// ==== Step 1 - Informations ====
// ===============================
$app->post('/inscription-set-informations', function($args) {
	$session = Session::getInstance();
	$session->inscriptionState = EInscriptionStep::Information;

	$inscription = unserialize($session->inscription);
	$inscription->clear();

	if (!isset($args['adherents'])) {
		Api::sendJSON([
			'result' => false,
			'message' => 'Veuillez ajouter au moins un adhérent.'
		]);
	}
	
	// Vérification des adhérents
	foreach ($args['adherents'] as $adherent) {
		$adh = new Adherent();
		$testAdherent = $adh->setInformation($adherent);

		if (count($testAdherent) > 0) {
			$message = '';

			foreach ($testAdherent as $err) {
				$message .= "- {$err}<br />";
			}

			Api::sendJSON([
				'result' => false,
				'message' => $message
			]);
		}

		$section = $adh->getSection();
		
		// Si l'adhérent appartien à une section
		if ($section === null) {
			Api::sendJSON([
				'result' => false,
				'message' => "Nous sommes désolé, " . $adh->getFirstname() . " est trop jeune pour s'inscrire."
			]);
		}

		// Si la section n'est pas pleine.
		if ($section->getNbMembers() >= $section->getNbMaxMembers()) {
			Api::sendJSON([
				'result' => false,
				'message' => "Nous sommes désolé, il n'y a plus de place dans la section " . $section->getName() . " pour " . $adh->getFirstname() . "."
			]);
		}

		$adh->setUniformOption(EUniformOption::Rent);
		$inscription->addAdherent($adh);
	}

	if (!isset($args['tuteurs'])) {
		Api::sendJSON([
			'result' => false,
			'message' => 'Veuillez ajouter au moins un tuteur.'
		]);
	}

	foreach ($args['tuteurs'] as $tuteur) {
		$tut = new Tuteur();
		$testTuteur = $tut->setInformation($tuteur);

		if (count($testTuteur) > 0) {
			$message = '';

			foreach ($testTuteur as $err) {
				$message .= "- {$err}<br />";
			}

			Api::sendJSON([
				'result' => false,
				'message' => $message
			]);
		}
		
		$inscription->addTuteur($tut);
	}

	if ($args['authorisation']['acceptTermes'] !== 'true') {
		Api::sendJSON([
			'result' => false,
			'message' => 'Veuillez accepter les conditions afin de pouvoir continuer.'
		]);
	}

	// Prépare les données à afficher
	$amountToPay = $inscription->computeFinalPrice();
	$reductions = [];
	foreach ($inscription->getPayment()->getReductions() as $resduction) {
		$reductions[] = $resduction->toArray();
	}

	$session->inscriptionState = EInscriptionStep::Payment;
	$session->inscription = serialize($inscription);

	Api::sendJSON([
		'result' => true,
		'message' => '',
		'amountToPay' => $amountToPay,
		'reductions' => $reductions
	]);
});

// ==========================
// ==== Step 2 - Payment ====
// ==========================
$app->post('/inscription-validate-payment', function($args) {
	$session = Session::getInstance();

	$inscription = unserialize($session->inscription);

	if ($session->inscriptionState !== EInscriptionStep::Payment &&
		$session->inscriptionState !== EInscriptionStep::Validation) {
		Api::sendJSON([
			'result' => false,
			'message' => 'Une erreur est survenue. Veuillez rafraichir votre page.'
		]);
	}

	$message = '';

	switch ($args['method']) {
		case 'optionEspece':
			$inscription->getPayment()->setMethod(EPaymentType::Espece);
			$session->inscriptionState = EInscriptionStep::Validation;
			break;

		case 'optionCheque':
			$nbDeadlines = (int)$args['deadlines'];

			if ($nbDeadlines >= 1 && $nbDeadlines <= 4) {
				$inscription->getPayment()->setMethod(EPaymentType::Cheque);
				$inscription->getPayment()->setNbDeadlines($nbDeadlines);

				$session->inscriptionState = EInscriptionStep::Validation;
			} else {
				$message = "Veuillez choisir un nombre d'échéance entre 1 et 4.";
			}
			break;

		case 'optionEnLigne':
			$inscription->getPayment()->setMethod(EPaymentType::Internet);
			
			if ($inscription->getPayment()->isDone()) {
				$session->inscriptionState = EInscriptionStep::Validation;
			} else {
				$message = 'Veuillez valider votre paiement en ligne.';
			}
			break;
	
		default:
			$message = 'Veuillez choisir un moyen de paiement.';
			break;
	}

	if ($session->inscriptionState !== EInscriptionStep::Validation) {
		Api::sendJSON([
			'result' => false,
			'message' => $message
		]);
	}

	// Sauvegarde en base de donnée de l'inscription
	if (!$inscription->saveToDatabase()) {
		Api::sendJSON([
			'result' => false,
			'message' => "Une erreur est survenue lors de l'enregistrement de votre inscription. Veuillez réessayer."
		]);
	}

	// ================================================
	// E-mail récapitulatif + facture
	// ================================================
	$payment = $inscription->getPayment();
	$emails = [];

	foreach ($inscription->getTuteurs() as $tuteur) {
		SnakeTools::sendRecap($inscription, $tuteur);
		SnakeTools::sendBill($payment, $tuteur);
		$emails[] = $tuteur->getEmail();
	}

	if(count($emails) > 0) {
		$message = 'Un E-mail récapitulatif a été envoyé à ' . implode(', ', $emails);
	}

	$session->inscription = serialize($inscription);
	
	Api::sendJSON([
		'result' => true,
		'message' => $message
	]);
});

// ===============================
// ==== Step 3 - Confirmation ====
// ===============================
$app->post('/close_inscription', function($args) {
	$session = Session::getInstance();
	$session->inscription = serialize(new Inscription());
	$session->inscriptionState = EInscriptionStep::Information;
});
?>