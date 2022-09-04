<?php
if(ToolBox::SearchInArray($session->admin_roles, array("admin", "member")))
{
	$app->Post("/adherent_info_add_tuteur", function($args) {
		include_once(ABSPATH . "model/snake/Tuteur.php");

		$session = Session::GetInstance();

		$tuteur = new Tuteur();
		$response = array();
			
		if($tuteur->SetInformation($args))
		{
			$tuteur->AddAdherent(unserialize($session->selectedAdherent));

			if($tuteur->SaveToDatabase())
			{
				$response = array(
					'id' => $tuteur->GetId(),
					'message' => "Le tuteur a bien été ajouté."
				);
			}
			else
			{
				$response = array(
					'message' => "Une erreur lors de l'ajout dans la base de données est survenue."
				);
			}
		}
		else
		{
			$response = array(
				'message' => "L'un des champs n'est pas correctement rempli."
			);
		}

		API::SendJSON($response);
	});
}

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "tresorier", "secretaire")))
{
	$app->Post("/adherent_info_send_bill", function($args) {
		include_once(ABSPATH . "model/snake/SnakeTools.php");
		include_once(ABSPATH . "model/snake/Adherent.php");
		include_once(ABSPATH . "model/snake/Tuteur.php");

		$session = Session::GetInstance();

		$adherent = unserialize($session->selectedAdherent);
		
		if(ENV == "DEV")
		{
			$tuteur = new Tuteur();
			$tuteur->SetFirstname(TITLE . " - DEV");
			$tuteur->SetEmail(EMAIL_WABMASTER);
			$tuteur->SetPhone("00 00 00 00 00");
		}
		else
		{
			if($args['id_tuteur'] == "snake")
			{
				$tuteur = new Tuteur();
				$tuteur->SetFirstname(TITLE);
				$tuteur->SetEmail(EMAIL_CONTACT);
				$tuteur->SetPhone("00 00 00 00 00");
			}
			else
				$tuteur = Tuteur::GetById($args['id_tuteur']);
		}

		$result = SnakeTools::SendBill($adherent->GetPayment(), $tuteur);

		$response = array();
		
		if($result) {
			$response = array(
				"message" => "La facture à bien été envoyé."
			);
		} else {
			$response = array(
				"message" => "Une erreur est survenue, veuillez réessayer."
			);
		}

		API::SendJSON($response);
	});
}

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "secretaire")))
{
	$app->Post("/adherent_info_send_recap", function($args) {
		include_once(ABSPATH . "model/snake/SnakeTools.php");
		include_once(ABSPATH . "model/snake/Adherent.php");
		include_once(ABSPATH . "model/snake/Tuteur.php");

		$session = Session::GetInstance();

		$adherent = unserialize($session->selectedAdherent);
		
		if(ENV == "DEV")
		{
			$tuteur = new Tuteur();
			$tuteur->SetFirstname(TITLE . " - DEV");
			$tuteur->SetEmail(EMAIL_WABMASTER);
			$tuteur->SetPhone("00 00 00 00 00");
		}
		else
		{
			if($args['id_tuteur'] == "snake")
			{
				$tuteur = new Tuteur();
				$tuteur->SetFirstname(TITLE);
				$tuteur->SetEmail(EMAIL_CONTACT);
				$tuteur->SetPhone("00 00 00 00 00");
			}
			else
				$tuteur = Tuteur::GetById($args['id_tuteur']);
		}

		$result = SnakeTools::SendRecap($adherent->GetPayment(), $tuteur);

		$response = array();
		
		if($result) {
			$response = array(
				"message" => "Le récapitulatif à bien été envoyé."
			);
		} else {
			$response = array(
				"message" => "Une erreur est survenue, veuillez réessayer."
			);
		}

		API::SendJSON($response);
	});
}
?>