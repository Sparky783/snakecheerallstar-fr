<?php
use ApiCore\Api;
use System\ToolBox;
use System\Session;
use Snake\Section;
use Snake\SnakeTools;

if(ToolBox::SearchInArray($session->admin_roles, array("admin", "webmaster")))
{
	$app->Post("/apply_options", function($args) {
		$session = Session::getInstance();
		
		if(isset($session->websiteOptions))
		{
			$options = unserialize($session->websiteOptions);

			// Récupération des données
			$options->IS_OPEN_INSCRIPTION = $args['open_inscription'];
			$options->INSCRIPTION_MIN_DATE = $args['min_date_inscription'];
			$options->INSCRIPTION_MAX_DATE = $args['max_date_inscription'];

			if($options->SaveToDatabase())
				$session->websiteOptions = serialize($options);
		}

		//API::SendJSON($reponse);
	});

	$app->Post("/section_list", function($args) {
		$session = Session::getInstance();

		$sections = Section::GetList($session->selectedSaison);
		$list = array();
		
		foreach($sections as $section)
		{
			$list[] = array(
				"id_section" => $section->GetId(),
				"name" => $section->GetName(),
				"min_age" => $section->GetMinAge(),
				"price_cotisation" => $section->GetPriceCotisation(),
				"price_uniform" => $section->GetPriceUniform(),
				"max_members" => $section->GetNbMaxMembers()
			);
		}

		API::SendJSON(array(
			"sections" => $list,
			"isCurrentSaison" => ($session->selectedSaison == SnakeTools::GetCurrentSaison()),
			"CurrentSaison" => SnakeTools::GetCurrentSaison()
		));
	});

	$app->Post("/section_add", function($args) {
		$section = new Section();
		$section->SetName($args['name']);
		$section->SetSaison(SnakeTools::GetCurrentSaison());
		$section->SetMinAge($args['min_age']);
		$section->SetPriceCotisation($args['price_cotisation']);
		$section->SetPriceUniform($args['price_uniform']);
		$section->SetNbMaxMembers($args['max_members']);
		
		if($section->SaveToDatabase())
			API::SendJSON($section->GetId());
		else
			API::SendJSON(false);
	});

	$app->Post("/section_edit", function($args) {
		$section = Section::GetById($args['id_section']);
		$section->SetName($args['name']);
		$section->SetMinAge($args['min_age']);
		$section->SetPriceCotisation($args['price_cotisation']);
		$section->SetPriceUniform($args['price_uniform']);
		$section->SetNbMaxMembers($args['max_members']);

		API::SendJSON($section->SaveToDatabase());
	});

	$app->Post("/sections_remove", function($args) {
		API::SendJSON(Section::RemoveFromDatabase($args['id_section']));
	});
}
?>