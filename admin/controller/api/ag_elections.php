<?php
if(ToolBox::SearchInArray($session->roles, array("admin")))
{
	$app->Post("/ag_candidats_list", function($args) {
		include_once(ABSPATH . "model/snake/Candidat.php");

		$candidats = Candidat::GetList();
		$candidatsArray = array();

		foreach($candidats as $candidat)
			$candidatsArray[] = $candidat->ToArray();
		
		$reponse = array(
			'candidats' => $candidatsArray
		);

		API::SendJSON($reponse);
	});

	$app->Post("/ag_candidat_add", function($args) {
		include_once(ABSPATH . "model/snake/Candidat.php");

		if($args['lastname'] != "" && $args['firstname'] != "")
		{
			$candidat = new Candidat();
			$candidat->SetFirstname($args['firstname']);
			$candidat->SetLastname($args['lastname']);
			$candidat->SaveToDatabase();
			
			API::SendJSON($candidat->GetId());
		}
		else
			API::SendJSON(false);
	});

	$app->Post("/ag_candidat_edit", function($args) {
		include_once(ABSPATH . "model/snake/Candidat.php");
		
		$candidat = Candidat::GetById($args['id_candidat']);
		$response = false;
		
		if($candidat !== false)
		{
			$candidat->SetFirstname($args['firstname']);
			$candidat->SetLastname($args['lastname']);
			
			$response = $candidat->SaveToDatabase();
		}
		
		API::SendJSON($response);
	});

	$app->Post("/ag_candidat_remove", function($args) {
		include_once(ABSPATH . "model/snake/Candidat.php");
		
		API::SendJSON(Candidat::RemoveFromDatabase($args['id_candidat']));
	});

	$app->Post("/ag_get_resultat", function($args) {
		include_once(ABSPATH . "model/system/Database.php");
		include_once(ABSPATH . "model/snake/Candidat.php");
		include_once(ABSPATH . "model/snake/Adherent.php");
		
		$database = new Database();

		$response = array();
		$nbVoteRapportMoral = 0;
		$nbVoteRapportFinancier = 0;
		$nbVoteCotisations = 0;

		$candidats = Candidat::GetList();

		// On récupère les résultats
		$result = $database->Query("SELECT * FROM ag_elections");
		$nbVote = 0;
	
		if($result)
		{
			// Comptabilise l'ensemble des votes
			while($data = $result->fetch())
			{
				$nbVote += intval($data['nb_voix']);

				if(boolval($data['rapport_moral']))
					$nbVoteRapportMoral += intval($data['nb_voix']);

				if(boolval($data['rapport_financier']))
					$nbVoteRapportFinancier += intval($data['nb_voix']);

				$others = unserialize($data['others']);
				if(boolval($others['cotisations']))
					$nbVoteCotisations ++;

				$id_candidats = unserialize($data['id_candidats']);
				foreach($id_candidats as $id)
				{
					foreach($candidats as &$cand)
					{
						if($cand->GetId() == $id)
						{
							$cand->AddVotes(intval($data['nb_voix']));
							break;
						}
					}
				}
			}

			// Détermine le nombre de vote attendu
			$adherents = Adherent::GetList(); 
			$nb_waitting_vote = count($adherents);
			
			// Convertit les objets Candidat en tableau.
			$candidatsArray = array();

			foreach($candidats as $candidat)
				$candidatsArray[] = $candidat->ToArray();
				
			$response = array(
				"nbVote" => $nbVote,
				"nbWaittingVote" => $nb_waitting_vote,
				"nbVoteRapportMoral" => $nbVoteRapportMoral,
				"nbVoteRapportFinancier" => $nbVoteRapportFinancier,
				"nbVoteCotisations" => $nbVoteCotisations,
				"candidats" => $candidatsArray
			);
		}


		API::SendJSON($response);
	});
}
?>