<?php
use ApiCore\Api;
use System\Session;
use System\ToolBox;
use Snake\Inscription;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Reduction;
use Snake\EInscriptionStep;
use Snake\EPaymentType;
use Snake\EReductionType;
use Snake\EUniformOption;
use Snake\SnakeTools;

// ===============================
// ==== Step 1 - Informations ====
// ===============================
$app->post('/inscription-set-informations', function($args) {
	$session = Session::getInstance();
	$session->inscription->clear();
	$session->inscriptionState = EInscriptionStep::Information;

	if (!isset($args['adherents'])) {
		Api::sendJSON([
			'result' => false,
			'message' => 'Veuillez ajouter au moins un adhérent.'
		]);
	}

	$listAdherents = [];

	// Vérification des adhérents
	foreach ($args['adherents'] as $adherent) {
		$adh = new Adherent();
		
		if (!$adh->setInformation($adherent)) {
			Api::sendJSON([
				'result' => false,
				'message' => "L'un des champs des adhérents n'est pas correctement rempli."
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
		$session->inscription->addAdherent($adh);
	}

	if (!isset($args['tuteurs'])) {
		Api::sendJSON([
			'result' => false,
			'message' => 'Veuillez ajouter au moins un tuteur.'
		]);
	}

	foreach ($args['tuteurs'] as $tuteur) {
		$tut = new Tuteur();
		
		if (!$tut->setInformation($tuteur)) {
			Api::sendJSON([
				'result' => false,
				'message' => "L'un des champs des tuteurs n'est pas correctement rempli."
			]);
		}
		
		$session->inscription->addTuteur($tut);
	}

	if ($args['authorisation']['acceptTermes'] !== 'true') {
		Api::sendJSON([
			'result' => false,
			'message' => 'Veuillez accepter les conditions afin de pouvoir continuer.'
		]);
	}

	// Prépare les données à afficher
	$amountToPay = $session->inscription->computeFinalPrice();
	$reductions = [];
	foreach ($session->inscription->getPayment()->getReductions() as $resduction) {
		$reductions[] = $resduction->toArray();
	}

	$session->inscriptionState = EInscriptionStep::Payment;

	Api::sendJSON([
		'result' => true,
		'message' => '',
		'amountToPay' => $amountToPay,
		'reductions' => $reductions
	]);
});

// ==========================
// ==== Step 4 - Payment ====
// ==========================
$app->post('/inscription-validate-payment', function($args) {
	$session = Session::getInstance();

	if ($session->inscriptionState !== EInscriptionStep::Payment) {
		Api::sendJSON([
			'result' => false,
			'message' => 'Une erreur est survenue. Veuillez rafraichir votre page.'
		]);
	}

	$message = '';

	switch ($args['method']) {
		case 'optionEspece':
			$session->inscription->getPayment()->setMethod(EPaymentType::Espece);
			$session->inscriptionState = EInscriptionStep::Validation;
			break;

		case 'optionCheque':
			$nbDeadlines = (int)$args['deadlines'];

			if ($nbDeadlines >= 1 && $nbDeadlines <= 4) {
				$session->inscription->getPayment()->setMethod(EPaymentType::Cheque);
				$session->inscription->getPayment()->setNbDeadlines($nbDeadlines);

				$session->inscriptionState = EInscriptionStep::Validation;
			} else {
				$message = "Veuillez choisir un nombre d'échéance entre 1 et 4.";
			}
			break;

		case 'optionEnLigne':
			$session->inscription->getPayment()->setMethod(EPaymentType::Internet);
			
			if ($session->inscription->getPayment()->isDone()) {
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
	$result = $session->inscription->saveToDatabase();

	if (!$result) {
		$message = "Une erreur est survenue lors de l'enregistrement de votre inscription. Veuillez réessayer.";
	}

	// ================================================
	// E-mail récapitulatif + facture
	// ================================================
	$payment = $session->inscription->getPayment();
	$emails = [];

	foreach ($session->inscription->getTuteurs() as $tuteur) {
		SnakeTools::sendRecap($session->inscription, $tuteur);
		SnakeTools::sendBill($payment, $tuteur);
		$emails[] = $tuteur->getEmail();
	}

	if(count($emails) > 0) {
		$message = 'Un E-mail récapitulatif a été envoyé à ' . implode(', ', $emails);
	}
	
	Api::sendJSON([
		'result' => $result,
		'message' => $message
	]);
});

// ===============================
// ==== Step 5 - Confirmation ====
// ===============================
$app->post('/close_inscription', function($args) {
	$session = Session::getInstance();
	$session->inscription = new Inscription();
	$session->inscriptionState = EInscriptionStep::Information;
});
?>