<?php
use ApiCore\Api;
use System\Session;
use System\ToolBox;
use Snake\Inscription;
use Snake\SnakeTools;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Reduction;

// ============================
// ==== Step 1 - Adherents ====
// ============================
$app->post('/inscription_validate_adherents', function($args) {
	$session = Session::getInstance();
	$session->inscription->clearAdherents(); // Nettoie la liste des adhérents.

	$result = true;
	$message = '';

	if (isset($args['adherents'])) {
		$listAdherents = [];

		$nbBySection = SnakeTools::nbBySection();

		// Vérification des adhérents
		foreach($args['adherents'] as $adherent) {
			$adh = new Adherent();
			
			if ($adh->setInformation($adherent)) {
				// Si l'adhérent appartien à une section
				if($adh->getSection() !== null) {
					// Si la section n'est pas pleine.
					if($nbBySection[$adh->getSection()->getId()] < $adh->getSection()->getNbMaxMembers()) {
						$listAdherents[] = $adh;
					} else {
						$result = false;
						$message = "Désolé, la section " . $adh->getSection()->getName() . " pour " . $adh->getFirstname() . " est pleine.";
					}
				} else {
					$result = false;
					$message = "Désolé, " . $adh->getFirstname() . " est trop jeune pour s'inscrire.";
				}
			} else {
				$result = false;
				$message = "L'un des champs n'est pas correctement rempli.";
			}
		}

		if ($result) {
			// Enregistrement des adhérents dans l'objet inscription
			foreach ($listAdherents as $adherent) {
				$session->inscription->addAdherent($adherent);
			}

			$session->inscription->changeState(Inscription::$STEPS['Tuteurs']);
		}
	} else {
		$result = false;
		$message = "Veuillez ajouter au moins un adhérent.";
	}

	$reponse = [
		'result' => $result,
		'message' => $message
	];

	API::sendJSON($reponse);
});

// ==========================
// ==== Step 2 - Tuteurs ====
// ==========================
$app->post('/inscription_validate_tuteurs', function($args) {
	$session = Session::getInstance();
	$session->inscription->ClearTuteurs(); // Nettoie la liste des adhérents.
	
	$result = true;
	$message = '';

	if (isset($args['tuteurs'])) {
		$listTuteurs = array();

		foreach ($args['tuteurs'] as $tuteur) {
			$tut = new Tuteur();
			
			if ($tut->SetInformation($tuteur)) {
				$listTuteurs[] = $tut;
			} else {
				$result = false;
				$message = "L'un des champs n'est pas correctement rempli.";
			}
		}

		if ($result) {
			// Enregistrement des tuteurs dans l'objet inscription
			foreach($listTuteurs as $tuteur) {
				$session->inscription->addTuteur($tuteur);
			}

			$session->inscription->changeState(Inscription::$STEPS['Authorization']);
		}
	} else {
		$result = false;
		$message = "Veuillez ajouter au moins un tuteur.";
	}

	$reponse = [
		'result' => $result,
		'message' => $message
	];

	API::sendJSON($reponse);
});

// ================================
// ==== Step 3 - Authorization ====
// ================================
$app->post('/inscription_validate_authorization', function($args) {
	$session = Session::getInstance();
	
	$result = true;
	$message = '';

	if($args['authorization'] === 'true') {
		$session->inscription->setAuthorization(true);
		
		// Ajout d'une réduction pour les fratries.
		if(count($session->inscription->getAdherents()) > 1) {
			$reduc = new Reduction();
			$reduc->setType(Reduction::$TYPE['Percentage']);
			$reduc->setValue(15); // 15%
			$reduc->setSujet("Tarif fratrie");
			
			$session->inscription->getPayment()->addReduction($reduc);
		}

		$session->inscription->computeCotisation();
		$session->inscription->changeState(Inscription::$STEPS['Payment']);
	}
	else
	{
		$result = false;
		$message = "Veuillez accepter les conditions afin de pouvoir continuer.";
	}
	
	$reponse = array(
		'result' => $result,
		'message' => $message
	);

	API::SendJSON($reponse);
});

// ==========================
// ==== Step 4 - Payment ====
// ==========================
$app->Post("/inscription_validate_payment", function($args) {
	$session = Session::getInstance();

	$message = "";

	if($args['method'] == "optionEspece")
	{
		$session->inscription->GetPayment()->SetMethod(Payment::$METHODS['Espece']);
		$session->inscription->ChangeState(Inscription::$STEPS['Validation']);
	}
	else if($args['method'] == "optionCheque")
	{
		$nbDeadlines = intval($args['deadlines']);

		if($nbDeadlines >= 1 && $nbDeadlines <= 4)
		{
			$session->inscription->GetPayment()->SetMethod(Payment::$METHODS['Cheque']);
			$session->inscription->GetPayment()->SetNbDeadlines($nbDeadlines);

			$session->inscription->ChangeState(Inscription::$STEPS['Validation']);
		}
		else
		{
			$message = "Veuillez choisir un nombre d'échéance entre 1 et 4.";
		}
	}
	else if($args['method'] == "optionEnLigne")
	{
		$session->inscription->GetPayment()->SetMethod(Payment::$METHODS['Internet']);
		
		if($session->inscription->GetPayment()->IsDone())
			$session->inscription->ChangeState(Inscription::$STEPS['Validation']);
		else
		{
			$message = "Veuillez valider votre paiement en ligne.";
		}
	}
	else
	{
		$message = "Veuillez choisir un moyen de paiement.";
	}

	// Ajout de la réduction suite au Pass Sport mis en place par le gouvernement
	if(isset($args['passSport']))
	{
		if(ToolBox::StringToBool($args['passSport']))
		{
			$reduc = new Reduction();
			$reduc->SetType(Reduction::$TYPE['Amount']);
			$reduc->SetValue(50); // 50€
			$reduc->SetSujet("Pass Sport");
			
			$session->inscription->GetPayment()->AddReduction($reduc);
		}
	}
	
	// Sauvegarde en base de donnée de l'inscription
	$result = false;
	if($session->inscription->GetState() == Inscription::$STEPS['Validation'])
		$result = $session->inscription->SaveToDatabase();

	$reponse = array(
		'result' => $result,
		'message' => $message
	);
	
	API::SendJSON($reponse);
});

// ===============================
// ==== Step 5 - Confirmation ====
// ===============================
$app->Post("/close_inscription", function($args) {
	$session = Session::getInstance();
	$session->inscription = new Inscription();
});
?>