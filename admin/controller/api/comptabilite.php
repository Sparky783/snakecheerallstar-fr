<?php
use ApiCore\Api;
use System\ToolBox;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'tresorier'])) {
	/*
	$app->Post("/comptabilite_export_list", function($args) {
		$session = Session::GetInstance();
		$database = new Database();
		$adherents = null;

		if($args['id_section'] == "all")
		{
			$adherents = $database->Query(
				"SELECT * FROM adherents JOIN payments ON adherents.id_payment = payments.id_payment JOIN sections ON adherents.id_section = sections.id_section",
			);
		}
		else
		{
			$adherents = $database->Query(
				"SELECT * FROM adherents JOIN payments ON adherents.id_payment = payments.id_payment JOIN sections ON adherents.id_section = sections.id_section WHERE id_adherent=:id_adherent",
				array("id_section" => intval($args['id_section']))
			);
		}
		
		if($adherents != null)
		{
			$fileContent = "";

			if($args['delimiter'] == ";")
				$fileContent .= "Nom;Prénom;Date de naissancce;Section\n";
			else
				$fileContent .= "Nom,Prénom,Date de naissancce,Section\n";

			while($adherent = $adherents->fetch())
			{
				$status = false;
				if ((boolval($adherent['chq_buy_uniform']) || (boolval($adherent['chq_rent_uniform']) && boolval($adherent['chq_clean_uniform']))) &&
					boolval($adherent['doc_ID_card']) &&
					boolval($adherent['doc_photo']) &&
					boolval($adherent['doc_fffa']) &&
					boolval($adherent['doc_sportmut']) &&
					boolval($adherent['doc_medic_auth']) &&
					boolval($adherent['is_done']))
					$status = true;

				if($args['delimiter'] == ";")
				{
					$fileContent .= 
				}
				else
				{
					
				}

				$fileContent .= "\n";
			}
		}
	});
	*/
}
?>