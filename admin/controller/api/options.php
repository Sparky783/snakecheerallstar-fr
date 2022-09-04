<?php
if(ToolBox::SearchInArray($session->admin_roles, array("admin", "member")))
{
	$app->Post("/apply_options", function($args) {
		include_once(ABSPATH . "model/system/ToolBox.php");
		include_once(ABSPATH . "model/Options.php");

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
		include_once(ABSPATH . "model/snake/Section.php");

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
		include_once(ABSPATH . "model/snake/SnakeTools.php");
		include_once(ABSPATH . "model/snake/Section.php");

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
		include_once(ABSPATH . "model/snake/Section.php");
		
		$section = Section::GetById($args['id_section']);
		$section->SetName($args['name']);
		$section->SetMinAge($args['min_age']);
		$section->SetPriceCotisation($args['price_cotisation']);
		$section->SetPriceUniform($args['price_uniform']);
		$section->SetNbMaxMembers($args['max_members']);

		API::SendJSON($section->SaveToDatabase());
	});

	$app->Post("/sections_remove", function($args) {
		include_once(ABSPATH . "model/snake/Section.php");
		
		API::SendJSON(Section::RemoveFromDatabase($args['id_section']));
	});
}
?>