<?php
use ApiCore\Api;
use System\ToolBox;
use Snake\SnakeTools;
use Snake\Adherent;
use Snake\Tuteur;
use Snake\Reduction;
use Snake\Inscription;

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster")))
{
	$app->Post("/adherents_add", function($args) {
		$inscription = new Inscription();
		
		//==== Adhérents ====
		if(!isset($args['adherents']))
		{
			API::SendJSON(array(
				"result" => false,
				"message" => "Veuillez ajouter au moins un adhérent."
			));
			return;
		}

		$listAdherents = array();
		$nbBySection = SnakeTools::NbBySection();

		// Vérification des adhérents
		foreach($args['adherents'] as $adherent)
		{
			$adh = new Adherent();
			
			if(!$adh->SetInformation($adherent))
			{
				API::SendJSON(array(
					"result" => false,
					"message" => "L'un des champs n'est pas correctement rempli."
				));
				return;
			}

			// Si l'adhérent appartien à une section
			if($adh->GetSection() == null)
			{
				API::SendJSON(array(
					"result" => false,
					"message" => "Attention, " . $adh->GetFirstname() . " est trop jeune pour s'inscrire."
				));
				return;
			}

			// Si la section n'est pas pleine.
			if($nbBySection[$adh->GetSection()->GetId()] >= $adh->GetSection()->GetNbMaxMembers())
			{
				API::SendJSON(array(
					"result" => false,
					"message" => "Attention, la section " . $adh->GetSection()->GetName() . " pour " . $adh->GetFirstname() . " est pleine."
				));
				return;
			}

			$listAdherents[] = $adh;
		}

		// Enregistrement des adhérents dans l'objet inscription
		foreach($listAdherents as $adherent)
			$inscription->AddAdherent($adherent);

		//==== Tuteurs ====
		if(!isset($args['tuteurs']))
		{
			API::SendJSON(array(
				"result" => false,
				"message" => "Veuillez ajouter au moins un tuteur."
			));
			return;
		}

		$listTuteurs = array();

		foreach($args['tuteurs'] as $tuteur)
		{
			$tut = new Tuteur();
			
			if(!$tut->SetInformation($tuteur))
			{
				API::SendJSON(array(
					"result" => false,
					"message" => "L'un des champs n'est pas correctement rempli."
				));
				return;
			}

			$listTuteurs[] = $tut;
		}

		// Enregistrement des tuteurs dans l'objet inscription
		foreach($listTuteurs as $tuteur)
			$inscription->AddTuteur($tuteur);

		//==== Proccess ====
		$inscription->SetAuthorization(true);
		
		// Ajout d'une réduction pour les fratries.
		if(count($inscription->GetAdherents()) > 1)
		{
			$reduc = new Reduction();
			$reduc->SetType(Reduction::$TYPE['Percentage']);
			$reduc->SetValue(15); // 15%
			$reduc->SetSujet("Tarif fratrie");
			
			$inscription->GetPayment()->AddReduction($reduc);
		}

		$inscription->ComputeCotisation();

		//==== Payment ====
		$payment = $args['payment'];
		if($payment['mode'] == "espece")
		{
			$inscription->GetPayment()->SetMethod(Payment::$METHODS['Espece']);
		}
		else if($payment['mode'] == "cheque")
		{
			$nbDeadlines = intval($payment['deadlines']);

			if($nbDeadlines >= 1 && $nbDeadlines <= 4)
			{
				$inscription->GetPayment()->SetMethod(Payment::$METHODS['Cheque']);
				$inscription->GetPayment()->SetNbDeadlines($nbDeadlines);
			}
			else
			{
				API::SendJSON(array(
					"result" => false,
					"message" => "Veuillez choisir un nombre d'échéance entre 1 et 4."
				));
				return;
			}
		}
		else if($payment['mode'] == "virement")
		{
			$inscription->GetPayment()->SetMethod(Payment::$METHODS['Virement']);
		}
		else
		{
			API::SendJSON(array(
				"result" => false,
				"message" => "Veuillez choisir un moyen de paiement."
			));
			return;
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
				
				$inscription->GetPayment()->AddReduction($reduc);
			}
		}
		
		// Sauvegarde en base de donnée de l'inscription
		$result = $inscription->SaveToDatabase();

		API::SendJSON(array(
			"result" => $result,
			"message" => "L'adhérent à bien été inscrit"
		));
	});
}
?>