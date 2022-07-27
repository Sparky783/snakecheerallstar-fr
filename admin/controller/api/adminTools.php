<?php
if(ToolBox::SearchInArray($session->roles, array("admin")))
{
	// Supprime l'adhérent de la base de données.
	$app->Post("/remove_adherent", function($args) {
		include_once(ABSPATH . "model/snake/Adherent.php");

		$adherent = Adherent::GetById($args['id_adherent']);
		$result = $adherent->RemoveFromDatabase();

		API::SendJSON(array(
			'result' => $result,
		));
	});

	$app->Get("/validation_remove", function($args) {
		include_once(ABSPATH . "model/system/Database.php");

		$database = new Database();
		$rech = $database->Query(
			"SELECT * FROM adherents WHERE id_section=:id_section",
			array(
				"id_section" => intval($args['id_section'])
			)
		);

		$adherents = array();
		
		if($rech != null) {
			while($donnees = $rech->fetch())
				$adherents[] = $donnees;
		}

		API::SendJSON(array(
			'adherents' => $adherents,
		));
	});
}
?>