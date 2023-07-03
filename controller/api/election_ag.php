<?php
use ApiCore\Api;
use Common\ReCaptcha;
use System\Database;
use System\Admin;
use Snake\Tuteur;

$app->Post("/election_ag", function($args) {
	global $router;

	// ReCaptcha
	$secret = "6LeNSLcUAAAAAKYgk_tGwoFD4sEwV2sRKZAKnxHL"; // A modifier
	$responseCaptcha = null;
	$reCaptcha = new ReCaptcha($secret);
	if(ENV != "DEV" && $args["g-recaptcha-response"]) {
		$responseCaptcha = $reCaptcha->verifyResponse(
			$_SERVER["REMOTE_ADDR"],
			$args["g-recaptcha-response"]
		);
	}

	// Récupération des données
	$reponse = array();
	$name = strip_tags($args['name']);
	$email = strip_tags($args['email']);

	$test = true;
	if(!preg_match("/^[a-zéèàêâùïüëçA-Z -]{2,40}$/", $name)) $test = false;
	if(!preg_match("/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/", $email)) $test = false;

	if($test)
	{
		if(($responseCaptcha != null && $responseCaptcha->success) || ENV == "DEV")
		{
			$database = new Database();

			// Recherche si la personne est un membre du club
			$found = false;
			
			$tuteurs = Tuteur::GetList();
			foreach($tuteurs as $tuteur)
			{
				if($tuteur->GetEmail() == $email)
				{
					$found = true;
					break;
				}
			}

			$admins = Admin::GetList();
			foreach($admins as $admin)
			{
				if($admin->GetEmail() == $email)
				{
					$found = true;
					break;
				}
			}

			if($found)
			{
				$votes = array();
				$rech = $database->query("SELECT * FROM ag_candidats");

				while($data = $rech->fetch())
				{
					
					if(isset($args['candidat' . $data['id_candidat']]))
					{
						$val = strip_tags($args['candidat' . $data['id_candidat']]);

						if($val == "yes")
							$votes[] = intval($data['id_candidat']);
					}	
				}

				// Détermine le poids du vote (Nombre de vois en fonction du nombre d'enfant).
				$tuteurs = $database->Query(
					"SELECT COUNT(*) FROM adherent_tuteur JOIN tuteurs ON adherent_tuteur.id_tuteur = tuteurs.id_tuteur WHERE tuteurs.email=:email",
					array("email" => $email)
				);
				$data = $tuteurs->fetch();
				$nbVoix = intval($data['COUNT(*)']);

				if($nbVoix == 0)
					$nbVoix = 1;

				// Recherche si le vote existe déjà.
				$rech = $database->query(
					"SELECT COUNT(*) FROM ag_elections WHERE email=:email",
					array("email" => $email)
				);
				$resRech = $rech->fetch();

				if(intval($resRech['COUNT(*)']) > 0)
				{
					$rechElec = $database->query(
						"SELECT * FROM ag_elections WHERE email=:email",
						array("email" => $email)
					);
					$elec = $rechElec->fetch();
					

					$data = array(
						//"name" => $name,
						"email" => $email,
						"nb_voix" => $nbVoix,
						"rapport_moral" => $args['rapportMoral'] == "yes" ? true : false,
						"rapport_financier" => $args['rapportFinancier'] == "yes" ? true : false,
						"id_candidats" => serialize($votes),
						"others" => serialize(array("cotisations" => $args['cotisations'] == "yes" ? true : false))
					);
					$database->Update("ag_elections", "id_election", intval($elec['id_election']), $data);
				}
				else
				{
					$data = array(
						//"name" => $name,
						"email" => $email,
						"nb_voix" => $nbVoix,
						"rapport_moral" => $args['rapportMoral'] == "yes" ? true : false,
						"rapport_financier" => $args['rapportFinancier'] == "yes" ? true : false,
						"id_candidats" => serialize($votes),
						"others" => serialize(array("cotisations" => $args['cotisations'] == "yes" ? true : false))
					);
					$database->Insert("ag_elections", $data);
				}

				$reponse = array(
					'message' => "Merci ! Votre vote a bien été enregistré =)<br /><br /><a class='btn btn-snake' href='" . URL . "'>Retourner sur le site</a>"
				);
			}
			else
			{
				$reponse = array(
					'error' => true,
					'errorMessage' => "Vous devez être membre du club pour pouvoir voter.",
					'message' => "Vous devez être membre du club pour pouvoir voter.<br /><br /><a class='btn btn-snake' href='" . $router->GetUrl("election_ag") . "'>Reessayer</a>"
				);
			}
		}
		else
		{
			$reponse = array(
				'error' => true,
				'errorMessage' => "Le controle anti-robot n'est pas dévérouillé.",
				'message' => "Le controle anti-robot n'est pas dévérouillé.<br /><br /><a class='btn btn-snake' href='" . $router->GetUrl("election_ag") . "'>Reessayer</a>"
			);
		}
	}
	else
	{
		$reponse = array(
			'error' => true,
			'errorMessage' => "L'un des champs n'est pas correctement rempli.",
			'message' => "L'un des champs n'est pas correctement rempli.<br /><br /><a class='btn btn-snake' href='" . $router->GetUrl("election_ag") . "'>Reessayer</a>"
		);
	}

	API::SendJSON($reponse);
});
?>