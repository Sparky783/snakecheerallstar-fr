<?php
use ApiCore\Api;
use System\ToolBox;
use System\Session;
use System\Database;
use Snake\Adherent;
use Snake\Section;

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "member")))
{
	// Retourne une liste d'adhérent en fonction d'une section donnée.
	$app->Post("/adherent_list", function($args) {
		global $router;

		$list = array();
		$session = Session::GetInstance();
		$adherents = Adherent::GetListBySection($args['id_section']);
		
		foreach($adherents as $adherent)
		{
			// Status
			$status = "";

			if (!$adherent->GetPayment()->IsDone() ||
				!$adherent->GetDocIdCard() ||
				!$adherent->GetDocPhoto() ||
				!$adherent->GetDocFFFA() ||
				!$adherent->GetDocSportmut() ||
				!$adherent->GetDocMedicAuth())
				$status = "not_complete";

			// Droits
			$actions = array();

			if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "member")))
				$actions[] = "view";

			if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster", "secretaire")) && $status != "")
				$actions[] = "validate";

			if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster")))
			{
				$actions[] = "surclasser";
				$actions[] = "sousclasser";
				$actions[] = "remove";
			}

			$list[] = array(
				"id" => $adherent->GetId(),
				"firstname" => $adherent->GetFirstname(),
				"lastname" => $adherent->GetLastname(),
				"status" => $status,
				"actions" => $actions,
				"link" => $router->GetURL("adherent-info") . "&id=" . $adherent->GetId()
			);
		}

		API::SendJSON(array(
			"nbAdherents" => 0,
			"list" => $list
		));
	});

	// Retourne le formulaire des éléments en attentes.
	$app->Get("/adherent_validate_form/{id_adherent}", function($args) {
		$adherent = Adherent::GetById($args['id_adherent']);
		$missDocHtml = "";

		if(!$adherent->GetPayment()->IsDone())
		{
			$priceHtml = "";
			switch($adherent->GetPayment()->GetMethod())
			{
				case Payment::$METHODS['Espece']:
					$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - Espèce";
					break;

				case Payment::$METHODS['Cheque']:
					$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - " . $adherent->GetPayment()->GetNbDeadlines() ." Chèques";
					break;

				case Payment::$METHODS['Virement']:
					$priceHtml = $adherent->GetPayment()->GetFinalAmount() . "€ - Virement";
					break;
			}

			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchPayment' class='custom-control-input' type='checkbox' name='payment' />
					<label class='custom-control-label' for='customSwitchPayment'>Paiement (" . $priceHtml . ")</label>
				</div>
			";
		}

		if($adherent->GetTenue()) // Acheté
		{
			if(!true) {
				$missDocHtml .= "
					<div class='custom-control custom-switch'>
						<input id='customSwitchChqBuyUniform' class='custom-control-input' type='checkbox' name='chqBuyUniform' />
						<label class='custom-control-label' for='customSwitchChqBuyUniform'>Chèque d'achat de la tenue (" . $adherent->GetSection()->GetPriceUniform() . "€)</label>
					</div>
				";
			}
		}

		if(!$adherent->GetDocIdCard()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchIdCard' class='custom-control-input' type='checkbox' name='idCard' />
					<label class='custom-control-label' for='customSwitchIdCard'>Pièce d'identité</label>
				</div>
			";
		}

		if(!$adherent->GetDocFFFA()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchFFFA' class='custom-control-input' type='checkbox' name='fffa' />
					<label class='custom-control-label' for='customSwitchFFFA'>Licence FFFA</label>
				</div>
			";
		}

		if(!$adherent->GetDocMedicAuth()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchMedic' class='custom-control-input' type='checkbox' name='medic' />
					<label class='custom-control-label' for='customSwitchMedic'>Autorisation médicale</label>
				</div>
			";
		}

		if(!$adherent->GetDocPhoto()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchPhoto' class='custom-control-input' type='checkbox' name='photo' />
					<label class='custom-control-label' for='customSwitchPhoto'>Photo d'identité</label>
				</div>
			";
		}

		if(!$adherent->GetDocSportmut()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchSportmut' class='custom-control-input' type='checkbox' name='sportmut' />
					<label class='custom-control-label' for='customSwitchSportmut'>Sportmut</label>
				</div>
			";
		}

		API::SendJSON(array(
			'name' => $adherent->GetFirstname() . " " . $adherent->GetLastname(),
			'content' => $missDocHtml
		));
	});

	// Met à jour les éléments attendu pour l'inscription
	$app->Post("/adherent_validate_update", function($args) {
		$data = array();
		if(isset($args['chqBuyUniform'])) $data['chq_buy_uniform'] = ToolBox::StringToBool($args['chqBuyUniform']);
		if(isset($args['chqRentUniform'])) $data['chq_rent_uniform'] = ToolBox::StringToBool($args['chqRentUniform']);
		if(isset($args['chqCleanUniform'])) $data['chq_clean_uniform'] = ToolBox::StringToBool($args['chqCleanUniform']);
		if(isset($args['idCard'])) $data['doc_ID_card'] = ToolBox::StringToBool($args['idCard']);
		if(isset($args['photo'])) $data['doc_photo'] = ToolBox::StringToBool($args['photo']);
		if(isset($args['fffa'])) $data['doc_fffa'] = ToolBox::StringToBool($args['fffa']);
		if(isset($args['sportmut'])) $data['doc_sportmut'] = ToolBox::StringToBool($args['sportmut']);
		if(isset($args['medic'])) $data['doc_medic_auth'] = ToolBox::StringToBool($args['medic']);

		$database = new Database();
		$result = true;
		$result = $result & $database->Update("adherents", "id_adherent", $args['id_adherent'], $data);

		if(isset($args['payment']))
		{
			$rech = $database->Query(
				"SELECT * FROM adherents WHERE id_adherent=:id_adherent",
				array("id_adherent" => $args['id_adherent'])
			);

			if($rech != null)
			{
				$adherent = $rech->fetch();

				$result = $result & $database->Update("payments", "id_payment", $adherent['id_payment'],
					array("is_done" => ToolBox::StringToBool($args['payment']))
				);
			}
		}

		API::SendJSON($result);
	});

	$app->Post("/adherent_export_list", function($args) {
		$session = Session::GetInstance();
		$adherents = null;

		if($args['id_section'] == "all")
			$adherents = Adherent::GetList($session->selectedSaison);
		else
			$adherents = Adherent::GetListBySection($args['id_section']);
		
		if($adherents != null)
		{
			$fileContent = "";
			$delimiter = "";

			if($args['delimiter'] == "pointvirgule")
				$delimiter = ";";
			else
				$delimiter = ",";
				
			$fileContent .= "Nom" . $delimiter . "Prenom" . $delimiter . "Date de naissancce" . $delimiter . "Section\n";

			foreach($adherents as $adherent)
			{
				if($args['delimiter'] == ";")
				{
					foreach($adherent as &$val)
						$val = str_replace(";", "_", $val);
				}
				else
				{
					foreach($adherent as &$val)
						$val = str_replace(",", ".", $val);
				}

				$fileContent .= ToolBox::StripAccents($adherent->GetLastname()) . $delimiter;
				$fileContent .= ToolBox::StripAccents($adherent->GetFirstname()) . $delimiter;
				$fileContent .= $adherent->GetBirthday()->format("d/m/Y") . $delimiter;
				$fileContent .= $adherent->GetSection()->GetName();
				$fileContent .= "\n";
			}

			header('Content-Type: text/csv; charset=ansi');
			echo $fileContent;
			exit;
		}
		else
			API::SendJSON("Erreur");
	});

	$app->Post("/adherent_surclassement", function($args) {
		$session = Session::GetInstance();
		$adherent = Adherent::GetById($args['id']);
		$sections = Section::GetList($session->selectedSaison);

		foreach($sections as $section)
		{
			if($section->GetMinAge() > $adherent->GetSection()->GetMinAge())
			{
				$adherent->SetSection($section);
				$adherent->SaveToDatabase();

				API::SendJSON("L'adhérent à été surclassé en " . $section->GetName());
				return;
			}
		}
		
		API::SendJSON("Impossible de surclasser l'adhérent.");
	});

	$app->Post("/adherent_sousclassement", function($args) {
		$session = Session::GetInstance();
		$adherent = Adherent::GetById($args['id']);
		$sections = Section::GetList($session->selectedSaison);

		for($i = count($sections) - 1; $i > 0; $i--)
		{
			if($sections[$i]->GetMinAge() < $adherent->GetSection()->GetMinAge())
			{
				$adherent->SetSection($sections[$i]);
				$adherent->SaveToDatabase();
				
				API::SendJSON("L'adhérent à été sousclassé en " . $sections[$i]->GetName());
				return;
			}
		}
		
		API::SendJSON("Impossible de sousclasser l'adhérent.");
	});
}
?>