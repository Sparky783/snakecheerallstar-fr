<?php
use ApiCore\Api;
use System\ToolBox;
use System\Session;
use System\Database;
use Snake\Adherent;
use Snake\EPaymentType;
use Snake\Section;

if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
	// Retourne une liste d'adhérent en fonction d'une section donnée.
	$app->post('/adherent_list', function($args) {
		global $router;

		$list = [];
		$session = Session::getInstance();
		$adherents = Adherent::getListBySection((int)$args['id_section']);
		
		foreach ($adherents as $adherent) {
			// Status
			$status = '';

			if (!$adherent->getPayment()->isDone() ||
				!$adherent->getDocIdCard() ||
				!$adherent->getDocPhoto() ||
				!$adherent->getDocFFFA() ||
				!$adherent->getDocSportmut() ||
				!$adherent->getDocMedicAuth())
			{
				$status = 'not_complete';
			}

			// Droits
			$actions = [];

			if (ToolBox::SearchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
				$actions[] = 'view';
			}

			if (ToolBox::SearchInArray($session->admin_roles, ['admin', 'webmaster', 'secretaire']) && $status !== '') {
				$actions[] = 'validate';
			}

			if (ToolBox::SearchInArray($session->admin_roles, ['admin', 'webmaster'])) {
				$actions[] = 'surclasser';
				$actions[] = 'sousclasser';
				$actions[] = 'remove';
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

		API::sendJSON([
			'nbAdherents' => 0,
			'list' => $list
		]);
	});

	// Retourne le formulaire des éléments en attentes.
	$app->get('/adherent_validate_form/{id_adherent}', function($args) {
		$adherent = Adherent::getById((int)$args['id_adherent']);
		$missDocHtml = '';

		if (!$adherent->getPayment()->isDone()) {
			$priceHtml = '';

			switch ($adherent->getPayment()->getMethod()) {
				case EPaymentType::Espece:
					$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - Espèce";
					break;

				case EPaymentType::Cheque:
					$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - " . $adherent->getPayment()->getNbDeadlines() ." Chèques";
					break;

				case EPaymentType::Virement:
					$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - Virement";
					break;
			}

			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchPayment' class='custom-control-input' type='checkbox' name='payment' />
					<label class='custom-control-label' for='customSwitchPayment'>Paiement (" . $priceHtml . ")</label>
				</div>
			";
		}

		if (!$adherent->hasDocIdCard()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchIdCard' class='custom-control-input' type='checkbox' name='idCard' />
					<label class='custom-control-label' for='customSwitchIdCard'>Pièce d'identité</label>
				</div>
			";
		}

		if (!$adherent->hasDocFffa()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchFFFA' class='custom-control-input' type='checkbox' name='fffa' />
					<label class='custom-control-label' for='customSwitchFFFA'>Licence FFFA</label>
				</div>
			";
		}

		if (!$adherent->hasDocMedicAuth()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchMedic' class='custom-control-input' type='checkbox' name='medic' />
					<label class='custom-control-label' for='customSwitchMedic'>Autorisation médicale</label>
				</div>
			";
		}

		if (!$adherent->hasPhoto()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchPhoto' class='custom-control-input' type='checkbox' name='photo' />
					<label class='custom-control-label' for='customSwitchPhoto'>Photo d'identité</label>
				</div>
			";
		}

		if (!$adherent->hasDocSportmut()) {
			$missDocHtml .= "
				<div class='custom-control custom-switch'>
					<input id='customSwitchSportmut' class='custom-control-input' type='checkbox' name='sportmut' />
					<label class='custom-control-label' for='customSwitchSportmut'>Sportmut</label>
				</div>
			";
		}

		API::sendJSON([
			'name' => "{$adherent->getFirstname()} {$adherent->getLastname()}",
			'content' => $missDocHtml
		]);
	});

	// Met à jour les éléments attendu pour l'inscription
	$app->post('/adherent_validate_update', function($args) {
		$data = [];

		if (isset($args['chqBuyUniform'])) {
			$data['chq_buy_uniform'] = ToolBox::stringToBool($args['chqBuyUniform']);
		}

		if (isset($args['chqRentUniform'])) {
			$data['chq_rent_uniform'] = ToolBox::stringToBool($args['chqRentUniform']);
		}

		if (isset($args['chqCleanUniform'])) {
			$data['chq_clean_uniform'] = ToolBox::stringToBool($args['chqCleanUniform']);
		}

		if (isset($args['idCard'])) {
			$data['doc_ID_card'] = ToolBox::stringToBool($args['idCard']);
		}

		if (isset($args['photo'])) {
			$data['doc_photo'] = ToolBox::stringToBool($args['photo']);
		}

		if (isset($args['fffa'])) {
			$data['doc_fffa'] = ToolBox::stringToBool($args['fffa']);
		}

		if (isset($args['sportmut'])) {
			$data['doc_sportmut'] = ToolBox::stringToBool($args['sportmut']);
		}

		if (isset($args['medic'])) {
			$data['doc_medic_auth'] = ToolBox::stringToBool($args['medic']);
		}

		$database = new Database();
		$result = true;
		// TODO: Move this function to Adherent.
		$result = $result & $database->update('adherents', 'id_adherent', (int)$args['id_adherent'], $data);

		if (isset($args['payment'])) {
			$rech = $database->query(
				"SELECT * FROM adherents WHERE id_adherent=:id_adherent",
				['id_adherent' => (int)$args['id_adherent']]
			);

			if ($rech !== null) {
				$adherent = $rech->fetch();

				$result = $result & $database->update('payments', 'id_payment', (int)$adherent['id_payment'],
					['is_done' => ToolBox::stringToBool($args['payment'])]
				);
			}
		}

		API::SendJSON($result);
	});

	$app->post('/adherent_export_list', function($args) {
		$session = Session::GetInstance();
		$adherents = null;

		if ($args['id_section'] === "all") {
			$adherents = Adherent::getList($session->selectedSaison);
		} else {
			$adherents = Adherent::getListBySection((int)$args['id_section']);
		}
		
		if ($adherents !== null) {
			$fileContent = '';
			$delimiter = '';

			if ($args['delimiter'] === 'pointvirgule') {
				$delimiter = ';';
			} else {
				$delimiter = ',';
			}
				
			$fileContent .= "Nom" . $delimiter . "Prenom" . $delimiter . "Date de naissancce" . $delimiter . "Section\n";

			foreach ($adherents as $adherent) {
				if ($args['delimiter'] === ';') {
					foreach ($adherent as &$val) {
						$val = str_replace(';', '_', $val);
					}
				} else {
					foreach ($adherent as &$val) {
						$val = str_replace(',', '.', $val);
					}
				}

				$fileContent .= ToolBox::stripAccents($adherent->getLastname()) . $delimiter;
				$fileContent .= ToolBox::stripAccents($adherent->getFirstname()) . $delimiter;
				$fileContent .= $adherent->getBirthday()->format('d/m/Y') . $delimiter;
				$fileContent .= $adherent->getSection()->getName();
				$fileContent .= '\n';
			}

			header('Content-Type: text/csv; charset=ansi');
			echo $fileContent;
			exit;
		} else {
			API::sendJSON('Erreur');
		}
	});

	$app->post('/adherent_surclassement', function($args) {
		$session = Session::getInstance();
		$adherent = Adherent::getById((int)$args['id']);
		$sections = Section::getList($session->selectedSaison);

		foreach ($sections as $section) {
			if ($section->getMaxYear() < $adherent->getSection()->getMaxYear()) {
				$adherent->setSection($section);
				$adherent->saveToDatabase();

				API::sendJSON("L'adhérent à été surclassé en {$section->getName()}");
				return;
			}
		}
		
		API::sendJSON("Impossible de surclasser l'adhérent.");
	});

	$app->post('/adherent_sousclassement', function($args) {
		$session = Session::getInstance();
		$adherent = Adherent::getById((int)$args['id']);
		$sections = Section::getList($session->selectedSaison);

		for ($i = count($sections) - 1; $i > 0; $i--) {
			if ($sections[$i]->getMaxYear() > $adherent->getSection()->getMaxYear()) {
				$adherent->setSection($sections[$i]);
				$adherent->saveToDatabase();
				
				API::sendJSON("L'adhérent à été sousclassé en {$sections[$i]->GetName()}");
				return;
			}
		}
		
		API::sendJSON("Impossible de sousclasser l'adhérent.");
	});
}
?>