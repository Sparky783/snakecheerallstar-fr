<?php
use ApiCore\Api;
use System\ToolBox;
use System\Session;
use System\Database;
use Snake\Adherent;
use Snake\EPaymentType;
use Snake\Section;
use Snake\SnakeMailer;
use Snake\SnakeTools;

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
				!$adherent->hasDocIdCard() ||
				!$adherent->hasDocPhoto() ||
				!$adherent->hasDocFFFA() ||
				!$adherent->hasDocSportmut() ||
				($adherent->hasMedicine() && !$adherent->hasDocMedicAuth()))
			{
				$status = 'not_complete';
			}

			// Droits
			$actions = [];

			if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'member'])) {
				$actions[] = 'view';
			}

			if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster', 'secretaire']) && $status !== '') {
				$actions[] = 'validate';
			}

			if (ToolBox::searchInArray($session->admin_roles, ['admin', 'webmaster'])) {
				$actions[] = 'surclasser';
				$actions[] = 'sousclasser';
				$actions[] = 'remove';
			}

			$list[] = [
				'id' => $adherent->getId(),
				'firstname' => $adherent->getFirstname(),
				'lastname' => $adherent->getLastname(),
				'status' => $status,
				'actions' => $actions,
				'link' => $router->getURL('adherent-info') . '&id=' . $adherent->getId()
			];
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
					$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - " . $adherent->getPayment()->getNbDeadlines() . " Chèques";
					break;

				case EPaymentType::Virement:
					$priceHtml = $adherent->getPayment()->getFinalAmount() . "€ - Virement";
					break;
			}

			$missDocHtml .= <<<HTML
				<div class='form-check form-switch'>
					<input id='customSwitchPayment' class='form-check-input' type='checkbox' name='payment' />
					<label class='form-check-label' for='customSwitchPayment'>Paiement ({$priceHtml})</label>
				</div>
				HTML;
		}

		if (!$adherent->hasDocIdCard()) {
			$missDocHtml .= <<<HTML
				<div class='form-check form-switch'>
					<input id='customSwitchIdCard' class='form-check-input' type='checkbox' name='idCard' />
					<label class='form-check-label' for='customSwitchIdCard'>Pièce d'identité</label>
				</div>
				HTML;
		}

		if (!$adherent->hasDocFffa()) {
			$missDocHtml .= <<<HTML
				<div class='form-check form-switch'>
					<input id='customSwitchFFFA' class='form-check-input' type='checkbox' name='fffa' />
					<label class='form-check-label' for='customSwitchFFFA'>Licence FFFA</label>
				</div>
				HTML;
		}

		if ($adherent->hasMedicine() && !$adherent->hasDocMedicAuth()) {
			$missDocHtml .= <<<HTML
				<div class='form-check form-switch'>
					<input id='customSwitchMedic' class='form-check-input' type='checkbox' name='medic' />
					<label class='form-check-label' for='customSwitchMedic'>Autorisation médicale</label>
				</div>
				HTML;
		}

		if (!$adherent->hasDocPhoto()) {
			$missDocHtml .= <<<HTML
				<div class='form-check form-switch'>
					<input id='customSwitchPhoto' class='form-check-input' type='checkbox' name='photo' />
					<label class='form-check-label' for='customSwitchPhoto'>Photo d'identité</label>
				</div>
				HTML;
		}

		if (!$adherent->hasDocSportmut()) {
			$missDocHtml .= <<<HTML
				<div class='form-check form-switch'>
					<input id='customSwitchSportmut' class='form-check-input' type='checkbox' name='sportmut' />
					<label class='form-check-label' for='customSwitchSportmut'>Sportmut</label>
				</div>
				HTML;
		}

		API::sendJSON([
			'name' => "{$adherent->getFirstname()} {$adherent->getLastname()}",
			'content' => $missDocHtml
		]);
	});

	// Met à jour les éléments attendu pour l'inscription
	$app->post('/adherent_validate_update', function($args) {
		$adherent = Adherent::getById((int)$args['id_adherent']);

		if (isset($args['payment']) && ToolBox::stringToBool($args['payment'])) {
			$payment = $adherent->getPayment();
			$payment->setIsDone(true);
			$payment->saveToDatabase();
		}

		if (isset($args['idCard'])) {
			$adherent->setDocIdCard(ToolBox::stringToBool($args['idCard']));
		}
		
		if (isset($args['fffa'])) {
			$adherent->setDocFffa(ToolBox::stringToBool($args['fffa']));
		}

		if (isset($args['medic'])) {
			$adherent->setDocMedicAuth(ToolBox::stringToBool($args['medic']));
		}

		if (isset($args['photo'])) {
			$adherent->setDocPhoto(ToolBox::stringToBool($args['photo']));
		}

		if (isset($args['sportmut'])) {
			$adherent->setDocSportmut(ToolBox::stringToBool($args['sportmut']));
		}

		API::sendJSON($adherent->saveToDatabase());
	});

	$app->post('/adherent_export_list', function($args) {
		$session = Session::getInstance();
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
		$sections = Section::getList($session->selectedSaison);
		$adherent = Adherent::getById((int)$args['id']);
		$payment = $adherent->getPayment();
		$oldPrice = $payment->isDone() ? $payment->getFinalAmount() : 0;

		// Recherche la nouvelle section
		$newSection = null;
		$nbSection = count($sections);

		for ($i = $nbSection - 1; $i >= 0; $i--) {
			if ($sections[$i]->getMaxYear() < $adherent->getSection()->getMaxYear()) {
				$newSection = $sections[$i];
				break;
			}
		}

		if ($newSection !== null) {
			SnakeTools::changeSection($adherent, $newSection);
			$payment = $adherent->getPayment();
			$newPrice = $payment->getFinalAmount();

			if (ToolBox::stringToBool($args['sendEmail'])) {
				foreach ($adherent->getTuteurs() as $tuteur) {
					SnakeMailer::sendSurclassementInformation($adherent, $oldPrice, $newPrice, $tuteur);
					SnakeMailer::sendBill($payment, $tuteur);
				}
			}

			API::sendJSON("L'adhérent à été surclassé en {$newSection->getName()}");
		}
		
		API::sendJSON("Impossible de surclasser l'adhérent.");
	});

	$app->post('/adherent_sousclassement', function($args) {
		$session = Session::getInstance();
		$sections = Section::getList($session->selectedSaison);
		$adherent = Adherent::getById((int)$args['id']);
		$payment = $adherent->getPayment();
		$oldPrice = $payment->isDone() ? $payment->getFinalAmount() : 0;

		// Recherche la nouvelle section
		$newSection = null;
		$nbSection = count($sections);

		for ($i = 0; $i < $nbSection; $i++) {
			if ($sections[$i]->getMaxYear() > $adherent->getSection()->getMaxYear()) {
				$newSection = $sections[$i];
				break;
			}
		}

		if ($newSection !== null) {
			SnakeTools::changeSection($adherent, $newSection);
			$payment = $adherent->getPayment();
			$newPrice = $payment->getFinalAmount();

			
			if (ToolBox::stringToBool($args['sendEmail'])) {
				foreach ($adherent->getTuteurs() as $tuteur) {
					SnakeMailer::sendSousclassementInformation($adherent, $oldPrice, $newPrice, $tuteur);
					SnakeMailer::sendBill($payment, $tuteur);
				}
			}

			API::sendJSON("L'adhérent à été sousclassé en {$newSection->getName()}");
		}
		
		API::sendJSON("Impossible de sousclasser l'adhérent.");
	});
}
?>