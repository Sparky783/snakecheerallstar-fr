<?php
use ApiCore\Api;
use System\ToolBox;
use System\Session;
use Snake\Section;
use Snake\SnakeTools;

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster")))
{
	$app->post('/apply_options', function($args) {
		$session = Session::getInstance();
		
		if (isset($session->websiteOptions)) {
			$options = unserialize($session->websiteOptions);

			// Récupération des données
			$options->IS_OPEN_INSCRIPTION = $args['open_inscription'];
			$options->INSCRIPTION_MIN_DATE = $args['min_date_inscription'];
			$options->INSCRIPTION_MAX_DATE = $args['max_date_inscription'];

			if ($options->saveToDatabase()) {
				$session->websiteOptions = serialize($options);
			}
		}

		//API::SendJSON($reponse);
	});

	$app->post('/section_list', function($args) {
		$session = Session::getInstance();

		$sections = Section::getList($session->selectedSaison);
		$list = [];
		
		foreach ($sections as $section) {
			$list[] = $section->toArray();
		}

		API::sendJSON([
			'sections' => $list,
			'isCurrentSaison' => ($session->selectedSaison == SnakeTools::getCurrentSaison()),
			'CurrentSaison' => SnakeTools::getCurrentSaison()
		]);
	});

	$app->post('/section_add', function($args) {
		$section = new Section();
		$section->setName($args['name']);
		$section->setSaison(SnakeTools::getCurrentSaison());
		$section->setMaxYear($args['maxYear']);
		$section->setCotisationPrice($args['cotisationPrice']);
		$section->setRentUniformPrice($args['rentUniformPrice']);
		$section->setCleanUniformPrice($args['cleanUniformPrice']);
		$section->setBuyUniformPrice($args['buyUniformPrice']);
		$section->setDepositUniformPrice($args['depositUniformPrice']);
		$section->setNbMaxMembers($args['maxMembers']);
		
		if ($section->SaveToDatabase()) {
			API::sendJSON($section->getId());
		} else {
			API::sendJSON(false);
		}
	});

	$app->post('/section_edit', function($args) {
		$section = Section::GetById($args['idSection']);
		$section->setName($args['name']);
		$section->setMaxYear($args['maxYear']);
		$section->setCotisationPrice($args['cotisationPrice']);
		$section->setRentUniformPrice($args['rentUniformPrice']);
		$section->setCleanUniformPrice($args['cleanUniformPrice']);
		$section->setBuyUniformPrice($args['buyUniformPrice']);
		$section->setDepositUniformPrice($args['depositUniformPrice']);
		$section->setNbMaxMembers($args['maxMembers']);

		API::SendJSON($section->saveToDatabase());
	});

	$app->post('/section_remove', function($args) {
		API::sendJSON(Section::removeFromDatabase($args['idSection']));
	});
}
?>